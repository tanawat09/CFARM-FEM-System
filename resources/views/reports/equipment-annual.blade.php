@extends('layouts.app')

@section('page_title', 'รายงานสรุปการตรวจ' . $typeName . 'ประจำปี')

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
                <i class="bi bi-{{ $type == 'emergency_light' ? 'lightbulb text-warning' : 'droplet text-info' }} me-2"></i>
                รายงานสรุปการตรวจ{{ $typeName }}ประจำปี
            </h5>
            <small class="text-muted">แสดงข้อมูลสถิติประจำปี {{ $year + 543 }}</small>
        </div>
        <div class="d-flex gap-2 d-print-none">
            <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ / PDF</button>
            <a href="{{ route('reports.export-equipment-annual-pdf', ['year' => $year, 'type' => $type]) }}" class="btn btn-danger btn-sm rounded-pill px-3"><i class="bi bi-file-pdf"></i> Export PDF</a>
        </div>
    </div>
    
    <div class="card-body p-4 p-md-5">

        <!-- Summary Chart -->
        <div class="row g-4 mb-5">
            <div class="col-md-8">
                <div class="card bg-light border-0 rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted mb-3">สถิติการตรวจเช็ครายเดือน (ปี {{ $year + 543 }})</h6>
                        <div style="height: 280px;">
                            <canvas id="annualBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row g-3">
                    @php
                        $grandTotalEquipment = collect($annualData)->sum('equipment_count');
                        $grandTotalPassed = collect($annualData)->sum('total_passed');
                        $grandTotalFailed = collect($annualData)->sum('total_failed');
                    @endphp
                    <div class="col-12">
                        <div class="p-3 bg-{{ $type == 'emergency_light' ? 'warning' : 'info' }} bg-opacity-10 text-{{ $type == 'emergency_light' ? 'warning' : 'info' }} rounded-4 text-center">
                            <i class="bi bi-{{ $type == 'emergency_light' ? 'lightbulb' : 'droplet' }} fs-2 mb-1"></i>
                            <h2 class="fw-bold mb-0">{{ $grandTotalEquipment }}</h2>
                            <small>อุปกรณ์ทั้งหมด</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 text-center">
                            <i class="bi bi-check-circle fs-3 mb-1"></i>
                            <h3 class="fw-bold mb-0">{{ $grandTotalPassed }}</h3>
                            <small>ผ่านทั้งปี</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 text-center">
                            <i class="bi bi-x-circle fs-3 mb-1"></i>
                            <h3 class="fw-bold mb-0">{{ $grandTotalFailed }}</h3>
                            <small>ไม่ผ่านทั้งปี</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card bg-light border-0 rounded-4">
                            <div class="card-body text-center p-3">
                                <h6 class="fw-bold text-muted mb-2">สัดส่วนผล</h6>
                                <canvas id="annualPieChart" style="max-height: 160px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table by Location -->
        <h5 class="fw-bold mb-4"><i class="bi bi-table text-primary me-2"></i> สรุปข้อมูลแยกตามพื้นที่</h5>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="4%" rowspan="2">ลำดับ</th>
                        <th class="py-3" width="14%" rowspan="2">พื้นที่ติดตั้ง</th>
                        <th class="py-3 text-center" width="5%" rowspan="2">อุปกรณ์</th>
                        <th class="py-2 text-center" colspan="12">ผลการตรวจสอบ ({{ $year + 543 }})</th>
                        <th class="py-3 text-center" width="6%" rowspan="2">รวมผ่าน</th>
                        <th class="py-3 text-center" width="6%" rowspan="2">รวมไม่ผ่าน</th>
                    </tr>
                    <tr>
                        @php
                            $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                        @endphp
                        @foreach($thaiMonths as $month)
                            <th class="text-center small">{{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($annualData as $index => $data)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $data['location_name'] }}</td>
                            <td class="text-center">{{ $data['equipment_count'] }}</td>
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $stats = $data['monthly_stats'][$m];
                                @endphp
                                <td class="text-center">
                                    @if($stats['total_inspected'] == 0)
                                        <span class="text-muted opacity-25">-</span>
                                    @elseif($stats['failed'] > 0)
                                        <span class="text-danger fw-bold" title="พบชำรุด {{ $stats['failed'] }} รายการ"><i class="bi bi-x-circle-fill"></i></span>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i></span>
                                    @endif
                                </td>
                            @endfor
                            <td class="text-center fw-bold text-success">{{ $data['total_passed'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $data['total_failed'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-5 text-muted">ไม่พบข้อมูลอุปกรณ์ในระบบประจำปี {{ $year + 543 }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 border-top pt-4">
            <h6 class="fw-bold">คำอธิบายสัญลักษณ์</h6>
            <div class="d-flex gap-4 mt-2">
                <div><span class="text-success"><i class="bi bi-check-circle-fill"></i></span> ตรวจสอบแล้ว ปกติทั้งหมด</div>
                <div><span class="text-danger"><i class="bi bi-x-circle-fill"></i></span> ตรวจสอบแล้ว พบอุปกรณ์ชำรุด</div>
                <div><span class="text-muted opacity-50">-</span> ยังไม่มีข้อมูลการตรวจสอบ</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Bar Chart (monthly passed vs failed)
        @php
            $monthLabels = [];
            $passedData = [];
            $failedData = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthLabels[] = $thaiMonths[$m - 1];
                $p = 0; $f = 0;
                foreach ($annualData as $d) {
                    $p += $d['monthly_stats'][$m]['passed'];
                    $f += $d['monthly_stats'][$m]['failed'];
                }
                $passedData[] = $p;
                $failedData[] = $f;
            }
        @endphp

        const barCtx = document.getElementById('annualBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [
                    {
                        label: 'ผ่าน (ปกติ)',
                        data: @json($passedData),
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'ไม่ผ่าน (ชำรุด)',
                        data: @json($failedData),
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Pie Chart
        const passed = {{ $grandTotalPassed }};
        const failed = {{ $grandTotalFailed }};
        if (passed > 0 || failed > 0) {
            const pieCtx = document.getElementById('annualPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['ผ่าน', 'ไม่ผ่าน'],
                    datasets: [{
                        data: [passed, failed],
                        backgroundColor: ['#198754', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endsection
