<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolType;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $query = Tool::with('location');

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('location_id') && $request->location_id != '') {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $tools = $query->latest()->paginate(15);
        $locations = Location::where('is_active', true)->get();
        $toolTypes = ToolType::where('is_active', true)->get();

        return view('tools.index', compact('tools', 'locations', 'toolTypes'));
    }

    public function create(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $toolTypes = ToolType::where('is_active', true)->get();
        return view('tools.create', compact('locations', 'toolTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|exists:tool_types,slug',
            'tool_code' => 'required|unique:tools,tool_code',
            'tool_name' => 'required',
            'location_id' => 'required|exists:locations,id',
        ]);

        $qrCode = Str::uuid()->toString();

        Tool::create([
            'type' => $request->type,
            'tool_code' => $request->tool_code,
            'tool_name' => $request->tool_name,
            'brand' => $request->brand,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'location_id' => $request->location_id,
            'house' => $request->house,
            'zone' => $request->zone,
            'purchase_date' => $request->purchase_date,
            'status' => 'active',
            'qr_code' => $qrCode,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('tools.index')
            ->with('success', 'เพิ่มเครื่องมือเรียบร้อยแล้ว');
    }

    public function show(Tool $tool)
    {
        $tool->load(['location', 'inspections.inspectedBy', 'inspections.inspectionItems']);
        return view('tools.show', compact('tool'));
    }

    public function edit(Tool $tool)
    {
        $locations = Location::where('is_active', true)->get();
        $toolTypes = ToolType::where('is_active', true)->get();
        return view('tools.edit', compact('tool', 'locations', 'toolTypes'));
    }

    public function update(Request $request, Tool $tool)
    {
        $request->validate([
            'tool_code' => 'required|unique:tools,tool_code,' . $tool->id,
            'tool_name' => 'required',
            'location_id' => 'required|exists:locations,id',
        ]);

        $tool->update($request->only([
            'type', 'tool_code', 'tool_name', 'brand', 'model',
            'serial_number', 'location_id', 'house', 'zone',
            'purchase_date', 'status', 'note',
        ]));

        return redirect()->route('tools.show', $tool)
            ->with('success', 'แก้ไขเครื่องมือเรียบร้อยแล้ว');
    }

    public function destroy(Tool $tool)
    {
        $tool->delete();

        return redirect()->route('tools.index')
            ->with('success', 'ลบเครื่องมือเรียบร้อยแล้ว');
    }

    public function scanQr($qr_code)
    {
        $tool = Tool::where('qr_code', $qr_code)->firstOrFail();

        if ($tool->status === 'disposed') {
            return redirect()->route('dashboard')->with('error', 'เครื่องมือนี้ถูกจำหน่ายไปแล้ว ไม่สามารถตรวจได้');
        }

        if ($tool->status === 'under_repair') {
            return redirect()->route('dashboard')->with('error', 'เครื่องมือนี้อยู่ระหว่างการซ่อมบำรุง');
        }

        return view('tools.scan-select', compact('tool'));
    }

    public function printQr(Tool $tool)
    {
        $qrCode = QrCode::size(200)->generate(url('/scan-tool/' . $tool->qr_code));
        return view('tools.qr', compact('tool', 'qrCode'));
    }

    public function bulkQr(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:100',
            'ids.*' => 'required|integer|exists:tools,id',
        ]);

        $tools = Tool::whereIn('id', $request->ids)->with('location')->get();
        $qrCodes = [];

        foreach ($tools as $tool) {
            $qrCodes[$tool->id] = QrCode::size(150)->generate(url('/scan-tool/' . $tool->qr_code));
        }

        return view('tools.bulk-qr', compact('tools', 'qrCodes'));
    }
}
