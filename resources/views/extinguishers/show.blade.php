@extends('layouts.app')

@section('page_title', 'ข้อมูลรายละเอียดถังดับเพลิง')

@section('content')
<div class="row g-4 mb-4">
    <!-- General Info Card -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body p-4 position-relative">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light w-100">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded text-primary">
                        <i class="bi bi-shield-shaded display-6"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="fw-bold mb-1 text-dark">หมายเลขซีเรียล: {{ $extinguisher->serial_number }}</h4>
                        <span class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $extinguisher->location->location_name ?? 'ไม่ระบุพื้นที่' }}</span>
                    </div>
                    <div class="ms-auto pe-2">
                        @if($extinguisher->status == 'active')
                            <span class="badge bg-success rounded-pill px-3 py-2 fw-medium"><i class="bi bi-check-circle me-1"></i> ปกติ</span>
                        @elseif($extinguisher->status == 'damage')
                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-medium"><i class="bi bi-exclamation-triangle me-1"></i> ชำรุด</span>
                        @elseif($extinguisher->status == 'under_repair')
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-medium"><i class="bi bi-tools me-1"></i> กำลังซ่อม</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3 py-2 fw-medium"><i class="bi bi-trash me-1"></i> ทำลาย</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <span class="text-muted d-block small mb-1 fw-medium text-uppercase ls-wide">ประเภท (Type)</span>
                        <span class="fw-bold d-block text-dark fs-6">{{ str_replace('_', ' ', $extinguisher->type) }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block small mb-1 fw-medium text-uppercase ls-wide">ขนาด (Size)</span>
                        <span class="fw-bold d-block text-dark fs-6">{{ $extinguisher->size }} {{ $extinguisher->size_unit }}</span>
                    </div>
                    <div class="col-6 mt-4">
                        <span class="text-muted d-block small mb-1 fw-medium text-uppercase ls-wide">ยี่ห้อ (Brand)</span>
                        <span class="fw-bold d-block text-dark fs-6">{{ $extinguisher->brand ?? '-' }}</span>
                    </div>
                    <div class="col-6 mt-4">
                        <span class="text-muted d-block small mb-1 fw-medium text-uppercase ls-wide">รุ่น (Model)</span>
                        <span class="fw-bold d-block text-dark fs-6">{{ $extinguisher->model ?? '-' }}</span>
                    </div>
                    <div class="col-12 mt-4 pt-3 border-top border-light">
                        <span class="text-muted d-block small mb-2 fw-medium text-uppercase ls-wide"><i class="bi bi-geo-alt me-1"></i> พื้นที่ติดตั้ง (Location)</span>
                        <div class="d-flex align-items-center bg-light p-3 rounded-3">
                            <span class="fw-bold text-dark me-auto">{{ $extinguisher->location->location_name ?? 'N/A' }}</span>
                            <span class="badge bg-white text-dark border px-3">ชั้น {{ $extinguisher->location->floor ?? '-' }}</span>
                            @if($extinguisher->location->zone)
                            <span class="badge bg-white text-dark border px-3 ms-2">โซน {{ $extinguisher->location->zone }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline & Next Check Card -->
    <div class="col-md-7">
        <div class="row h-100 g-4">
            <!-- Alert Status -->
            <div class="col-12 h-50 pb-2">
                <div class="card border-0 shadow-sm h-100 bg-dark text-white rounded-4 overflow-hidden position-relative">
                    <div class="position-absolute" style="right: -20px; bottom: -40px; opacity: 0.1;">
                        <i class="bi bi-clock-history" style="font-size: 15rem;"></i>
                    </div>
                    <div class="card-body p-4 z-index-1">
                        <div class="row h-100 align-items-center">
                            @php
                                $d30 = now()->addDays(30);
                                $isExpiring = $extinguisher->expire_date && \Carbon\Carbon::parse($extinguisher->expire_date)->isBefore($d30);
                            @endphp
                            
                            <div class="col-6 border-end border-secondary">
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-2">กำหนดตรวจเช็คครั้งถัดไป</span>
                                <h3 class="mb-0 fw-bold {{ $extinguisher->next_inspection_date && \Carbon\Carbon::parse($extinguisher->next_inspection_date)->isPast() ? 'text-danger' : 'text-info' }}">
                                    @if($extinguisher->next_inspection_date)
                                        {{ \Carbon\Carbon::parse($extinguisher->next_inspection_date)->format('d / m / Y') }}
                                        @if(\Carbon\Carbon::parse($extinguisher->next_inspection_date)->isPast())
                                            <span class="d-block fs-6 mt-1 text-danger bg-danger bg-opacity-10 py-1 px-2 rounded d-inline-block"><i class="bi bi-exclamation-circle me-1"></i> เกินกำหนด</span>
                                        @endif
                                    @else
                                        รอตรวจสอบ
                                    @endif
                                </h3>
                            </div>
                            
                            <div class="col-6 ps-4">
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-2">วันหมดอายุสารเคมี</span>
                                <h3 class="mb-0 fw-bold {{ $isExpiring ? 'text-warning' : 'text-success' }}">
                                    @if($extinguisher->expire_date)
                                      {{ \Carbon\Carbon::parse($extinguisher->expire_date)->format('d / m / Y') }}
                                      @if($isExpiring && !\Carbon\Carbon::parse($extinguisher->expire_date)->isPast())
                                         <span class="d-block mt-1 fs-6 text-warning"><i class="bi bi-bell-fill mb-1"></i> ใกล้หมดอายุ</span>
                                      @endif
                                    @else
                                      ไม่ระบุ
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Manufacturing Dates -->
            <div class="col-12 h-50 pt-2">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <div class="row text-center mb-3 border-bottom pb-4">
                            <div class="col">
                                <i class="bi bi-calendar2-minus d-block text-muted mb-2 fs-4"></i>
                                <span class="text-secondary small fw-medium d-block">วันที่ผลิต (MFG)</span>
                                <span class="fw-bold fs-5 text-dark">{{ $extinguisher->manufacture_date ? \Carbon\Carbon::parse($extinguisher->manufacture_date)->format('M Y') : 'N/A' }}</span>
                            </div>
                            <div class="col border-start">
                                <i class="bi bi-tools d-block text-muted mb-2 fs-4"></i>
                                <span class="text-secondary small fw-medium d-block">ติดตั้ง-รันระบบ</span>
                                <span class="fw-bold fs-5 text-dark">{{ \Carbon\Carbon::parse($extinguisher->install_date)->format('M Y') }}</span>
                            </div>
                            <div class="col border-start">
                                <i class="bi bi-arrow-repeat d-block text-muted mb-2 fs-4"></i>
                                <span class="text-secondary small fw-medium d-block">ครบกำหนดเติมสาร</span>
                                <span class="fw-bold fs-5 text-dark">{{ $extinguisher->next_refill_date ? \Carbon\Carbon::parse($extinguisher->next_refill_date)->format('M Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between px-3 mt-1">
                            <a href="{{ route('inspections.create', ['extinguisher_id' => $extinguisher->id]) }}" class="btn btn-primary rounded-pill px-4 fw-medium"><i class="bi bi-shield-check me-2"></i> บันทึกการตรวจ</a>
                            <a href="{{ route('repair-logs.create', ['extinguisher_id' => $extinguisher->id]) }}" class="btn btn-danger rounded-pill px-4 fw-medium bg-opacity-10 text-danger border-danger"><i class="bi bi-wrench me-2"></i> แจ้งซ่อม</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Card -->
<div class="card border-0 shadow-sm rounded-4 mt-2">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i> ประวัติย้อนหลังล่าสุด</h5>
        
        <ul class="nav nav-pills" id="historyTab" role="tablist">
            <li class="nav-item border rounded-start border-end-0">
                <a class="nav-link active rounded-start bg-transparent text-dark fw-medium px-4" id="inspection-tab" data-bs-toggle="tab" href="#inspection" role="tab" aria-selected="true">
                    การตรวจเช็ค ({{ $extinguisher->inspections->count() }})
                </a>
            </li>
            <li class="nav-item border rounded-end border-start-0">
                <a class="nav-link rounded-end bg-transparent text-dark fw-medium px-4" id="repair-tab" data-bs-toggle="tab" href="#repair" role="tab" aria-selected="false">
                    การแจ้งซ่อม ({{ $extinguisher->repairLogs->count() }})
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <div class="tab-content" id="myTabContent">
            <!-- Inspection Tab -->
            <div class="tab-pane fade show active p-4" id="inspection" role="tabpanel">
                @if($extinguisher->inspections->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle list-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary small py-3 ps-4 rounded-start">วันที่ตรวจ</th>
                                <th class="text-uppercase text-secondary small py-3">ผู้ตรวจสอบ</th>
                                <th class="text-uppercase text-secondary small py-3">ผลการตรวจ</th>
                                <th class="text-uppercase text-secondary small py-3">หมายเหตุ</th>
                                <th class="rounded-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extinguisher->inspections as $ins)
                            <tr class="border-bottom border-light">
                                <td class="ps-4 fw-medium text-dark py-3">{{ \Carbon\Carbon::parse($ins->inspected_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($ins->inspectedBy->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-dark fw-medium">{{ $ins->inspectedBy->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($ins->overall_result == 'pass')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2"><i class="bi bi-check-lg me-1"></i> ผ่าน</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2"><i class="bi bi-x-lg me-1"></i> ไม่ผ่าน</span>
                                    @endif
                                </td>
                                <td class="text-muted"><span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $ins->remark ?? '-' }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('inspections.show', $ins->id) }}" class="btn btn-sm btn-light text-primary rounded-pill px-3">รายละเอียด</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x display-1 text-light d-block mb-3"></i>
                    <h5 class="fw-normal">ยังไม่มีประวัติการตรวจเช็คจากระบบนี้</h5>
                </div>
                @endif
            </div>
            
            <!-- Repair Tab -->
            <div class="tab-pane fade p-4" id="repair" role="tabpanel">
               @if($extinguisher->repairLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary small py-3 ps-4 rounded-start">วันที่แจ้ง</th>
                                <th class="text-uppercase text-secondary small py-3">ปัญหาขัดข้อง</th>
                                <th class="text-uppercase text-secondary small py-3">สถานะซ่อม</th>
                                <th class="text-uppercase text-secondary small py-3">วันสำเร็จ</th>
                                <th class="rounded-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extinguisher->repairLogs as $rep)
                            <tr class="border-bottom border-light">
                                <td class="ps-4 fw-medium text-dark py-3">{{ \Carbon\Carbon::parse($rep->created_at)->format('d M Y') }}</td>
                                <td class="text-muted"><span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $rep->problem }}</span></td>
                                <td>
                                    @if($rep->status == 'pending')
                                        <span class="badge bg-secondary px-3 py-2">รอตรวจสอบ</span>
                                    @elseif($rep->status == 'in_progress')
                                        <span class="badge bg-warning text-dark px-3 py-2">กำลังซ่อมแซม</span>
                                    @else
                                        <span class="badge bg-success px-3 py-2">ซ่อมแล้วเสร็จ</span>
                                    @endif
                                </td>
                                <td>{{ $rep->completed_date ? \Carbon\Carbon::parse($rep->completed_date)->format('d M Y') : '-' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('repair-logs.show', $rep->id) }}" class="btn btn-sm btn-light text-primary rounded-pill px-3">รายละเอียด</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-tools display-1 text-light d-block mb-3"></i>
                    <h5 class="fw-normal">ถังนี้ยังไม่เคยมีประวัติการแจ้งซ่อมในระบบ</h5>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Tab Styles */
.nav-pills .nav-link.active {
    background-color: #f8f9fa !important;
    color: #0d6efd !important;
    border-bottom: 3px solid #0d6efd !important;
    border-radius: 0;
}
</style>
@endsection
