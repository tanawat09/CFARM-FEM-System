<?php

namespace App\Http\Controllers;

use App\Models\FireExtinguisher;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class FireExtinguisherController extends Controller
{
    public function index(Request $request)
    {
        $extinguishers = FireExtinguisher::with('location')
            ->when($request->search, function($query) use ($request) {
                $query->where('asset_code', 'like', "%{$request->search}%")
                      ->orWhere('serial_number', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15);
            
        return view('extinguishers.index', compact('extinguishers'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('extinguishers.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|unique:fire_extinguishers',
            'type' => 'required|in:CO2,Dry_Chemical,Foam,Water,Clean_Agent',
            'size' => 'required|numeric',
            'size_unit' => 'required|in:kg,lbs',
            'brand' => 'required|string',
            'model' => 'required|string',
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
        return view('extinguishers.edit', compact('extinguisher', 'locations'));
    }

    public function update(Request $request, FireExtinguisher $extinguisher)
    {
        $validated = $request->validate([
            'serial_number' => 'required|unique:fire_extinguishers,serial_number,' . $extinguisher->id,
            'type' => 'required|in:CO2,Dry_Chemical,Foam,Water,Clean_Agent',
            'size' => 'required|numeric',
            'size_unit' => 'required|in:kg,lbs',
            'brand' => 'required|string',
            'model' => 'required|string',
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
}
