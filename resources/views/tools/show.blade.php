@extends('layouts.app')

@section('page_title', 'รายละเอียดเครื่องมือ: ' . $tool->tool_code)

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <!-- Tool Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-wrench me-2"></i>
                    {{ $tool->type_name }}
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" width="35%">รหัสเครื่องมือ</td>
                        <td class="fw-bold">{{ $tool->tool_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ชื่อ</td>
                        <td class="fw-bold">{{ $tool->tool_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ประเภท</td>
                        <td>
                            <span class="badge bg-{{ $tool->toolType->color ?? 'secondary' }} rounded-pill">{{ $tool->type_name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">พื้นที่</td>
                        <td><i class="bi bi-pin-map text-danger"></i> {{ $tool->location->location_name ?? '-' }}</td>
                    </tr>
                    @if($tool->house || $tool->zone)
                    <tr>
                        <td class="text-muted">อาคาร/โซน</td>
                        <td>{{ $tool->house ?? '-' }} / {{ $tool->zone ?? '-' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">ยี่ห้อ</td>
                        <td>{{ $tool->brand ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">รุ่น</td>
                        <td>{{ $tool->model ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">S/N</td>
                        <td>{{ $tool->serial_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">วันที่ซื้อ</td>
                        <td>{{ $tool->purchase_date ? $tool->purchase_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">สถานะ</td>
                        <td>
                            @if($tool->status == 'active')
                                <span class="badge bg-success rounded-pill px-3">พร้อมใช้งาน</span>
                            @elseif($tool->status == 'under_repair')
                                <span class="badge bg-warning text-dark rounded-pill px-3">ซ่อมบำรุง</span>
                            @elseif($tool->status == 'inactive')
                                <span class="badge bg-secondary rounded-pill px-3">ไม่ใช้งาน</span>
                            @else
                                <span class="badge bg-dark rounded-pill px-3">จำหน่ายแล้ว</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">ตรวจครั้งถัดไป</td>
                        <td>
                            @if($tool->next_inspection_date)
                                @if($tool->next_inspection_date->isPast())
                                    <span class="badge bg-danger rounded-pill"><i class="bi bi-exclamation-triangle"></i> เกินกำหนด</span>
                                    <span class="text-danger small">{{ $tool->next_inspection_date->format('d/m/Y') }}</span>
                                @else
                                    {{ $tool->next_inspection_date->format('d/m/Y') }}
                                @endif
                            @else
                                <span class="badge bg-info rounded-pill">ยังไม่เคยตรวจ</span>
                            @endif
                        </td>
                    </tr>
                    @if($tool->note)
                    <tr>
                        <td class="text-muted">หมายเหตุ</td>
                        <td>{{ $tool->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('tool-inspections.create', ['tool_id' => $tool->id]) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-clipboard-check"></i> ตรวจสอบ
                </a>
                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'safety_officer']))
                <a href="{{ route('tools.edit', $tool) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> แก้ไข
                </a>
                @endif
                <a href="{{ route('tools.qr', $tool) }}" class="btn btn-dark btn-sm" target="_blank">
                    <i class="bi bi-qr-code"></i> QR Code
                </a>
                <a href="{{ route('tools.index') }}" class="btn btn-secondary btn-sm ms-auto">
                    <i class="bi bi-arrow-left"></i> กลับ
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <!-- Inspection History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> ประวัติการตรวจสอบ</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3">เลขที่</th>
                                <th class="border-0">วันที่ตรวจ</th>
                                <th class="border-0">ผู้ตรวจ</th>
                                <th class="border-0">ผลตรวจ</th>
                                <th class="border-0 text-end pe-3">ดู</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tool->inspections->sortByDesc('inspected_at') as $insp)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold small d-block">{{ $insp->inspection_no }}</span>
                                    @if($insp->inspection_type == 'pre_work')
                                        <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">ก่อนเริ่มงาน</span>
                                    @else
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">ประจำเดือน</span>
                                    @endif
                                </td>
                                <td>{{ $insp->inspected_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $insp->inspectedBy->name ?? '-' }}</td>
                                <td>
                                    @if($insp->overall_result == 'pass')
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i> ผ่าน</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle"></i> ไม่ผ่าน</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('tool-inspections.show', $insp->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard display-4 text-secondary opacity-25 d-block mb-3"></i>
                                    <span>ยังไม่มีประวัติการตรวจสอบ</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
