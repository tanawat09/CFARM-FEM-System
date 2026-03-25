@extends('layouts.app')

@section('page_title', 'รายงานสรุปการตรวจเช็คก่อนใช้งาน (เครื่องมือช่าง)')

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
<div class="d-none d-print-block text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 100px;">
</div>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-file-earmark-check text-secondary me-2"></i>
                ตรวจเช็คก่อนใช้งาน (เครื่องมือช่าง) — เดือน {{ $month }} ปี {{ $year + 543 }}
            </h5>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill px-3 shadow-sm me-2 d-print-none"><i class="bi bi-arrow-left"></i> กลับ</a>
            <button type="button" onclick="window.print()" class="btn btn-secondary rounded-pill px-3 shadow-sm me-2 d-print-none"><i class="bi bi-printer"></i> พิมพ์หน้านี้</button>
            <a href="{{ route('reports.export-tools-prework-pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-danger rounded-pill px-3 shadow-sm d-print-none"><i class="bi bi-file-pdf"></i> Export PDF</a>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">
        
        <div class="row g-4 mb-5">
            <div class="col-md-5">
                <div class="card bg-light border-0 h-100 rounded-4">
                    <div class="card-body text-center p-4">
                        <h6 class="fw-bold text-muted mb-4">สัดส่วนผลการประเมินความปลอดภัยก่อนใช้งาน</h6>
                        <canvas id="monthlyChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-4 bg-primary bg-opacity-10 text-primary rounded-4 text-center h-100">
                            <i class="bi bi-tools display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('tool_count') }}</h2>
                            <small>อุปกรณ์ทั้งหมด (ชิ้น)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-secondary bg-opacity-10 text-secondary rounded-4 text-center h-100">
                            <i class="bi bi-ui-checks display-4 mb-2"></i>
                            @php
                                $totalInspected = $locationStats->sum('prework_passed') + $locationStats->sum('prework_failed');
                            @endphp
                            <h2 class="fw-bold mb-0">{{ $totalInspected }}</h2>
                            <small>ประเมินก่อนใช้งานทั้งหมด (ครั้ง)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-success bg-opacity-10 text-success rounded-4 text-center h-100">
                            <i class="bi bi-check-circle display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('prework_passed') }}</h2>
                            <small>ผ่าน / ปลอดภัย (ครั้ง)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 bg-danger bg-opacity-10 text-danger rounded-4 text-center h-100">
                            <i class="bi bi-x-circle display-4 mb-2"></i>
                            <h2 class="fw-bold mb-0">{{ $locationStats->sum('prework_failed') }}</h2>
                            <small>ไม่ผ่าน / พบชำรุด (ครั้ง)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4"><i class="bi bi-table text-secondary me-2"></i> สรุปข้อมูลแยกตามพื้นที่ติดตั้ง</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="5%">ลำดับ</th>
                        <th class="py-3">พื้นที่ติดตั้ง</th>
                        <th class="py-3 text-center" width="15%">จำนวนอุปกรณ์ (ชิ้น)</th>
                        <th class="py-3 text-center" width="15%">ตรวจก่อนใช้งาน (ครั้ง)</th>
                        <th class="py-3 text-center" width="15%">ปกติ (ครั้ง)</th>
                        <th class="py-3 text-center" width="15%">พบชำรุด (ครั้ง)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locationStats as $index => $stat)
                        @php
                            $inspected = $stat->prework_passed + $stat->prework_failed;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $stat->location_name }}</td>
                            <td class="text-center">{{ $stat->tool_count }}</td>
                            <td class="text-center fw-bold">{{ $inspected }}</td>
                            <td class="text-center text-success fw-bold">{{ $stat->prework_passed }}</td>
                            <td class="text-center text-danger fw-bold">{{ $stat->prework_failed }}</td>
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
        const passed = {{ $locationStats->sum('prework_passed') }};
        const failed = {{ $locationStats->sum('prework_failed') }};
        const totalChecks = passed + failed;

        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        if(totalChecks === 0) {
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['ปกติ', 'พบชำรุด'],
                datasets: [{
                    data: [passed, failed],
                    backgroundColor: [
                        '#198754',
                        '#dc3545'
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
