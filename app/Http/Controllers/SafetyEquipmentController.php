<?php

namespace App\Http\Controllers;

use App\Models\SafetyEquipment;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class SafetyEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'emergency_light');
        
        $query = SafetyEquipment::with('location')
            ->where('type', $type);

        if ($request->has('location_id') && $request->location_id != '') {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $equipments = $query->latest()->paginate(15);
        $locations = Location::where('is_active', true)->get();

        return view('safety-equipment.index', compact('equipments', 'locations', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'emergency_light');
        $locations = Location::where('is_active', true)->get();
        return view('safety-equipment.create', compact('locations', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:emergency_light,eyewash_shower',
            'asset_code' => 'required|unique:safety_equipments,asset_code',
            'location_id' => 'required|exists:locations,id',
        ]);

        // Auto-generate QR code
        $qrCode = Str::uuid()->toString();

        SafetyEquipment::create([
            'type' => $request->type,
            'asset_code' => $request->asset_code,
            'serial_number' => $request->serial_number,
            'brand' => $request->brand,
            'model' => $request->model,
            'location_id' => $request->location_id,
            'house' => $request->house,
            'zone' => $request->zone,
            'install_date' => $request->install_date,
            'battery_replace_date' => $request->battery_replace_date,
            'status' => 'active',
            'qr_code' => $qrCode,
            'note' => $request->note,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('safety-equipment.index', ['type' => $request->type])
            ->with('success', 'เพิ่มอุปกรณ์เรียบร้อยแล้ว');
    }

    public function show(SafetyEquipment $safetyEquipment)
    {
        $safetyEquipment->load(['location', 'inspections.inspectedBy', 'inspections.inspectionItems']);
        return view('safety-equipment.show', compact('safetyEquipment'));
    }

    public function edit(SafetyEquipment $safetyEquipment)
    {
        $locations = Location::where('is_active', true)->get();
        return view('safety-equipment.edit', compact('safetyEquipment', 'locations'));
    }

    public function update(Request $request, SafetyEquipment $safetyEquipment)
    {
        $request->validate([
            'asset_code' => 'required|unique:safety_equipments,asset_code,' . $safetyEquipment->id,
            'location_id' => 'required|exists:locations,id',
        ]);

        $safetyEquipment->update($request->only([
            'asset_code', 'serial_number', 'brand', 'model',
            'location_id', 'house', 'zone', 'install_date',
            'battery_replace_date', 'status', 'note',
        ]));

        return redirect()->route('safety-equipment.show', $safetyEquipment)
            ->with('success', 'แก้ไขอุปกรณ์เรียบร้อยแล้ว');
    }

    public function destroy(SafetyEquipment $safetyEquipment)
    {
        $type = $safetyEquipment->type;
        $safetyEquipment->delete();

        return redirect()->route('safety-equipment.index', ['type' => $type])
            ->with('success', 'ลบอุปกรณ์เรียบร้อยแล้ว');
    }

    public function scanQr($qr_code)
    {
        $equipment = SafetyEquipment::where('qr_code', $qr_code)->firstOrFail();

        if ($equipment->status === 'disposed') {
            return redirect()->route('dashboard')->with('error', 'อุปกรณ์นี้ถูกจำหน่ายไปแล้ว ไม่สามารถตรวจได้');
        }

        if ($equipment->status === 'under_repair') {
            return redirect()->route('dashboard')->with('error', 'อุปกรณ์นี้อยู่ระหว่างการซ่อมบำรุง');
        }

        return redirect()->route('equipment-inspections.create', ['equipment_id' => $equipment->id]);
    }

    public function printQr(SafetyEquipment $safetyEquipment)
    {
        $qrCode = QrCode::size(200)->generate(url('/scan-equipment/' . $safetyEquipment->qr_code));
        return view('safety-equipment.qr', compact('safetyEquipment', 'qrCode'));
    }

    public function bulkQr(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|max:100',
            'ids.*' => 'required|integer|exists:safety_equipments,id',
        ]);

        $equipments = SafetyEquipment::whereIn('id', $request->ids)->with('location')->get();
        $qrCodes = [];

        foreach ($equipments as $eq) {
            $qrCodes[$eq->id] = QrCode::size(150)->generate(url('/scan-equipment/' . $eq->qr_code));
        }

        return view('safety-equipment.bulk-qr', compact('equipments', 'qrCodes'));
    }
}
