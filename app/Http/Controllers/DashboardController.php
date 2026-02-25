<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireExtinguisher;
use App\Models\Inspection;
use App\Models\RepairLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLocation = $request->input('location_name');

        $extinguishersQuery = FireExtinguisher::query();
        
        if ($selectedLocation) {
            $extinguishersQuery->whereHas('location', function ($q) use ($selectedLocation) {
                $q->where('location_name', $selectedLocation);
            });
        }

        $totalExtinguishers = (clone $extinguishersQuery)->count();
        $activeExtinguishers = (clone $extinguishersQuery)->where('status', 'active')->count();
        $damageExtinguishers = (clone $extinguishersQuery)->whereIn('status', ['damage', 'under_repair'])->count();
        $disposedExtinguishers = (clone $extinguishersQuery)->where('status', 'disposed')->count();

        // แจ้งเตือนถังที่ใกล้จะหมดอายุ
        $expireSoonCount = (clone $extinguishersQuery)->where('expire_date', '<=', now()->addDays(30))
            ->where('expire_date', '>=', now())
            ->where('status', '!=', 'disposed')
            ->count();
            
        $expiredCount = (clone $extinguishersQuery)->where('expire_date', '<', now())
            ->where('status', '!=', 'disposed')
            ->count();

        $recentInspectionsQuery = Inspection::with(['fireExtinguisher', 'inspectedBy']);
        
        if ($selectedLocation) {
            $recentInspectionsQuery->whereHas('fireExtinguisher.location', function ($q) use ($selectedLocation) {
                $q->where('location_name', $selectedLocation);
            });
        }
        
        $recentInspections = $recentInspectionsQuery->latest('inspected_at')
            ->take(10)
            ->get();
            
        $locationNames = \App\Models\Location::select('location_name')->distinct()->whereNotNull('location_name')->pluck('location_name');

        // 12-months statistics
        $months = [];
        $passedCounts = [];
        $failedCounts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonthsNoOverflow($i);
            $months[] = $date->format('M y'); // e.g., Feb 26
            
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $baseQuery = Inspection::whereBetween('inspected_at', [$monthStart, $monthEnd]);

            if ($selectedLocation) {
                $baseQuery->whereHas('fireExtinguisher.location', function ($q) use ($selectedLocation) {
                    $q->where('location_name', $selectedLocation);
                });
            }

            // Fetch the records for this month to count them safely in PHP
            $inspections = $baseQuery->get(['overall_result']);

            $passedCounts[] = $inspections->where('overall_result', 'pass')->count();
            $failedCounts[] = $inspections->whereIn('overall_result', ['fail', 'Fail', 'FAIL'])->count();
        }
        
        $chartData = [
            'labels' => $months,
            'passed' => $passedCounts,
            'failed' => $failedCounts,
        ];

        return view('dashboard.index', compact(
            'totalExtinguishers',
            'activeExtinguishers',
            'damageExtinguishers',
            'disposedExtinguishers',
            'expireSoonCount',
            'expiredCount',
            'recentInspections',
            'locationNames',
            'selectedLocation',
            'chartData'
        ));
    }
}
