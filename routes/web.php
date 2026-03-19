<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FireExtinguisherController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\RepairLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SafetyEquipmentController;
use App\Http\Controllers\EquipmentInspectionController;
use App\Http\Controllers\AuditLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route to serve internal images directly without symlinks (Useful for Docker/Windows)
Route::get('/local-storage/locations/{filename}', function ($filename) {
    // Prevent path traversal attacks
    $filename = basename($filename);
    if (!preg_match('/^[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|gif)$/i', $filename)) {
        abort(403);
    }
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('locations/' . $filename)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response('locations/' . $filename);
})->name('storage.locations')->where('filename', '[^/]+');

Route::get('/setup-fonts', function () {
    $dir = public_path('fonts');
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Official Sarabun fonts from Google Fonts Github
    $urlNormal = 'https://github.com/google/fonts/raw/main/ofl/sarabun/Sarabun-Regular.ttf';
    $urlBold = 'https://github.com/google/fonts/raw/main/ofl/sarabun/Sarabun-Bold.ttf';

    $arrContextOptions=array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        )
    );

    try {
        $normal = @file_get_contents($urlNormal, false, stream_context_create($arrContextOptions));
        $bold = @file_get_contents($urlBold, false, stream_context_create($arrContextOptions));

        // Ensure we got actual TTF files (more than 10KB)
        if ($normal && strlen($normal) > 10000 && $bold && strlen($bold) > 10000) {
            file_put_contents($dir.'/THSarabunNew.ttf', $normal);
            file_put_contents($dir.'/THSarabunNew-Bold.ttf', $bold);
            return redirect('/reports')->with('success', 'ติดตั้ง Font ภาษาไทย (Sarabun) สำเร็จแล้ว');
        } else {
            return back()->with('error', 'โหลดไฟล์ font ไม่สำเร็จ อาจติด Block หรือ 404');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'เกิดข้อผิดพลาดในการโหลด font');
    }
})->middleware(['auth', 'role:admin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/scan', function () {
        return view('scan');
    })->name('scan');

    // Admin Only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('locations', LocationController::class);
        Route::resource('users', UserController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Admin Only (Manage Extinguishers)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('extinguishers/{extinguisher}/qr', [FireExtinguisherController::class, 'printQr'])->name('extinguishers.qr');
        Route::post('extinguishers/bulk-qr', [FireExtinguisherController::class, 'bulkQr'])->name('extinguishers.bulk-qr');
        Route::get('extinguishers/{extinguisher}/history', [FireExtinguisherController::class, 'history'])->name('extinguishers.history');
        Route::resource('extinguishers', FireExtinguisherController::class);
    });

    // All Auth Users - Read-only & Inspection actions
    Route::middleware(['role:admin|safety_officer|user'])->group(function () {
        Route::get('scan/{qr_code}', [InspectionController::class, 'scanQr'])->name('scan.qr');
        Route::post('inspections/draft', [InspectionController::class, 'saveDraft'])->name('inspections.draft.save');
        Route::get('inspections/draft/{id}', [InspectionController::class, 'loadDraft'])->name('inspections.draft.load');
        Route::resource('inspections', InspectionController::class)->only(['index', 'create', 'store', 'show']);

        Route::get('repair-logs', [RepairLogController::class, 'index'])->name('repair-logs.index');
        Route::get('repair-logs/{repair_log}', [RepairLogController::class, 'show'])->name('repair-logs.show');

        // Safety Equipment - View & Inspect only
        Route::get('scan-equipment/{qr_code}', [SafetyEquipmentController::class, 'scanQr'])->name('scan-equipment.qr');
        Route::resource('safety-equipment', SafetyEquipmentController::class)->parameters(['safety-equipment' => 'safetyEquipment'])->only(['index', 'show']);
        Route::resource('equipment-inspections', EquipmentInspectionController::class)->parameters(['equipment-inspections' => 'equipmentInspection'])->only(['index', 'create', 'store', 'show']);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/equipment-monthly', [ReportController::class, 'equipmentMonthly'])->name('reports.equipment-monthly');
        Route::get('reports/equipment-annual', [ReportController::class, 'equipmentAnnual'])->name('reports.equipment-annual');
        Route::get('reports/export-equipment-monthly-pdf', [ReportController::class, 'exportEquipmentMonthlyPdf'])->name('reports.export-equipment-monthly-pdf');
        Route::get('reports/export-equipment-annual-pdf', [ReportController::class, 'exportEquipmentAnnualPdf'])->name('reports.export-equipment-annual-pdf');
        Route::get('reports/annual', [ReportController::class, 'annual'])->name('reports.annual');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('reports/export-monthly-pdf', [ReportController::class, 'exportMonthlyPdf'])->name('reports.export-monthly-pdf');
        Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('reports/damage', [ReportController::class, 'damageReport'])->name('reports.damage');

        // MAP - View only for all users
        Route::get('map', [\App\Http\Controllers\MapController::class, 'index'])->name('map.index');
    });

    // Admin & Safety Officer - Write/Modify operations
    Route::middleware(['role:admin|safety_officer'])->group(function () {
        Route::resource('inspections', InspectionController::class)->only(['destroy']);

        Route::get('repair-logs/create', [RepairLogController::class, 'create'])->name('repair-logs.create');
        Route::post('repair-logs', [RepairLogController::class, 'store'])->name('repair-logs.store');
        Route::get('repair-logs/{repair_log}/edit', [RepairLogController::class, 'edit'])->name('repair-logs.edit');
        Route::put('repair-logs/{repair_log}', [RepairLogController::class, 'update'])->name('repair-logs.update');
        Route::delete('repair-logs/{repair_log}', [RepairLogController::class, 'destroy'])->name('repair-logs.destroy');
        Route::post('repair-logs/{repair_log}/complete', [RepairLogController::class, 'complete'])->name('repair-logs.complete');

        // Safety Equipment - CRUD for admin/safety_officer
        Route::get('safety-equipment/{safetyEquipment}/qr', [SafetyEquipmentController::class, 'printQr'])->name('safety-equipment.qr');
        Route::post('safety-equipment/bulk-qr', [SafetyEquipmentController::class, 'bulkQr'])->name('safety-equipment.bulk-qr');
        Route::resource('safety-equipment', SafetyEquipmentController::class)->parameters(['safety-equipment' => 'safetyEquipment'])->only(['create', 'store', 'edit', 'update', 'destroy']);

        // MAP - Pin management for admin/safety_officer
        Route::post('map/pin', [\App\Http\Controllers\MapController::class, 'savePin'])->name('map.save-pin');
        Route::post('map/pin/remove', [\App\Http\Controllers\MapController::class, 'removePin'])->name('map.remove-pin');
    });
});

if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
