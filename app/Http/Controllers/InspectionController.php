<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\FireExtinguisher;
use App\Models\InspectionItem;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
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
        
        // This would typically load the standard checklist from DB or config
        $checklist = [
            ['code' => 'CHK-001', 'category' => 'ความดัน', 'item' => 'เข็มความดันอยู่ในเกณฑ์มาตรฐาน (สีเขียว)'],
            ['code' => 'CHK-002', 'category' => 'สายและหัวฉีด', 'item' => 'สายฉีดไม่แตกหรือรั่ว'],
            ['code' => 'CHK-004', 'category' => 'ตัวถัง', 'item' => 'ตัวถังไม่มีสนิมหรือบุบแตก'],
            ['code' => 'CHK-008', 'category' => 'ความปลอดภัยในการใช้งาน', 'item' => 'ไม่มีสิ่งกีดขวางการเข้าถึง'],
            ['code' => 'CHK-012', 'category' => 'ซีลและสลักนิรภัย', 'item' => 'สลักนิรภัย (Pin) ครบและปลอดภัย'],
        ];

        return view('inspections.create', compact('extinguisher', 'checklist'));
    }

    public function store(Request $request)
    {
        // Add robust validation as needed
        $request->validate([
            'extinguisher_id' => 'required|exists:fire_extinguishers,id',
            'results' => 'required|array',
        ]);
        
        // Check if already inspected this month to prevent bypassing via direct POST
        $alreadyInspected = Inspection::where('extinguisher_id', $request->extinguisher_id)
            ->whereMonth('inspected_at', now()->month)
            ->whereYear('inspected_at', now()->year)
            ->exists();
            
        if ($alreadyInspected) {
            return redirect()->route('dashboard')->with('error', 'ถังดับเพลิงนี้ได้รับการตรวจเช็คแล้วในเดือนนี้ ไม่สามารถบันทึกซ้ำได้');
        }
        
        $results = $request->input('results'); // array of item_code => result
        $overallResult = in_array('not_ok', array_values($results)) ? 'fail' : 'pass';

        $inspection = Inspection::create([
            'inspection_no' => 'INS-' . date('Ym') . '-' . str_pad(Inspection::count() + 1, 4, '0', STR_PAD_LEFT),
            'extinguisher_id' => $request->extinguisher_id,
            'inspected_by' => auth()->id() ?? 1,
            'inspected_at' => now(),
            'overall_result' => $overallResult,
            'remark' => $request->remark,
            'next_inspection_date' => now()->addDays(30),
            'is_draft' => false,
            'weather_condition' => $request->weather_condition,
        ]);

        $itemsToInsert = [];
        $now = now();
        foreach ($results as $code => $result) {
            $itemsToInsert[] = [
                'inspection_id' => $inspection->id,
                'item_code' => $code,
                'item_name' => collect($request->items)->firstWhere('code', $code)['item'] ?? 'Unknown',
                'category' => 'General',
                'result' => $result,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        InspectionItem::insert($itemsToInsert);
        
        $extinguisher = FireExtinguisher::find($request->extinguisher_id);
        $extinguisher->next_inspection_date = now()->addDays(30);
        
        if ($overallResult === 'fail') {
            $extinguisher->status = 'damage'; // Or under_repair depending on flow map
            // Here you'd likely trigger an event or queue job to create a repair log automatically 
            // and notify safety officers etc.
        }
        
        $extinguisher->save();

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
