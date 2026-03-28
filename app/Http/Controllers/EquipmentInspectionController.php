<?php

namespace App\Http\Controllers;

use App\Models\SafetyEquipment;
use App\Models\EquipmentInspection;
use App\Models\EquipmentInspectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EquipmentInspectionController extends Controller
{
    private function getChecklist(string $type): array
    {
        if ($type === 'emergency_light') {
            return [
                ['code' => 'EL-001', 'category' => 'สภาพภายนอก', 'item' => 'ตัวเครื่องและหลอดไฟไม่แตกหัก ปลั๊กไฟเสียบแน่น'],
                ['code' => 'EL-002', 'category' => 'การทำงาน', 'item' => 'เมื่อกดปุ่ม Test หรือถอดปลั๊ก ไฟต้องสว่างทันที'],
                ['code' => 'EL-003', 'category' => 'สัญญาณไฟ', 'item' => 'ไฟ LED สถานะ (Charge/Ready) แสดงผลปกติ'],
                ['code' => 'EL-004', 'category' => 'ตำแหน่ง', 'item' => 'ทิศทางของหัวโคมส่องสว่างไปยังพื้นที่ใช้งาน'],
            ];
        }

        // eyewash_shower
        return [
            ['code' => 'EW-001', 'category' => 'การเข้าถึง', 'item' => 'ไม่มีสิ่งกีดขวาง'],
            ['code' => 'EW-002', 'category' => 'สภาพอุปกรณ์', 'item' => 'ไม่รั่วซึม'],
            ['code' => 'EW-003', 'category' => 'สภาพอุปกรณ์', 'item' => 'หัวฉีดมีฝาครอบกันฝุ่น'],
            ['code' => 'EW-004', 'category' => 'ความพร้อมเปิดใช้งาน', 'item' => 'น้ำไหลปกติ'],
            ['code' => 'EW-005', 'category' => 'ความพร้อมเปิดใช้งาน', 'item' => 'น้ำแรงดันสม่ำเสมอ และน้ำสะอาดไม่มีตะกอน'],
        ];
    }

    private function validateResults(array $results, array $allowedCodes): void
    {
        $submittedCodes = array_keys($results);
        sort($submittedCodes);
        sort($allowedCodes);

        if ($submittedCodes !== $allowedCodes) {
            throw ValidationException::withMessages([
                'results' => 'ข้อมูลรายการตรวจไม่ถูกต้อง กรุณาโหลดหน้าแบบฟอร์มใหม่แล้วลองอีกครั้ง',
            ]);
        }

        foreach ($results as $code => $result) {
            if (!in_array($result, ['ok', 'not_ok', 'na'], true)) {
                throw ValidationException::withMessages([
                    "results.$code" => 'ค่าผลการตรวจไม่ถูกต้อง',
                ]);
            }
        }
    }

    private function generateInspectionNo(): string
    {
        return 'EQI-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
    }

    public function index(Request $request)
    {
        $type = $request->input('type', 'emergency_light');

        $query = EquipmentInspection::with(['equipment.location', 'inspectedBy'])
            ->whereHas('equipment', function ($q) use ($type) {
                $q->where('type', $type);
            });

        if ($request->has('location_id') && $request->location_id != '') {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $inspections = $query->latest('inspected_at')->paginate(15);
        $locations = \App\Models\Location::where('is_active', true)->get();

        return view('equipment-inspections.index', compact('inspections', 'locations', 'type'));
    }

    public function create(Request $request)
    {
        $equipment = SafetyEquipment::findOrFail($request->equipment_id);

        // Check if already inspected this month
        $alreadyInspected = EquipmentInspection::where('equipment_id', $equipment->id)
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('safety-equipment.show', $equipment)
                ->with('error', 'อุปกรณ์นี้ได้รับการตรวจเช็คแล้วในเดือนนี้');
        }

        $checklist = $this->getChecklist($equipment->type);

        return view('equipment-inspections.create', compact('equipment', 'checklist'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:safety_equipments,id',
            'results' => 'required|array',
            'remark' => 'nullable|string|max:1000',
        ]);

        $equipment = SafetyEquipment::findOrFail($validated['equipment_id']);
        $checklist = collect($this->getChecklist($equipment->type))->keyBy('code');
        $this->validateResults($validated['results'], $checklist->keys()->all());

        // Prevent duplicate inspection in the same month
        $alreadyInspected = EquipmentInspection::where('equipment_id', $validated['equipment_id'])
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();

        if ($alreadyInspected) {
            return redirect()->route('safety-equipment.index')
                ->with('error', 'อุปกรณ์นี้ได้รับการตรวจเช็คแล้วในเดือนนี้ ไม่สามารถบันทึกซ้ำได้');
        }

        $results = $validated['results'];
        $overallResult = in_array('not_ok', array_values($results)) ? 'fail' : 'pass';

        $equipment = DB::transaction(function () use ($validated, $results, $checklist, $overallResult, $equipment) {
            $inspection = EquipmentInspection::create([
                'inspection_no' => $this->generateInspectionNo(),
                'equipment_id' => $validated['equipment_id'],
                'inspected_by' => auth()->id(),
                'inspected_at' => now(),
                'overall_result' => $overallResult,
                'remark' => $validated['remark'] ?? null,
                'next_inspection_date' => now()->addDays(30),
            ]);

            $itemsToInsert = [];
            $now = now();
            foreach ($results as $code => $result) {
                $item = $checklist->get($code);
                $itemsToInsert[] = [
                    'inspection_id' => $inspection->id,
                    'item_code' => $code,
                    'item_name' => $item['item'],
                    'category' => $item['category'],
                    'result' => $result,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            EquipmentInspectionItem::insert($itemsToInsert);

            $equipment->next_inspection_date = now()->addDays(30);
            if ($overallResult === 'fail') {
                $equipment->status = 'under_repair';
            }
            $equipment->save();

            return $equipment;
        });

        return redirect()->route('safety-equipment.show', $equipment)
            ->with('success', 'บันทึกการตรวจเช็คเรียบร้อยแล้ว');
    }

    public function show(EquipmentInspection $equipmentInspection)
    {
        $equipmentInspection->load(['equipment.location', 'inspectedBy', 'inspectionItems']);
        return view('equipment-inspections.show', compact('equipmentInspection'));
    }
}
