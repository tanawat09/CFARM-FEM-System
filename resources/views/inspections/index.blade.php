@extends('layouts.app')

@section('page_title', 'รายการตรวจสอบย้อนหลัง')

@section('content')

<!-- Action Button -->
<div class="row mb-4 align-items-center">
    <div class="col-md-6 mb-3 mb-md-0">
        <form action="{{ route('inspections.index') }}" method="GET" class="d-flex gap-2">
            <select name="location_id" class="form-select rounded-pill shadow-sm" style="max-width: 300px;" onchange="this.form.submit()">
                <option value="">-- กรองตามพื้นที่ติดตั้ง (ทั้งหมด) --</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->location_name }}
                    </option>
                @endforeach
            </select>
            @if(request('location_id'))
                <a href="{{ route('inspections.index') }}" class="btn btn-light rounded-pill border shadow-sm" title="ล้างตัวกรอง"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('inspections.create') }}" class="btn btn-primary rounded-pill fw-medium shadow-sm px-4"><i class="bi bi-shield-plus me-2"></i> บันทึกการตรวจเช็ค</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-4 py-3 border-0 fw-medium">เลขที่ใบตรวจ</th>
                        <th class="py-3 border-0 fw-medium">ข้อมูลอุปกรณ์ / พื้นที่</th>
                        <th class="py-3 border-0 fw-medium">ผู้ตรวจ</th>
                        <th class="py-3 border-0 fw-medium">วันที่ / เวลา</th>
                        <th class="py-3 border-0 fw-medium text-center">ผลลัพธ์</th>
                        <th class="pe-4 py-3 border-0 text-end fw-medium">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $ins)
                    <tr>
                        <td class="ps-4 py-3">
                            <span class="fw-bold text-dark">{{ $ins->inspection_no ?? 'INS-'.sprintf("%04d", $ins->id) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('extinguishers.show', $ins->fireExtinguisher->id) }}" class="text-decoration-none fw-semibold d-block">
                                <i class="bi bi-upc-scan me-1"></i> {{ $ins->fireExtinguisher->serial_number ?? 'N/A' }}
                            </a>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i> {{ $ins->fireExtinguisher->location->location_name ?? 'ไม่ระบุ' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle text-muted me-2 fs-5"></i>
                                <span>{{ $ins->inspectedBy->name ?? 'ไม่ระบุ' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark fw-medium">{{ \Carbon\Carbon::parse($ins->inspected_at)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($ins->inspected_at)->format('H:i') }} น.</small>
                        </td>
                        <td class="text-center">
                             @if($ins->overall_result == 'pass')
                                 <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> ปกติ</span>
                             @elseif($ins->overall_result == 'fail')
                                 <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50 px-3 py-2 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> ชำรุด/แจ้งซ่อม</span>
                             @else
                                 <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-50 px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> รอดำเนินการ</span>
                             @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('inspections.show', $ins->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x display-4 d-block mb-3 opacity-50"></i>
                            <h5 class="fw-normal">ไม่พบข้อมูลการตรวจเช็คในระบบ</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(isset($inspections) && $inspections->hasPages())
    <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-center">
        {{ $inspections->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
