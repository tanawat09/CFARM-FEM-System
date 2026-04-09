<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireExtinguisher;
use App\Models\Inspection;
use App\Models\RepairLog;
use App\Models\SafetyEquipment;
use App\Models\EquipmentInspection;
use App\Models\Tool;
use App\Models\ToolInspection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLocation = $request->input('location_name');
        $expireYears = FireExtinguisher::getConfiguredExpireYears();
        $warningDaysBefore = FireExtinguisher::getWarningDaysBefore();

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
        $expireSoonCount = (clone $extinguishersQuery)
            ->expiringSoonByCurrentSetting($expireYears, $warningDaysBefore)
            ->where('status', '!=', 'disposed')
            ->count();
            
        $expiredCount = (clone $extinguishersQuery)
            ->expiredByCurrentSetting($expireYears)
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

        // Safety Equipment Stats
        $elQuery = SafetyEquipment::where('type', 'emergency_light');
        $ewQuery = SafetyEquipment::where('type', 'eyewash_shower');

        if ($selectedLocation) {
            $elQuery->whereHas('location', fn($q) => $q->where('location_name', $selectedLocation));
            $ewQuery->whereHas('location', fn($q) => $q->where('location_name', $selectedLocation));
        }

        $elTotal = (clone $elQuery)->count();
        $elActive = (clone $elQuery)->where('status', 'active')->count();
        $elDamage = (clone $elQuery)->whereIn('status', ['under_repair', 'inactive'])->count();
        $elInspectedThisMonth = EquipmentInspection::whereHas('equipment', function($q) use ($selectedLocation) {
            $q->where('type', 'emergency_light');
            if ($selectedLocation) {
                $q->whereHas('location', fn($q2) => $q2->where('location_name', $selectedLocation));
            }
        })->whereMonth('inspected_at', now()->month)->whereYear('inspected_at', now()->year)->count();

        $ewTotal = (clone $ewQuery)->count();
        $ewActive = (clone $ewQuery)->where('status', 'active')->count();
        $ewDamage = (clone $ewQuery)->whereIn('status', ['under_repair', 'inactive'])->count();
        $ewInspectedThisMonth = EquipmentInspection::whereHas('equipment', function($q) use ($selectedLocation) {
            $q->where('type', 'eyewash_shower');
            if ($selectedLocation) {
                $q->whereHas('location', fn($q2) => $q2->where('location_name', $selectedLocation));
            }
        })->whereMonth('inspected_at', now()->month)->whereYear('inspected_at', now()->year)->count();

        // Tools Stats
        $toolsQuery = Tool::query();
        if ($selectedLocation) {
            $toolsQuery->whereHas('location', fn($q) => $q->where('location_name', $selectedLocation));
        }

        $toolTotal = (clone $toolsQuery)->where('status', '!=', 'disposed')->count();
        $toolActive = (clone $toolsQuery)->where('status', 'active')->count();
        $toolDamage = (clone $toolsQuery)->whereIn('status', ['under_repair', 'inactive'])->count();
        $toolInspectedThisMonth = ToolInspection::whereHas('tool', function($q) use ($selectedLocation) {
            if ($selectedLocation) {
                $q->whereHas('location', fn($q2) => $q2->where('location_name', $selectedLocation));
            }
        })->where('inspection_type', 'monthly')->whereMonth('inspected_at', now()->month)->whereYear('inspected_at', now()->year)->count();

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
            'chartData',
            'elTotal', 'elActive', 'elDamage', 'elInspectedThisMonth',
            'ewTotal', 'ewActive', 'ewDamage', 'ewInspectedThisMonth',
            'toolTotal', 'toolActive', 'toolDamage', 'toolInspectedThisMonth'
        ));
    }
}
