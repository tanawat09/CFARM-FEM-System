@extends('layouts.app')

@section('page_title', 'รายงานการชำรุดและแจ้งซ่อม')

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark">รายงานอุปกรณ์ชำรุด</h5>
            <small class="text-muted">ตั้งแต่ {{ \Carbon\Carbon::createFromFormat('Y-m', $start_month)->translatedFormat('F Y') }} ถึง {{ \Carbon\Carbon::createFromFormat('Y-m', $end_month)->translatedFormat('F Y') }}</small>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill px-3 shadow-sm me-2"><i class="bi bi-arrow-left"></i> กลับ</a>
            <a href="{{ route('reports.export-pdf', ['start_month' => $start_month, 'end_month' => $end_month]) }}" class="btn btn-danger rounded-pill px-3 shadow-sm me-2"><i class="bi bi-file-pdf"></i> Export PDF</a>
            <a href="{{ route('reports.export-excel', ['start_month' => $start_month, 'end_month' => $end_month]) }}" class="btn btn-success rounded-pill px-3 shadow-sm"><i class="bi bi-file-excel"></i> Export Excel</a>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-4 text-center border h-100">
                    <h6 class="text-muted fw-bold mb-2">จำนวนรายการแจ้งซ่อมทั้งหมด</h6>
                    <h2 class="display-5 fw-bold text-dark mb-0">{{ $totalReported }}</h2>
                    <small class="text-muted">รายการ</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-success bg-opacity-10 rounded-4 text-center border border-success border-opacity-25 h-100">
                    <h6 class="text-success fw-bold mb-2">ซ่อมแซมเสร็จสิ้นแล้ว</h6>
                    <h2 class="display-5 fw-bold text-success mb-0">{{ $totalCompleted }}</h2>
                    <small class="text-success opacity-75">รายการ</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-warning bg-opacity-10 rounded-4 text-center border border-warning border-opacity-25 h-100">
                    <h6 class="text-warning fw-bold mb-2">อยู่ระหว่างดำเนินการ</h6>
                    <h2 class="display-5 fw-bold text-warning mb-0">{{ $totalPending }}</h2>
                    <small class="text-warning opacity-75">รายการ</small>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-4"><i class="bi bi-list-ul text-primary me-2"></i> รายละเอียดการแจ้งซ่อม</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="5%">ลำดับ</th>
                        <th class="py-3" width="15%">รหัสถังดับเพลิง</th>
                        <th class="py-3" width="20%">สถานที่ติดตั้ง</th>
                        <th class="py-3" width="20%">อาการชำรุด</th>
                        <th class="py-3 text-center" width="15%">วันที่แจ้ง</th>
                        <th class="py-3 text-center" width="15%">สถานะ</th>
                        <th class="py-3 text-center" width="10%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairLogs as $index => $log)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-medium">
                                <a href="{{ route('extinguishers.show', $log->extinguisher_id) }}" class="text-decoration-none">
                                    {{ $log->fireExtinguisher->serial_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td>{{ $log->fireExtinguisher->location->location_name ?? '-' }}</td>
                            <td>{{ Str::limit($log->problem, 50) }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M y') }}</td>
                            <td class="text-center">
                                @if($log->status == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">รอซ่อม</span>
                                @elseif($log->status == 'in_progress')
                                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill">กำลังซ่อม</span>
                                @else
                                    <span class="badge bg-success px-3 py-2 rounded-pill">เสร็จสิ้น</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('repair-logs.show', $log->id) }}" class="btn btn-sm btn-light border shadow-sm" title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">ไม่พบข้อมูลการแจ้งซ่อมในช่วงเวลาที่เลือก</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
