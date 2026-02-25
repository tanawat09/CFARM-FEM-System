<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->paginate(10);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
        ]);

        $validated['building'] = '-';
        $validated['floor'] = '-';
        $validated['location_code'] = 'LOC-' . strtoupper(\Illuminate\Support\Str::random(6));

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'เพิ่มพื้นที่สำเร็จ');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['building'] = '-';
        $validated['floor'] = '-';

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'อัปเดตพื้นที่สำเร็จ');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'ลบพื้นที่สำเร็จ');
    }
}
