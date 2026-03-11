<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $validated['building'] = '-';
        $validated['floor'] = '-';
        $validated['location_code'] = 'LOC-' . strtoupper(\Illuminate\Support\Str::random(6));

        if ($request->hasFile('floor_plan_image')) {
            $path = $request->file('floor_plan_image')->store('locations', 'public');
            $validated['floor_plan_image'] = $path;
        }

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
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $validated['building'] = '-';
        $validated['floor'] = '-';

        if ($request->hasFile('floor_plan_image')) {
            // Delete old image if exists
            if ($location->floor_plan_image) {
                Storage::disk('public')->delete($location->floor_plan_image);
            }
            $path = $request->file('floor_plan_image')->store('locations', 'public');
            $validated['floor_plan_image'] = $path;
        }

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'อัปเดตพื้นที่สำเร็จ');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'ลบพื้นที่สำเร็จ');
    }
}
