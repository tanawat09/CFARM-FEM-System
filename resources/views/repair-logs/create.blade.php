@extends('layouts.app')

@section('page_title', 'บันทึกแจ้งซ่อม')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-danger text-white p-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-wrench-adjustable-circle display-5 me-3 opacity-75"></i>
                    <div>
                        <h4 class="mb-0 fw-bold">สร้างใบบันทึกแจ้งซ่อมใหม่ (Repair Log)</h4>
                        <p class="mb-0 fw-light">ระบุรายละเอียดปัญหาเพื่อส่งต่อให้ผู้รับผิดชอบดำเนินการแก้ไข</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4 p-md-5 bg-white">
                <!-- Related Extinguisher Info -->
                @if($extinguisher)
                <div class="bg-light p-3 rounded-3 mb-4 d-flex align-items-center border border-light shadow-sm">
                    <span class="bg-white p-2 border rounded shadow-sm text-dark me-3"><i class="bi bi-shield-shaded fs-3"></i></span>
                    <div>
                        <span class="text-secondary small fw-medium text-uppercase d-block mb-1">อุปกรณ์ที่แจ้งปัญหา</span>
                        <h5 class="fw-bold text-dark mb-0">S/N: {{ $extinguisher->serial_number }}</h5>
                        <div class="mt-1 text-muted small"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $extinguisher->location->location_name ?? 'N/A' }}</div>
                    </div>
                </div>
                @endif

                <form action="{{ route('repair-logs.store') }}" method="POST">
                    @csrf
                    @if($extinguisher)
                        <input type="hidden" name="extinguisher_id" value="{{ $extinguisher->id }}">
                    @else
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">ระบุถังดับเพลิงที่มีปัญหา <span class="text-danger">*</span></label>
                            <select name="extinguisher_id" class="form-select rounded-3 border-light shadow-sm" required>
                                <option value="">-- เลือกถังดับเพลิง --</option>
                                @foreach($extinguishers as $ext)
                                    <option value="{{ $ext->id }}">S/N: {{ $ext->serial_number }} ({{ $ext->location->location_name ?? 'ไม่ระบุพื้นที่' }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">อธิบายปัญหาที่พบ <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3 border-light shadow-sm" name="problem" rows="4" placeholder="เช่น สายฉีดขาด, สลักหลุดหาย, หน้าปัดความดันตก..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">บริษัท/ผู้รับเหมาที่จะส่งซ่อม (ถ้าทราบ)</label>
                        <input type="text" name="vendor_name" class="form-control rounded-3 py-2 border-light shadow-sm" placeholder="ระบุชื่อร้าน หรือ ปล่อยว่างไว้">
                    </div>

                    <div class="alert alert-warning border-0 rounded-3 shadow-sm d-flex align-items-start p-3 mb-4">
                        <i class="bi bi-info-circle-fill text-warning fs-4 me-3 mt-1"></i>
                        <div>
                            <strong class="text-dark d-block">หมายเหตุระบบ</strong>
                            <small class="text-dark opacity- ৭৫">การแจ้งซ่อมจะทำให้สถานะของถังดับเพลิงใบนี้เปลี่ยนเป็น "<span class="text-warning fw-bold">กำลังซ่อม</span>" โดยอัตโนมัติ และไม่สามารถนำไปสแกนตรวจเช็คได้จนกว่างานซ่อมจะ <span class="text-success fw-bold">ปิดงาน</span></small>
                        </div>
                    </div>

                    <hr class="border-light mb-4">

                    <div class="d-flex justify-content-end">
                        <a href="javascript:history.back()" class="btn btn-light btn-lg px-4 rounded-pill me-2 fw-medium text-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm fw-bold"><i class="bi bi-send-fill me-2"></i> ยืนยันแจ้งซ่อม</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
