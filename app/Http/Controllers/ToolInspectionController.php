<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolInspection;
use App\Models\ToolInspectionItem;
use App\Models\ToolType;
use App\Models\ToolChecklistItem;
use Illuminate\Http\Request;

class ToolInspectionController extends Controller
{
    private function getChecklist(string $type): array
    {
        $toolType = ToolType::where('slug', $type)->first();
        if (!$toolType) {
            return [];
        }

        return $toolType->activeChecklistItems->map(function ($item) {
            return [
                'code' => $item->item_code,
                'category' => $item->category,
                'item' => $item->item_name,
            ];
        })->toArray();
    }

    public function index(Request $request)
    {
        $type = $request->input('type', '');
        $inspectionType = $request->input('inspection_type', 'monthly');

        $query = ToolInspection::with(['tool.location', 'inspectedBy'])
            ->where('inspection_type', $inspectionType);

        if ($type != '') {
            $query->whereHas('tool', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($request->has('location_id') && $request->location_id != '') {
            $query->whereHas('tool', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $inspections = $query->latest('inspected_at')->paginate(15);
        $locations = \App\Models\Location::where('is_active', true)->get();
        $toolTypes = ToolType::where('is_active', true)->get();

        return view('tool-inspections.index', compact('inspections', 'locations', 'type', 'inspectionType', 'toolTypes'));
    }

    public function create(Request $request)
    {
        $tool = Tool::findOrFail($request->tool_id);
        
        if (!$request->has('inspection_type')) {
            if ($tool->status === 'disposed') {
                return redirect()->route('tools.index')->with('error', 'เครื่องมือนี้ถูกจำหน่ายไปแล้ว ไม่สามารถตรวจได้');
            }
            if ($tool->status === 'under_repair') {
                return redirect()->route('tools.index')->with('error', 'เครื่องมือนี้อยู่ระหว่างการซ่อมบำรุง');
            }
            return view('tools.scan-select', compact('tool'));
        }
        
        $inspectionType = $request->input('inspection_type');

        $checklist = $this->getChecklist($tool->type);

        return view('tool-inspections.create', compact('tool', 'checklist', 'inspectionType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'inspection_type' => 'required|in:monthly,pre_work',
            'results' => 'required|array',
        ]);

        $results = $request->input('results');
        $overallResult = in_array('not_ok', array_values($results)) ? 'fail' : 'pass';

        $prefix = $request->inspection_type === 'pre_work' ? 'TLW' : 'TLI';
        
        $inspection = ToolInspection::create([
            'inspection_no' => $prefix . '-' . date('Ym') . '-' . str_pad(ToolInspection::count() + 1, 4, '0', STR_PAD_LEFT),
            'tool_id' => $request->tool_id,
            'inspection_type' => $request->inspection_type,
            'inspected_by' => auth()->id(),
            'inspected_at' => now(),
            'overall_result' => $overallResult,
            'remark' => $request->remark,
            'next_inspection_date' => $request->inspection_type === 'monthly' ? now()->addDays(30) : null,
        ]);

        // Mass insert inspection items
        $itemsToInsert = [];
        $now = now();
        foreach ($results as $code => $result) {
            $itemsToInsert[] = [
                'inspection_id' => $inspection->id,
                'item_code' => $code,
                'item_name' => collect($request->items)->firstWhere('code', $code)['item'] ?? 'Unknown',
                'category' => collect($request->items)->firstWhere('code', $code)['category'] ?? 'General',
                'result' => $result,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        ToolInspectionItem::insert($itemsToInsert);

        // Only update tool's next inspection date if it's a monthly check
        $tool = Tool::find($request->tool_id);
        
        if ($request->inspection_type === 'monthly') {
            $tool->next_inspection_date = now()->addDays(30);
        }
        
        if ($overallResult === 'fail') {
            $tool->status = 'under_repair';
        }
        $tool->save();

        return redirect()->route('tools.show', $tool)
            ->with('success', 'บันทึกการตรวจสอบเรียบร้อยแล้ว');
    }

    public function show(ToolInspection $toolInspection)
    {
        $toolInspection->load(['tool.location', 'inspectedBy', 'inspectionItems']);
        return view('tool-inspections.show', compact('toolInspection'));
    }
}
