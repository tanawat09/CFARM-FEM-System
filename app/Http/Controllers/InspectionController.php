<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\FireExtinguisher;
use App\Models\InspectionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InspectionController extends Controller
{
    private function getChecklist(): array
    {
        return [
            ['code' => 'CHK-001', 'category' => 'ความดัน', 'item' => 'เข็มความดันอยู่ในเกณฑ์มาตรฐาน (สีเขียว)'],
            ['code' => 'CHK-002', 'category' => 'สายและหัวฉีด', 'item' => 'สายฉีดไม่แตกหรือรั่ว'],
            ['code' => 'CHK-004', 'category' => 'ตัวถัง', 'item' => 'ตัวถังไม่มีสนิมหรือบุบแตก'],
            ['code' => 'CHK-008', 'category' => 'ความปลอดภัยในการใช้งาน', 'item' => 'ไม่มีสิ่งกีดขวางการเข้าถึง'],
            ['code' => 'CHK-012', 'category' => 'ซีลและสลักนิรภัย', 'item' => 'สลักนิรภัย (Pin) ครบและปลอดภัย'],
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
        return 'INS-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
    }

    public function index(Request $request)
    {
        $query = Inspection::with(['fireExtinguisher.location', 'inspectedBy']);

        if ($request->has('location_id') && $request->location_id != '') {
            $query->whereHas('fireExtinguisher', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $inspections = $query->latest('inspected_at')->paginate(15);
        $locations = \App\Models\Location::where('is_active', true)->get();

        return view('inspections.index', compact('inspections', 'locations'));
    }

    public function scanQr($qr_code)
    {
        $extinguisher = FireExtinguisher::where('qr_code', $qr_code)->firstOrFail();
        
        if ($extinguisher->status === 'disposed') {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้ถูกจำหน่ายไปแล้ว ไม่สามารถตรวจได้');
        }

        if (\Carbon\Carbon::parse($extinguisher->expire_date)->isBefore(now())) {
             return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้หมดอายุแล้ว ห้ามตรวจ');
        }

        if ($extinguisher->status === 'under_repair') {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้อยู่ระหว่างการซ่อมแซม');
        }

        // Check if already inspected this month
        $alreadyInspected = Inspection::where('extinguisher_id', $extinguisher->id)
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();
            
        if ($alreadyInspected) {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้ได้รับการตรวจเช็คแล้วในเดือนนี้ (1 ถังตรวจได้ 1 ครั้ง/เดือน)');
        }

        return redirect()->route('inspections.create', ['extinguisher_id' => $extinguisher->id]);
    }

    public function create(Request $request)
    {
        if (!$request->has('extinguisher_id')) {
            $extinguishers = FireExtinguisher::where('status', '!=', 'disposed')->get();
            return view('inspections.select_extinguisher', compact('extinguishers'));
        }

        $extinguisher = FireExtinguisher::findOrFail($request->extinguisher_id);
        
        // Check if already inspected this month
        $alreadyInspected = Inspection::where('extinguisher_id', $extinguisher->id)
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();
            
        if ($alreadyInspected) {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้ได้รับการตรวจเช็คแล้วในเดือนนี้ (1 ถังตรวจได้ 1 ครั้ง/เดือน)');
        }

        $checklist = $this->getChecklist();

        return view('inspections.create', compact('extinguisher', 'checklist'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'extinguisher_id' => 'required|exists:fire_extinguishers,id',
            'results' => 'required|array',
            'remark' => 'nullable|string|max:1000',
            'weather_condition' => 'nullable|string|max:255',
        ]);

        $checklist = collect($this->getChecklist())->keyBy('code');
        $this->validateResults($validated['results'], $checklist->keys()->all());

        // Check if already inspected this month to prevent bypassing via direct POST
        $alreadyInspected = Inspection::where('extinguisher_id', $validated['extinguisher_id'])
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();
            
        if ($alreadyInspected) {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้ได้รับการตรวจเช็คแล้วในเดือนนี้ ไม่สามารถบันทึกซ้ำได้');
        }

        $results = $validated['results'];
        $overallResult = in_array('not_ok', array_values($results)) ? 'fail' : 'pass';

        $extinguisher = DB::transaction(function () use ($validated, $results, $checklist, $overallResult) {
            $inspection = Inspection::create([
                'inspection_no' => $this->generateInspectionNo(),
                'extinguisher_id' => $validated['extinguisher_id'],
                'inspected_by' => auth()->id(),
                'inspected_at' => now(),
                'overall_result' => $overallResult,
                'remark' => $validated['remark'] ?? null,
                'next_inspection_date' => now()->addDays(30),
                'is_draft' => false,
                'weather_condition' => $validated['weather_condition'] ?? null,
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
            InspectionItem::insert($itemsToInsert);

            $extinguisher = FireExtinguisher::findOrFail($validated['extinguisher_id']);
            $extinguisher->next_inspection_date = now()->addDays(30);

            if ($overallResult === 'fail') {
                $extinguisher->status = 'damage';
            }

            $extinguisher->save();

            return $extinguisher;
        });

        if ($overallResult === 'fail') {
             return redirect()->route('repair-logs.create', ['extinguisher_id' => $extinguisher->id])
                 ->with('warning', 'ถังดับเพลิงไม่ผ่านการตรวจ กรุณาสร้างใบบันทึกการซ่อม');
        }

        return redirect()->route('extinguishers.show', $extinguisher->id)->with('success', 'บันทึกการตรวจเรียบร้อยแล้ว');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['fireExtinguisher', 'inspectedBy', 'inspectionItems', 'photos']);
        return view('inspections.show', compact('inspection'));
    }

    public function destroy(Inspection $inspection)
    {
        // Delete related items
        $inspection->inspectionItems()->delete();
        
        // Soft delete the inspection record itself
        $inspection->delete();

        return redirect()->route('inspections.index')->with('success', 'ลบประวัติการตรวจเช็คเรียบร้อยแล้ว');
    }

    public function saveDraft(Request $request)
    {
        // Implementation for auto save draft
        return response()->json(['status' => 'success']);
    }

    public function loadDraft($id)
    {
        // Implementation for loading draft
        return response()->json(['status' => 'success']);
    }
}
