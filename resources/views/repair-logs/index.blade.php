@extends('layouts.app')

@section('page_title', 'รายการแจ้งซ่อม')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom border-light p-4 pt-3 pb-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">สถานะแจ้งซ่อมทั้งหมด</h5>
        <div>
            <a href="#" class="btn btn-outline-secondary btn-sm px-3 rounded-pill me-2"><i class="bi bi-funnel me-1"></i> กรองสถานะ</a>
            <a href="{{ route('repair-logs.create') }}" class="btn btn-danger btn-sm px-3 rounded-pill"><i class="bi bi-tools me-1"></i> แจ้งซ่อม</a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 border-bottom border-light fw-medium small text-uppercase" width="15%">เลขที่ใบซ่อม</th>
                        <th class="py-3 border-bottom border-light fw-medium small text-uppercase" width="15%">รหัสสินทรัพย์</th>
                        <th class="py-3 border-bottom border-light fw-medium small text-uppercase" width="20%">ปัญหาเบื้องต้น</th>
                        <th class="py-3 border-bottom border-light fw-medium small text-uppercase" width="15%">ผู้แจ้ง</th>
                        <th class="py-3 border-bottom border-light fw-medium small text-uppercase text-center" width="15%">สถานะ</th>
                        <th class="pe-4 py-3 border-bottom border-light fw-medium small text-uppercase text-end" width="20%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairLogs as $rep)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3 fw-bold text-dark">
                            {{ $rep->repair_no ?? 'REQ-'.sprintf("%04d", $rep->id) }}<br>
                            <small class="text-muted fw-normal">{{ \Carbon\Carbon::parse($rep->created_at)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <span class="d-inline-block px-2 py-1 bg-light border rounded">
                                <i class="bi bi-upc-scan me-1 text-muted"></i> {{ $rep->fireExtinguisher->serial_number ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $rep->problem }}">
                                {{ $rep->problem }}
                            </div>
                        </td>
                        <td>
                            {{ $rep->repairedBy->name ?? 'ไม่ระบุ' }}
                        </td>
                        <td class="text-center">
                             @if($rep->status == 'pending')
                                 <span class="badge bg-secondary rounded-pill px-3 py-2 fw-normal"><i class="bi bi-clock me-1"></i> รอดำเนินการ</span>
                             @elseif($rep->status == 'in_progress')
                                 <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-medium"><i class="bi bi-tools me-1"></i> กำลังซ่อมแซม</span>
                             @else
                                 <span class="badge bg-success rounded-pill px-3 py-2 fw-normal"><i class="bi bi-check-all me-1"></i> ปิดงานแล้ว</span>
                             @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('repair-logs.show', $rep->id) }}" class="btn btn-sm btn-link text-decoration-none">อัปเดตงาน</a>
                            @if($rep->status != 'completed')
                                <form action="{{ route('repair-logs.complete', $rep->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('ยืนยันปิดงานซ่อม ถังดับเพลิงพร้อมใช้งาน?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 ms-1 shadow-sm"><i class="bi bi-check-lg"></i> ปิดงาน</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-tools display-4 d-block mb-3 opacity-50"></i>
                            <h5 class="fw-normal">ไม่พบรายการแจ้งซ่อม</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(isset($repairLogs) && $repairLogs->hasPages())
    <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-center">
        {{ $repairLogs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
