<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\FireExtinguisher;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        // Get locations that have a floor plan image
        $locations = Location::whereNotNull('floor_plan_image')->get();

        $selectedLocationId = $request->location_id ?? ($locations->first()->id ?? null);
        $selectedLocation = null;
        $extinguishers = [];

        if ($selectedLocationId) {
            $selectedLocation = Location::find($selectedLocationId);
            $extinguishers = FireExtinguisher::where('location_id', $selectedLocationId)->get();
        }

        return view('map.index', compact('locations', 'selectedLocation', 'extinguishers'));
    }

    public function savePin(Request $request)
    {
        $request->validate([
            'extinguisher_id' => 'required|exists:fire_extinguishers,id',
            'map_x' => 'required|numeric',
            'map_y' => 'required|numeric',
        ]);

        $extinguisher = FireExtinguisher::findOrFail($request->extinguisher_id);
        
        // Ensure user can only pin to the location the extinguisher belongs to
        // Or if location changes are allowed here, we'd handle it. 
        // For now, just update coordinates.
        $extinguisher->update([
            'map_x' => $request->map_x,
            'map_y' => $request->map_y,
        ]);

        return response()->json(['success' => true, 'message' => 'บันทึกตำแหน่งสำเร็จ']);
    }

    public function removePin(Request $request)
    {
        $request->validate([
            'extinguisher_id' => 'required|exists:fire_extinguishers,id',
        ]);

        $extinguisher = FireExtinguisher::findOrFail($request->extinguisher_id);
        $extinguisher->update([
            'map_x' => null,
            'map_y' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'ลบตำแหน่งสำเร็จ']);
    }
}
