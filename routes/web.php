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

Route::get('/', function () {
    return redirect()->route('login');
});

// Route to serve internal images directly without symlinks (Useful for Docker/Windows)
Route::get('/local-storage/locations/{filename}', function ($filename) {
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('locations/' . $filename)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('public')->response('locations/' . $filename);
})->name('storage.locations');

Route::get('/setup-fonts', function () {
    $dir = public_path('fonts');
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Official Sarabun fonts from Google Fonts Github
    $urlNormal = 'https://github.com/google/fonts/raw/main/ofl/sarabun/Sarabun-Regular.ttf';
    $urlBold = 'https://github.com/google/fonts/raw/main/ofl/sarabun/Sarabun-Bold.ttf';
    
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
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
            return '✅ ติดตั้ง Font ภาษาไทย (Sarabun) สำเร็จแล้วครับ! <br><br>ไฟล์ถูกโหลดไปที่: '.$dir. '<br><br>👉 <a href="/reports">กลับไปหน้ารายงานเพื่อ Export PDF</a>';
        } else {
            return '❌ เกิดข้อผิดพลาด: โหลดไฟล์มาแล้วแต่ไฟล์มีขนาดเล็กเกินไป (อาจติด Block หรือ 404)<br/>' . strlen($normal) . ' bytes';
        }
    } catch (\Exception $e) {
        return '❌ เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
});

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
    });

    // Admin Only (Manage Extinguishers)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('extinguishers/{extinguisher}/qr', [FireExtinguisherController::class, 'printQr'])->name('extinguishers.qr');
        Route::post('extinguishers/bulk-qr', [FireExtinguisherController::class, 'bulkQr'])->name('extinguishers.bulk-qr');
        Route::get('extinguishers/{extinguisher}/history', [FireExtinguisherController::class, 'history'])->name('extinguishers.history');
        Route::resource('extinguishers', FireExtinguisherController::class);
    });

    // All Auth Users (Admin, Safety Officer, User)
    Route::middleware(['role:admin|safety_officer|user'])->group(function () {
        Route::get('scan/{qr_code}', [InspectionController::class, 'scanQr'])->name('scan.qr');
        Route::post('inspections/draft', [InspectionController::class, 'saveDraft'])->name('inspections.draft.save');
        Route::get('inspections/draft/{id}', [InspectionController::class, 'loadDraft'])->name('inspections.draft.load');
        Route::resource('inspections', InspectionController::class);

        Route::post('repair-logs/{repair_log}/complete', [RepairLogController::class, 'complete'])->name('repair-logs.complete');
        Route::resource('repair-logs', RepairLogController::class);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/annual', [ReportController::class, 'annual'])->name('reports.annual');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('reports/export-monthly-pdf', [ReportController::class, 'exportMonthlyPdf'])->name('reports.export-monthly-pdf');
        Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('reports/damage', [ReportController::class, 'damageReport'])->name('reports.damage');

        // MAP (Interactive Floor Plan)
        Route::get('map', [\App\Http\Controllers\MapController::class, 'index'])->name('map.index');
        Route::post('map/pin', [\App\Http\Controllers\MapController::class, 'savePin'])->name('map.save-pin');
        Route::post('map/pin/remove', [\App\Http\Controllers\MapController::class, 'removePin'])->name('map.remove-pin');
    });
});

if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
