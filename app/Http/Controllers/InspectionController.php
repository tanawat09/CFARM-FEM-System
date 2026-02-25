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

        return redirect()->route('inspections.create', ['extinguisher_id' => $extinguisher->id]);
    }

    public function create(Request $request)
    {
        if (!$request->has('extinguisher_id')) {
            $extinguishers = FireExtinguisher::where('status', '!=', 'disposed')->get();
            return view('inspections.select_extinguisher', compact('extinguishers'));
        }

        $extinguisher = FireExtinguisher::findOrFail($request->extinguisher_id);
        // This would typically load the standard checklist from DB or config
        $checklist = [
            ['code' => 'CHK-001', 'category' => 'ความดัน', 'item' => 'เข็มความดันอยู่ในเกณฑ์มาตรฐาน (สีเขียว)'],
            ['code' => 'CHK-002', 'category' => 'สายและหัวฉีด', 'item' => 'สายฉีดไม่แตกหรือรั่ว'],
            ['code' => 'CHK-003', 'category' => 'สายและหัวฉีด', 'item' => 'หัวฉีดไม่อุดตันและอยู่ในสภาพดี'],
            ['code' => 'CHK-004', 'category' => 'ตัวถัง', 'item' => 'ตัวถังไม่มีสนิมหรือบุบแตก'],
            ['code' => 'CHK-005', 'category' => 'ตัวถัง', 'item' => 'ตัวถังสะอาด ไม่มีสิ่งสกปรกสะสม'],
            ['code' => 'CHK-006', 'category' => 'ป้ายและเอกสาร', 'item' => 'ป้ายฉลากชัดเจน อ่านได้ครบถ้วน'],
            ['code' => 'CHK-007', 'category' => 'ป้ายและเอกสาร', 'item' => 'วันหมดอายุไม่เกินกำหนด'],
            ['code' => 'CHK-008', 'category' => 'ความปลอดภัยในการใช้งาน', 'item' => 'ไม่มีสิ่งกีดขวางการเข้าถึง'],
            ['code' => 'CHK-009', 'category' => 'ความปลอดภัยในการใช้งาน', 'item' => 'ติดตั้งในตำแหน่งที่มองเห็นชัดเจน'],
            ['code' => 'CHK-010', 'category' => 'ความปลอดภัยในการใช้งาน', 'item' => 'แขวนหรือวางในความสูงที่เหมาะสม'],
            ['code' => 'CHK-011', 'category' => 'ซีลและสลักนิรภัย', 'item' => 'ซีลนิรภัยยังสมบูรณ์ ไม่ถูกดึงออก'],
            ['code' => 'CHK-012', 'category' => 'ซีลและสลักนิรภัย', 'item' => 'สลักนิรภัย (Pin) ครบและปลอดภัย'],
            ['code' => 'CHK-013', 'category' => 'การติดตั้ง', 'item' => 'ขาแขวนหรือฐานรองแข็งแรง'],
            ['code' => 'CHK-014', 'category' => 'การติดตั้ง', 'item' => 'ป้าย QR Code ชัดเจน สแกนได้'],
        ];

        return view('inspections.create', compact('extinguisher', 'checklist'));
    }

    public function store(Request $request)
    {
        // Add robust validation as needed
        
        $results = $request->input('results'); // array of item_code => result
        $overallResult = 'pass';
        
        foreach ($results as $result) {
            if ($result === 'not_ok') {
                $overallResult = 'fail';
                break;
            }
        }

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

        foreach ($results as $code => $result) {
            InspectionItem::create([
                'inspection_id' => $inspection->id,
                'item_code' => $code,
                'item_name' => collect($request->items)->firstWhere('code', $code)['item'] ?? 'Unknown', // Simplified
                'category' => 'General', // Simplified
                'result' => $result,
            ]);
        }
        
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
