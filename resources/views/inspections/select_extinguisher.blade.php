@extends('layouts.app')

@section('page_title', 'ระบุถังดับเพลิงที่ต้องการตรวจเช็ค')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-qr-code-scan text-primary me-2"></i> สแกนรับข้อมูลเพื่อเข้าตรวจ</h5>
            </div>
            <div class="card-body p-4 p-md-5 text-center">
                
                <div class="bg-light p-5 rounded-4 mb-4 border border-light">
                    <i class="bi bi-upc-scan display-1 text-muted opacity-50 mb-3 d-block"></i>
                    <p class="text-muted fw-bold mb-0">ระบบพร้อมใช้งาน</p>
                    <p class="small text-muted">กดปุ่มกล้องที่มือถือของคุณ เพื่อสแกน QR Code บนตัวถังได้เลย</p>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <hr class="flex-grow-1">
                    <span class="px-3 text-muted small fw-bold">หรือค้นหาด้วยตัวเอง</span>
                    <hr class="flex-grow-1">
                </div>

                <form action="{{ route('inspections.create') }}" method="GET">
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold">เลือกหมายเลขซีเรียล (ถังดับเพลิง) <span class="text-danger">*</span></label>
                        <select name="extinguisher_id" class="form-select form-select-lg rounded-pill shadow-sm" required>
                            <option value="">-- กรุณาเลือกหมายเลขซีเรียล --</option>
                            @foreach($extinguishers as $ext)
                                <option value="{{ $ext->id }}">{{ $ext->serial_number }} ({{ $ext->location->location_name ?? 'ไม่ระบุพื้นที่' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('inspections.index') }}" class="btn btn-light btn-lg flex-fill rounded-pill">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary btn-lg flex-fill rounded-pill shadow-sm"><i class="bi bi-check2-circle me-1"></i> เริ่มการตรวจเช็ค</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
