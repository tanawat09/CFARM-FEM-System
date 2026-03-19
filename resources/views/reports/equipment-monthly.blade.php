@extends('layouts.app')

@section('page_title', 'รายงานสรุปการตรวจเช็ค' . $typeName . 'ประจำเดือน')

@section('styles')
<style>
    @media print {
        body { background-color: #fff !important; }
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-content { width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-header { padding: 0 0 1rem 0 !important; border-bottom: 2px solid #000 !important; }
        .card-body { padding: 1rem 0 0 0 !important; }
    }
</style>
@endsection

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-{{ $type == 'emergency_light' ? 'lightbulb text-warning' : 'droplet text-info' }} me-2"></i>
                {{ $typeName }} — เดือน {{ $month }} ปี {{ $year + 543 }}
            </h5>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill px-3 shadow-sm me-2 d-print-none"><i class="bi bi-arrow-left"></i> กลับ</a>
            <button type="button" onclick="window.print()" class="btn btn-secondary rounded-pill px-3 shadow-sm me-2 d-print-none"><i class="bi bi-printer"></i> พิมพ์หน้านี้</button>
            <a href="{{ route('reports.export-equipment-monthly-pdf', ['month' => $month, 'year' => $year, 'type' => $type]) }}" class="btn btn-danger rounded-pill px-3 shadow-sm d-print-none"><i class="bi bi-file-pdf"></i> Export PDF</a>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">
        
        <div class="row g-4 mb-5">
            <div class="col-md-5">
                <div class="card bg-light border-0 h-100 rounded-4">
                    <div class="card-body text-center p-4">
                        <h6 class="fw-bold text-muted mb-4">สัดส่วนการตรวจเช็ค (ภาพรวม)</h6>
                        <canvas id="monthlyChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-4 bg-{{ $type == 'emergency_light' ? 'warning' : 'info' }} bg-opacity-10 text-{{ $type == 'emergency_light' ? 'warning' : 'info' }} rounded-4 text-center h-100">
                            <i class="bi bi-{{ $type == 'emergency_light' ? 'lightbulb' : 'droplet' }} display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('equipment_count') }}</h2>
                            <small>อุปกรณ์ทั้งหมด (ชิ้น)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-primary bg-opacity-10 text-primary rounded-4 text-center h-100">
                            <i class="bi bi-clipboard-check display-4 mb-2"></i>
                            @php
                                $totalInspected = $locationStats->sum('inspections_passed') + $locationStats->sum('inspections_failed');
                            @endphp
                            <h2 class="fw-bold mb-0">{{ $totalInspected }}</h2>
                            <small>ตรวจแล้วเดือนนี้ (ครั้ง)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-success bg-opacity-10 text-success rounded-4 text-center h-100">
                            <i class="bi bi-check-circle display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('inspections_passed') }}</h2>
                            <small>ผ่าน (ปกติ)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-danger bg-opacity-10 text-danger rounded-4 text-center h-100">
                            <i class="bi bi-x-circle display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('inspections_failed') }}</h2>
                            <small>ไม่ผ่าน (ชำรุด)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4"><i class="bi bi-table text-primary me-2"></i> สรุปข้อมูลแยกตามพื้นที่ติดตั้ง</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="5%">ลำดับ</th>
                        <th class="py-3">พื้นที่ติดตั้ง</th>
                        <th class="py-3 text-center" width="15%">จำนวนอุปกรณ์</th>
                        <th class="py-3 text-center" width="15%">ตรวจผ่าน (ปกติ)</th>
                        <th class="py-3 text-center" width="15%">ตรวจไม่ผ่าน (ชำรุด)</th>
                        <th class="py-3 text-center" width="15%">ยังไม่ได้ตรวจ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locationStats as $index => $stat)
                        @php
                            $inspected = $stat->inspections_passed + $stat->inspections_failed;
                            $uninspected = $stat->equipment_count - $inspected;
                            $uninspected = $uninspected < 0 ? 0 : $uninspected;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $stat->location_name }}</td>
                            <td class="text-center">{{ $stat->equipment_count }}</td>
                            <td class="text-center text-success fw-bold">{{ $stat->inspections_passed }}</td>
                            <td class="text-center text-danger fw-bold">{{ $stat->inspections_failed }}</td>
                            <td class="text-center text-muted">{{ $uninspected }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูลอุปกรณ์ในระบบ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const passed = {{ $locationStats->sum('inspections_passed') }};
        const failed = {{ $locationStats->sum('inspections_failed') }};
        const total = {{ $locationStats->sum('equipment_count') }};
        const uninspected = total - (passed + failed) > 0 ? total - (passed + failed) : 0;

        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        if(total === 0 || (passed === 0 && failed === 0 && uninspected === 0)) {
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['ปกติ', 'ชำรุด', 'ยังไม่ได้ตรวจ'],
                datasets: [{
                    data: [passed, failed, uninspected],
                    backgroundColor: [
                        '#198754',
                        '#dc3545',
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
