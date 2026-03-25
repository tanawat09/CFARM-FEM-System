@extends('layouts.app')

@section('page_title', 'ผลการตรวจสอบ #' . $toolInspection->inspection_no)

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <!-- Tool Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-wrench me-2"></i>
                    {{ $toolInspection->tool->type_name ?? '-' }}
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" width="40%">รหัสเครื่องมือ</td>
                        <td class="fw-bold">
                            <a href="{{ route('tools.show', $toolInspection->tool_id) }}">{{ $toolInspection->tool->tool_code }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">ชื่อ</td>
                        <td>{{ $toolInspection->tool->tool_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">พื้นที่</td>
                        <td><i class="bi bi-pin-map text-danger"></i> {{ $toolInspection->tool->location->location_name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Inspection Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i> ข้อมูลการตรวจ</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" width="40%">เลขที่ใบตรวจ</td>
                        <td class="fw-bold">
                            {{ $toolInspection->inspection_no }}
                            @if($toolInspection->inspection_type == 'pre_work')
                                <span class="badge bg-primary ms-2 rounded-pill"><i class="bi bi-clipboard2-check"></i> ก่อนเริ่มงาน</span>
                            @else
                                <span class="badge bg-success ms-2 rounded-pill"><i class="bi bi-calendar-check"></i> ประจำเดือน</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">วันที่ตรวจ</td>
                        <td>{{ $toolInspection->inspected_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ผู้ตรวจ</td>
                        <td>{{ $toolInspection->inspectedBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ผลตรวจรวม</td>
                        <td>
                            @if($toolInspection->overall_result == 'pass')
                                <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle"></i> ผ่าน</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3"><i class="bi bi-x-circle"></i> ไม่ผ่าน</span>
                            @endif
                        </td>
                    </tr>
                    @if($toolInspection->remark)
                    <tr>
                        <td class="text-muted">หมายเหตุ</td>
                        <td>{{ $toolInspection->remark }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <!-- Inspection Items -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i> รายการตรวจสอบ</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3" width="5%">#</th>
                                <th class="border-0">หมวดหมู่</th>
                                <th class="border-0">รายการ</th>
                                <th class="border-0 text-center">ผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($toolInspection->inspectionItems as $idx => $item)
                            <tr>
                                <td class="ps-3">{{ $idx + 1 }}</td>
                                <td><span class="badge bg-light text-secondary">{{ $item->category }}</span></td>
                                <td>{{ $item->item_name }}</td>
                                <td class="text-center">
                                    @if($item->result == 'ok')
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i> ปกติ</span>
                                    @elseif($item->result == 'not_ok')
                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle"></i> ไม่ผ่าน</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
