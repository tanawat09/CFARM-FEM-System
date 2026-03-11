<?php

namespace App\Http\Controllers;

use App\Models\FireExtinguisher;
use App\Models\Location;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class FireExtinguisherController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $extinguishers = FireExtinguisher::with('location')
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('asset_code', 'like', "%{$request->search}%")
                      ->orWhere('serial_number', 'like', "%{$request->search}%");
                });
            })
            ->when($request->location_id, function($query) use ($request) {
                $query->where('location_id', $request->location_id);
            })
            ->latest()
            ->paginate(30); // Use 30 instead of 15 to make bulk selecting easier per page
            
        return view('extinguishers.index', compact('extinguishers', 'locations'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        $typesSetting = SystemSetting::where('key', 'extinguisher_types')->value('value') ?? 'CO2,Dry_Chemical,Foam,Water,Clean_Agent';
        $types = array_filter(array_map('trim', explode(',', $typesSetting)));
        return view('extinguishers.create', compact('locations', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|unique:fire_extinguishers',
            'type' => 'required|string',
            'size' => 'required|numeric',
            'size_unit' => 'required|in:kg,lbs',
            'brand' => 'required|string',
            'manufacture_date' => 'required|date',
            'install_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'house' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'status' => 'required|in:active,damage,disposed,under_repair',
        ]);

        $validated['asset_code'] = 'FE-' . strtoupper(Str::random(8));
        $validated['qr_code'] = Str::uuid()->toString();
        $validated['created_by'] = auth()->id() ?? 1; // 1 for fallback if not logged in temp
        $validated['expire_date'] = \Carbon\Carbon::parse($validated['manufacture_date'])->addYears(5);
        $validated['next_refill_date'] = \Carbon\Carbon::parse($validated['install_date'])->addMonths(6);
        $validated['model'] = '-'; // set default value instead of empty null

        FireExtinguisher::create($validated);

        return redirect()->route('extinguishers.index')->with('success', 'เพิ่มถังดับเพลิงสำเร็จ');
    }

    public function show(FireExtinguisher $extinguisher)
    {
        $extinguisher->load(['location', 'inspections' => function($q) {
            $q->latest()->take(5);
        }, 'repairLogs']);
        
        return view('extinguishers.show', compact('extinguisher'));
    }

    public function edit(FireExtinguisher $extinguisher)
    {
        $locations = Location::where('is_active', true)->get();
        $typesSetting = SystemSetting::where('key', 'extinguisher_types')->value('value') ?? 'CO2,Dry_Chemical,Foam,Water,Clean_Agent';
        $types = array_filter(array_map('trim', explode(',', $typesSetting)));
        return view('extinguishers.edit', compact('extinguisher', 'locations', 'types'));
    }

    public function update(Request $request, FireExtinguisher $extinguisher)
    {
        $validated = $request->validate([
            'serial_number' => 'required|unique:fire_extinguishers,serial_number,' . $extinguisher->id,
            'type' => 'required|string',
            'size' => 'required|numeric',
            'size_unit' => 'required|in:kg,lbs',
            'brand' => 'required|string',
            'install_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'house' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'status' => 'required|in:active,damage,disposed,under_repair',
        ]);

        $extinguisher->update($validated);

        return redirect()->route('extinguishers.index')->with('success', 'อัปเดตข้อมูลถังดับเพลิงสำเร็จ');
    }

    public function destroy(FireExtinguisher $extinguisher)
    {
        $extinguisher->delete(); // Soft delete
        return redirect()->route('extinguishers.index')->with('success', 'ลบถังดับเพลิงสำเร็จ');
    }

    public function printQr(FireExtinguisher $extinguisher)
    {
        // Generates an SVG format QR string
        $qrCode = QrCode::size(200)->generate(url('/scan/' . $extinguisher->qr_code));
        return view('extinguishers.qr', compact('extinguisher', 'qrCode'));
    }

    public function bulkQr(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:fire_extinguishers,id'
        ]);

        $extinguishers = FireExtinguisher::whereIn('id', $request->ids)->with('location')->get();
        $qrCodes = [];

        foreach ($extinguishers as $ext) {
            $qrCodes[$ext->id] = QrCode::size(150)->generate(url('/scan/' . $ext->qr_code));
        }

        return view('extinguishers.bulk-qr', compact('extinguishers', 'qrCodes'));
    }
}
