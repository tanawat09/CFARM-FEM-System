@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')

<!-- Location Filter Header -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i> Dashboard สรุปภาพรวม</h4>
    <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center custom-shadow px-3 py-2 bg-white rounded-pill">
        <label for="location_name" class="fw-bold text-muted me-2 mb-0 d-none d-md-block text-nowrap"><i class="bi bi-funnel-fill"></i> กรองพื้นที่:</label>
        <select name="location_name" id="location_name" class="form-select form-select-sm border-0 bg-light rounded-pill fw-medium text-dark focus-ring" style="min-width: 200px;" onchange="this.form.submit()">
            <option value="">-- ดึงข้อมูลรวมทุกสถานที่ --</option>
            @foreach($locationNames as $locName)
                <option value="{{ $locName }}" {{ (isset($selectedLocation) && $selectedLocation == $locName) ? 'selected' : '' }}>{{ $locName }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-heptagon fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">ถังทั้งหมด</h6>
                        <h3 class="fw-bold mb-0">{{ $totalExtinguishers ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">ใช้งานปกติ (Active)</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $activeExtinguishers ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-tools fs-3 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">ชำรุด / ส่งซ่อม</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $damageExtinguishers ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded">
                        <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">ใกล้หมดอายุ / หมดแล้ว</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ ($expireSoonCount ?? 0) + ($expiredCount ?? 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Chart Section (Dummpy placeholder for now) -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold">สถิติการตรวจเช็ค (12 เดือนย้อนหลัง)</h6>
            </div>
            <div class="card-body">
                <div class="p-3" style="height: 350px;">
                    <canvas id="inspectionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Inspections -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">การตรวจล่าสุด</h6>
                <a href="{{ route('inspections.index') }}" class="btn btn-sm btn-link text-decoration-none">ดูทั้งหมด</a>
            </div>
            <div class="card-body">
                @if(isset($recentInspections) && $recentInspections->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentInspections as $insp)
                        <div class="list-group-item px-0">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                    {{ $insp->fireExtinguisher->serial_number ?? 'N/A' }} 
                                    <small class="text-muted">({{ $insp->fireExtinguisher->location->location_name ?? '' }})</small>
                                </h6>
                                <span class="badge {{ $insp->overall_result == 'pass' ? 'bg-success' : 'bg-danger' }}">
                                    {{ strtoupper($insp->overall_result) }}
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-person"></i> {{ $insp->inspectedBy->name ?? 'Unknown' }} | <i class="bi bi-clock"></i> {{ $insp->inspected_at->diffForHumans() }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        ยังไม่มีประวัติการตรวจ
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData ?? null);
        
        if (chartData && chartData.labels) {
            const ctx = document.getElementById('inspectionChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'ผ่านการตรวจ (Pass)',
                            data: chartData.passed,
                            backgroundColor: 'rgba(25, 135, 84, 0.8)', // Bootstrap success
                            borderRadius: 4,
                        },
                        {
                            label: 'ชำรุด/แจ้งซ่อม (Fail)',
                            data: chartData.failed,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)', // Bootstrap danger
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
