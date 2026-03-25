<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function equipmentMonthly(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'emergency_light');

        $typeName = $type == 'emergency_light' ? 'ไฟฉุกเฉิน (Emergency Light)' : 'ที่ล้างตา/ฝักบัวฉุกเฉิน (Eyewash & Shower)';

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->equipment_count = \App\Models\SafetyEquipment::where('type', $type)
                ->where('location_id', $location->id)
                ->count();

            $location->inspections_passed = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                $q->where('location_id', $location->id)->where('type', $type);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->inspections_failed = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                $q->where('location_id', $location->id)->where('type', $type);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        // Filter out locations with no equipment
        $locationStats = $locationStats->filter(fn($loc) => $loc->equipment_count > 0)->values();

        return view('reports.equipment-monthly', compact('locationStats', 'month', 'year', 'type', 'typeName'));
    }

    public function equipmentAnnual(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'emergency_light');

        $typeName = $type == 'emergency_light' ? 'ไฟฉุกเฉิน (Emergency Light)' : 'ที่ล้างตา/ฝักบัวฉุกเฉิน (Eyewash & Shower)';

        $locations = \App\Models\Location::all();
        $annualData = [];

        foreach ($locations as $location) {
            $eqCount = \App\Models\SafetyEquipment::where('type', $type)
                ->where('location_id', $location->id)->count();

            if ($eqCount == 0) continue;

            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthStart = \Carbon\Carbon::create($year, $m, 1)->startOfMonth();
                $monthEnd = \Carbon\Carbon::create($year, $m, 1)->endOfMonth();

                $baseQuery = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                    $q->where('location_id', $location->id)->where('type', $type);
                })->whereBetween('inspected_at', [$monthStart, $monthEnd]);

                $passedCount = (clone $baseQuery)->where('overall_result', 'pass')->count();
                $failedCount = (clone $baseQuery)->where('overall_result', 'fail')->count();
                $totalInspected = $passedCount + $failedCount;

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $totalInspected,
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'equipment_count' => $eqCount,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        return view('reports.equipment-annual', compact('annualData', 'year', 'type', 'typeName'));
    }

    public function toolsMonthly(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->tool_count = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            $location->inspections_passed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'monthly')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->inspections_failed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'monthly')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        $locationStats = $locationStats->filter(fn($loc) => $loc->tool_count > 0)->values();

        return view('reports.tools-monthly', compact('locationStats', 'month', 'year'));
    }

    public function toolsPreWork(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->tool_count = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            $location->prework_passed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'pre_work')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->prework_failed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'pre_work')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        $locationStats = $locationStats->filter(fn($loc) => $loc->tool_count > 0)->values();

        return view('reports.tools-prework', compact('locationStats', 'month', 'year'));
    }

    public function toolsAnnual(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $locations = \App\Models\Location::all();
        $annualData = [];

        foreach ($locations as $location) {
            $toolCount = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            if ($toolCount == 0) continue;

            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthStart = \Carbon\Carbon::create($year, $m, 1)->startOfMonth();
                $monthEnd = \Carbon\Carbon::create($year, $m, 1)->endOfMonth();

                $baseQuery = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                    $q->where('location_id', $location->id);
                })->where('inspection_type', 'monthly')->whereBetween('inspected_at', [$monthStart, $monthEnd]);

                $passedCount = (clone $baseQuery)->where('overall_result', 'pass')->count();
                $failedCount = (clone $baseQuery)->where('overall_result', 'fail')->count();
                $totalInspected = $passedCount + $failedCount;

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $totalInspected,
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'tool_count' => $toolCount,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        return view('reports.tools-annual', compact('annualData', 'year'));
    }

    public function monthly(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Since withCount on hasManyThrough with closures might have issues in some Laravel versions, 
        // we can fetch the locations with their extinguishers, and do an explicit count via relationships or joins
        // The most reliable way here is to use withCount for extinguishers, and load inspections separately or use raw subqueries.
        
        $locationStats = \App\Models\Location::withCount('fireExtinguishers')->get();

        foreach ($locationStats as $location) {
            $location->inspections_passed = \App\Models\Inspection::whereHas('fireExtinguisher', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->distinct('extinguisher_id')
              ->count('extinguisher_id');

            $location->inspections_failed = \App\Models\Inspection::whereHas('fireExtinguisher', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->distinct('extinguisher_id')
              ->count('extinguisher_id');
        }

        return view('reports.monthly', compact('locationStats', 'month', 'year'));
    }

    public function annual(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // We need a summary of passed and failed inspections for each month of the given year, grouped by location.
        // For simplicity, we can fetch all inspections for the year, and let Laravel collections group them,
        // or we can use raw queries. We'll use Eloquent with scopes or group by.

        $locations = \App\Models\Location::with(['inspections' => function($query) use ($year) {
            $query->whereYear('inspected_at', $year);
        }])->get();

        $annualData = [];

        foreach ($locations as $location) {
            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                // Find inspections for this month
                $monthInspections = $location->inspections->filter(function($inspection) use ($m) {
                    return \Carbon\Carbon::parse($inspection->inspected_at)->month == $m;
                });

                // Since locations might have multiple extinguishers, we just want to know if there were any fail or pass
                // We'll just count passed and failed for this location in this month
                $passedCount = $monthInspections->where('overall_result', 'pass')->count();
                $failedCount = $monthInspections->where('overall_result', 'fail')->count();

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $monthInspections->count()
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        return view('reports.annual', compact('annualData', 'year'));
    }

    public function exportAnnualPdf(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $locations = \App\Models\Location::with(['inspections' => function($query) use ($year) {
            $query->whereYear('inspected_at', $year);
        }])->get();

        $annualData = [];

        foreach ($locations as $location) {
            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthInspections = $location->inspections->filter(function($inspection) use ($m) {
                    return \Carbon\Carbon::parse($inspection->inspected_at)->month == $m;
                });

                $passedCount = $monthInspections->where('overall_result', 'pass')->count();
                $failedCount = $monthInspections->where('overall_result', 'fail')->count();

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $monthInspections->count()
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.annual', compact('annualData', 'year'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('รายงานตรวจเช็คถังดับเพลิง_ประจำปี'.$year.'.pdf');
    }

    public function exportEquipmentMonthlyPdf(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'emergency_light');

        $typeName = $type == 'emergency_light' ? 'ไฟฉุกเฉิน' : 'ที่ล้างตา/ฝักบัวฉุกเฉิน';

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->equipment_count = \App\Models\SafetyEquipment::where('type', $type)
                ->where('location_id', $location->id)->count();

            $location->inspections_passed = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                $q->where('location_id', $location->id)->where('type', $type);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->inspections_failed = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                $q->where('location_id', $location->id)->where('type', $type);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        $locationStats = $locationStats->filter(fn($loc) => $loc->equipment_count > 0)->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.equipment-monthly', compact('locationStats', 'month', 'year', 'type', 'typeName'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        $typeSlug = $type == 'emergency_light' ? 'ไฟฉุกเฉิน' : 'ที่ล้างตา';
        return $pdf->download('รายงานตรวจเช็ค'.$typeSlug.'_เดือน'.$month.'_'.$year.'.pdf');
    }

    public function exportEquipmentAnnualPdf(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'emergency_light');

        $typeName = $type == 'emergency_light' ? 'ไฟฉุกเฉิน' : 'ที่ล้างตา/ฝักบัวฉุกเฉิน';

        $locations = \App\Models\Location::all();
        $annualData = [];

        foreach ($locations as $location) {
            $eqCount = \App\Models\SafetyEquipment::where('type', $type)
                ->where('location_id', $location->id)->count();

            if ($eqCount == 0) continue;

            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthStart = \Carbon\Carbon::create($year, $m, 1)->startOfMonth();
                $monthEnd = \Carbon\Carbon::create($year, $m, 1)->endOfMonth();

                $baseQuery = \App\Models\EquipmentInspection::whereHas('equipment', function($q) use ($location, $type) {
                    $q->where('location_id', $location->id)->where('type', $type);
                })->whereBetween('inspected_at', [$monthStart, $monthEnd]);

                $passedCount = (clone $baseQuery)->where('overall_result', 'pass')->count();
                $failedCount = (clone $baseQuery)->where('overall_result', 'fail')->count();

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $passedCount + $failedCount,
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'equipment_count' => $eqCount,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.equipment-annual', compact('annualData', 'year', 'type', 'typeName'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        $typeSlug = $type == 'emergency_light' ? 'ไฟฉุกเฉิน' : 'ที่ล้างตา';
        return $pdf->download('รายงานตรวจเช็ค'.$typeSlug.'_ประจำปี'.$year.'.pdf');
    }


    public function exportToolsMonthlyPdf(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->tool_count = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            $location->inspections_passed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'monthly')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->inspections_failed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'monthly')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        $locationStats = $locationStats->filter(fn($loc) => $loc->tool_count > 0)->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.tools-monthly', compact('locationStats', 'month', 'year'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('รายงานตรวจเช็คเครื่องมือช่าง_เดือน'.$month.'_'.$year.'.pdf');
    }

    public function exportToolsPreWorkPdf(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $locationStats = \App\Models\Location::all();

        foreach ($locationStats as $location) {
            $location->tool_count = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            $location->prework_passed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'pre_work')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->count();

            $location->prework_failed = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->where('inspection_type', 'pre_work')
              ->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->count();
        }

        $locationStats = $locationStats->filter(fn($loc) => $loc->tool_count > 0)->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.tools-prework', compact('locationStats', 'month', 'year'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('รายงานตรวจเช็คก่อนใช้งานเครื่องมือช่าง_เดือน'.$month.'_'.$year.'.pdf');
    }

    public function exportToolsAnnualPdf(Request $request)

    {
        $year = $request->input('year', date('Y'));

        $locations = \App\Models\Location::all();
        $annualData = [];

        foreach ($locations as $location) {
            $toolCount = \App\Models\Tool::where('location_id', $location->id)
                ->where('status', '!=', 'disposed')
                ->count();

            if ($toolCount == 0) continue;

            $monthlyStats = [];
            $totalPassed = 0;
            $totalFailed = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthStart = \Carbon\Carbon::create($year, $m, 1)->startOfMonth();
                $monthEnd = \Carbon\Carbon::create($year, $m, 1)->endOfMonth();

                $baseQuery = \App\Models\ToolInspection::whereHas('tool', function($q) use ($location) {
                    $q->where('location_id', $location->id);
                })->where('inspection_type', 'monthly')->whereBetween('inspected_at', [$monthStart, $monthEnd]);

                $passedCount = (clone $baseQuery)->where('overall_result', 'pass')->count();
                $failedCount = (clone $baseQuery)->where('overall_result', 'fail')->count();

                $totalPassed += $passedCount;
                $totalFailed += $failedCount;

                $monthlyStats[$m] = [
                    'passed' => $passedCount,
                    'failed' => $failedCount,
                    'total_inspected' => $passedCount + $failedCount,
                ];
            }

            $annualData[] = [
                'location_name' => $location->location_name,
                'tool_count' => $toolCount,
                'monthly_stats' => $monthlyStats,
                'total_passed' => $totalPassed,
                'total_failed' => $totalFailed,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.tools-annual', compact('annualData', 'year'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('รายงานตรวจเช็คเครื่องมือช่าง_ประจำปี'.$year.'.pdf');
    }

    public function damageReport(Request $request)
    {
        $start_month = $request->input('start_month', date('Y-m', strtotime('-6 months')));
        $end_month = $request->input('end_month', date('Y-m'));

        $start_date = \Carbon\Carbon::createFromFormat('Y-m', $start_month)->startOfMonth();
        $end_date = \Carbon\Carbon::createFromFormat('Y-m', $end_month)->endOfMonth();

        // Fetch repair logs within the given date range
        $repairLogs = \App\Models\RepairLog::with(['fireExtinguisher.location', 'repairedBy'])
            ->whereBetween('created_at', [$start_date, $end_date])
            ->latest('created_at')
            ->get();
            
        // Calculate some basic stats
        $totalReported = $repairLogs->count();
        $totalCompleted = $repairLogs->where('status', 'completed')->count();
        $totalPending = $totalReported - $totalCompleted;

        return view('reports.damage', compact('repairLogs', 'start_month', 'end_month', 'totalReported', 'totalCompleted', 'totalPending'));
    }

    public function exportPdf(Request $request)
    {
        $start_month = $request->input('start_month', date('Y-m', strtotime('-6 months')));
        $end_month = $request->input('end_month', date('Y-m'));

        $start_date = \Carbon\Carbon::createFromFormat('Y-m', $start_month)->startOfMonth();
        $end_date = \Carbon\Carbon::createFromFormat('Y-m', $end_month)->endOfMonth();

        $repairLogs = \App\Models\RepairLog::with(['fireExtinguisher.location', 'repairedBy'])
            ->whereBetween('created_at', [$start_date, $end_date])
            ->latest('created_at')
            ->get();
            
        $totalReported = $repairLogs->count();
        $totalCompleted = $repairLogs->where('status', 'completed')->count();
        $totalPending = $totalReported - $totalCompleted;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.damage', compact('repairLogs', 'start_month', 'end_month', 'totalReported', 'totalCompleted', 'totalPending'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');
        
        return $pdf->download('รายงานแจ้งซ่อม_'.$start_month.'_ถึง_'.$end_month.'.pdf');
    }

    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $locationStats = \App\Models\Location::withCount('fireExtinguishers')->get();

        foreach ($locationStats as $location) {
            $location->inspections_passed = \App\Models\Inspection::whereHas('fireExtinguisher', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'pass')
              ->distinct('extinguisher_id')
              ->count('extinguisher_id');

            $location->inspections_failed = \App\Models\Inspection::whereHas('fireExtinguisher', function($q) use ($location) {
                $q->where('location_id', $location->id);
            })->whereMonth('inspected_at', $month)
              ->whereYear('inspected_at', $year)
              ->where('overall_result', 'fail')
              ->distinct('extinguisher_id')
              ->count('extinguisher_id');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.monthly', compact('locationStats', 'month', 'year'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'THSarabunNew',
                'chroot' => public_path(),
                'fontDir' => public_path('fonts'),
                'fontCache' => public_path('fonts')
            ])
            ->setPaper('a4', 'landscape');
        
        return $pdf->download('รายงานตรวจเช็คประจำเดือน_'.$month.'_'.$year.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $start_month = $request->input('start_month', date('Y-m', strtotime('-6 months')));
        $end_month = $request->input('end_month', date('Y-m'));
        
        $start_date = \Carbon\Carbon::createFromFormat('Y-m', $start_month)->startOfMonth();
        $end_date = \Carbon\Carbon::createFromFormat('Y-m', $end_month)->endOfMonth();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DamageReportExport($start_date, $end_date), 'รายงานแจ้งซ่อม_'.$start_month.'_ถึง_'.$end_month.'.xlsx');
    }
}
