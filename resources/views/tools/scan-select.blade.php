@extends('layouts.app')

@section('page_title', 'เลือกประเภทการตรวจสอบ - ' . $tool->tool_name)

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 text-center">
                <div class="mb-3 d-inline-block bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                    <i class="bi bi-tools display-5"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $tool->tool_name }}</h4>
                <p class="text-muted mb-0">รหัส: {{ $tool->tool_code }} | ประเภท: {{ $tool->type_name }}</p>
                <div class="mt-2">
                    <span class="badge bg-light text-dark border"><i class="bi bi-pin-map text-danger"></i> {{ $tool->location->location_name ?? '-' }}</span>
                </div>
            </div>
            <div class="card-body p-4 p-md-5">
                <h5 class="text-center fw-bold mb-4">กรุณาเลือกประเภทการตรวจสอบ</h5>
                
                <div class="d-grid gap-3">
                    <!-- Pre-work Check Button -->
                    <a href="{{ route('tool-inspections.create', ['tool_id' => $tool->id, 'inspection_type' => 'pre_work']) }}" 
                       class="btn btn-outline-primary btn-lg p-4 text-start d-flex align-items-center rounded-4 custom-hover-card">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-clipboard2-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">ตรวจสอบก่อนเริ่มงาน</h5>
                            <small class="opacity-75">การตรวจสอบสภาพความพร้อมก่อนนำไปใช้งานประจำวัน (Pre-work Check)</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto fs-4 opacity-50"></i>
                    </a>

                    <!-- Monthly Check Button -->
                    <a href="{{ route('tool-inspections.create', ['tool_id' => $tool->id, 'inspection_type' => 'monthly']) }}" 
                       class="btn btn-outline-success btn-lg p-4 text-start d-flex align-items-center rounded-4 custom-hover-card">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">ตรวจสอบประจำเดือน</h5>
                            <small class="opacity-75">การตรวจสอบสภาพตามรอบเดือนโดยเจ้าหน้าที่ (Monthly Check)</small>
                        </div>
                        <i class="bi bi-chevron-right ms-auto fs-4 opacity-50"></i>
                    </a>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('tools.show', $tool) }}" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> ยกเลิก
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-hover-card {
    transition: all 0.2s ease;
    border-width: 2px;
}
.custom-hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important;
}
.btn-outline-primary:hover small { color: rgba(255,255,255,0.8) !important; }
.btn-outline-success:hover small { color: rgba(255,255,255,0.8) !important; }
</style>
@endsection
