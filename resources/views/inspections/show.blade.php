@extends('layouts.app')

@section('page_title', 'รายละเอียดการตรวจเช็ค')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">ใบรายงานการตรวจที่ #{{ $inspection->inspection_no }}</h5>
                <a href="{{ route('inspections.index') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <div class="row mb-5 bg-light p-4 rounded-4 align-items-center">
                    <div class="col-md-6 border-end-md">
                        <span class="text-muted small fw-medium d-block text-uppercase mb-1">อุปกรณ์ (Extinguisher)</span>
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-shield-shaded text-primary me-2 fs-4"></i>
                            <h4 class="mb-0 fw-bold text-dark">S/N: {{ $inspection->fireExtinguisher->serial_number ?? 'N/A' }}</h4>
                        </div>
                        <span class="text-secondary ms-4 ps-2"><i class="bi bi-geo-alt"></i> {{ $inspection->fireExtinguisher->location->location_name ?? '-' }} (ชั้น {{ $inspection->fireExtinguisher->location->floor ?? '-' }})</span>
                    </div>
                    
                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <span class="text-muted small fw-medium d-block mb-1">ผลประเมินรวม</span>
                                @if($inspection->overall_result == 'pass')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-bold"><i class="bi bi-check-circle-fill me-1"></i> ปกติ (PASSED)</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold"><i class="bi bi-x-circle-fill me-1"></i> ชำรุด (FAILED)</span>
                                @endif
                            </div>
                            <div class="col-6 mb-3">
                                <span class="text-muted small fw-medium d-block mb-1">ผู้ตรวจ (Inspector)</span>
                                <span class="fw-semibold text-dark">{{ $inspection->inspectedBy->name ?? 'SYSTEM' }}</span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted small fw-medium d-block mb-1">วันเวลาที่บันทึก</span>
                                <span class="fw-semibold text-dark"><i class="bi bi-calendar-event text-secondary me-1"></i> {{ \Carbon\Carbon::parse($inspection->inspected_at)->format('d F Y พ.ศ. ') }}{{ \Carbon\Carbon::parse($inspection->inspected_at)->addYears(543)->format('Y เวลา H:i น.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 border-bottom pb-2">รายการตรวจเช็ค (Checklist)</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table-striped align-middle rounded-3 overflow-hidden">
                        <thead class="bg-dark text-white text-center">
                            <tr>
                                <th class="py-3 fw-medium">รหัสรายการ</th>
                                <th class="py-3 text-start fw-medium">หัวข้อการตรวจ</th>
                                <th class="py-3 fw-medium">ผลการตรวจ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inspection->inspectionItems as $item)
                            <tr>
                                <td class="text-center text-muted fw-mono text-sm py-3">{{ collect(explode('-', $item->item_code))->last() }}</td>
                                <td class="py-3">{{ $item->item_name }}</td>
                                <td class="text-center py-3">
                                    @if($item->result == 'ok')
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    @elseif($item->result == 'not_ok')
                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    @else
                                        <i class="bi bi-dash-circle text-secondary fs-5"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($inspection->remark)
                <h6 class="fw-bold mb-3">หมายเหตุ / ข้อเสนอแนะ</h6>
                <div class="alert alert-light border shadow-sm p-4 text-dark fst-italic">
                    " {{ $inspection->remark }} "
                </div>
                @endif
                
            </div>
            <div class="card-footer bg-light p-3 text-center d-print-none">
                <button onclick="window.print()" class="btn btn-secondary rounded-pill px-4"><i class="bi bi-printer me-1"></i> พิมพ์รายงานนี้</button>
            </div>
        </div>
    </div>
</div>
@endsection
