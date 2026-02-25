@extends('layouts.app')

@section('page_title', 'อัปเดตสถานะแจ้งซ่อม')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">บันทึกซ่อมบำรุงที่ #{{ $repairLog->repair_no }}</h5>
                <a href="{{ route('repair-logs.index') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <div class="row mb-4">
                    <div class="col-md-7 d-flex align-items-center">
                        <div>
                            <span class="text-muted d-block small mb-1 fw-medium text-uppercase">อุปกรณ์ที่มีปัญหา</span>
                            <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-shaded text-primary me-2"></i> {{ $repairLog->fireExtinguisher->serial_number ?? 'N/A' }}</h4>
                            <span class="text-secondary"><i class="bi bi-geo-alt me-1 text-danger"></i> Location: {{ $repairLog->fireExtinguisher->location->location_name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-5 text-end">
                        <span class="text-muted small fw-medium text-uppercase d-block mb-1">สถานะปัจจุบัน</span>
                        @if($repairLog->status == 'pending')
                            <span class="badge bg-secondary px-4 py-2 rounded-pill fs-6 fw-normal">รอตรวจสอบ</span>
                        @elseif($repairLog->status == 'in_progress')
                            <span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 fw-medium"><i class="bi bi-tools me-1"></i> กำลังซ่อมแซม</span>
                        @else
                            <span class="badge bg-success px-4 py-2 rounded-pill fs-6 fw-normal"><i class="bi bi-check-all me-1"></i> ปิดงานแล้ว</span>
                        @endif
                    </div>
                </div>

                <div class="bg-light p-4 rounded-4 mb-4 border border-light shadow-sm">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> ปัญหาที่แจ้ง</h6>
                    <p class="text-dark fs-5 mb-0">{{ $repairLog->problem }}</p>
                    <div class="mt-3 text-muted small d-flex justify-content-between">
                        <span><i class="bi bi-person me-1"></i> {{ $repairLog->repairedBy->name ?? 'SYSTEM' }}</span>
                        <span><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($repairLog->created_at)->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <form action="{{ route('repair-logs.update', $repairLog->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-wrench-adjustable me-2"></i> บันทึกผลการดำเนินงาน</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">อัปเดตสถานะงาน <span class="text-danger">*</span></label>
                        <select id="status_select" name="status" class="form-select border-light shadow-sm" required {{ $repairLog->status == 'completed' ? 'disabled' : '' }}>
                            <option value="pending" {{ $repairLog->status == 'pending' ? 'selected' : '' }}>รอตรวจสอบ (Pending)</option>
                            <option value="in_progress" {{ $repairLog->status == 'in_progress' ? 'selected' : '' }}>กำลังซ่อมแซม (In Progress)</option>
                            <option value="completed" {{ $repairLog->status == 'completed' ? 'selected' : '' }}>ปิดงานซ่อม (Completed)</option>
                        </select>
                        @if($repairLog->status == 'completed')
                            <input type="hidden" name="status" value="completed">
                        @endif
                    </div>

                    <div class="mb-3" id="repair_result_div" style="display: none;">
                        <label class="form-label fw-bold text-dark">ผลการซ่อม (เมื่อเลือกปิดงาน)</label>
                        <select name="repair_result" class="form-select border-light shadow-sm bg-light">
                            <option value="repaired">ซ่อมแซมสำเร็จ (นำกลับมาใช้งานได้ตามปกติ)</option>
                            <option value="unrepairable">ชำรุด / ซ่อมไม่ได้ (แทงจำหน่ายถังนี้)</option>
                        </select>
                        <small class="text-danger d-block mt-1"><i class="bi bi-info-circle"></i> หากเลือก "ซ่อมไม่ได้" สถานะของถังจะเปลี่ยนเป็น "จำหน่าย" ทันที</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">สิ่งที่ได้ดำเนินการไป (รายละเอียดการซ่อม)</label>
                        <textarea name="action_taken" class="form-control border-light shadow-sm" rows="3" placeholder="ระบุการเปลี่ยนอะไหล่ หรือ วิธีการแก้ไข" {{ $repairLog->status == 'completed' ? 'readonly' : '' }}>{{ $repairLog->action_taken }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">ผู้ให้บริการ / ร้านซ่อม</label>
                            <input type="text" name="vendor_name" class="form-control border-light shadow-sm" value="{{ $repairLog->vendor_name }}" {{ $repairLog->status == 'completed' ? 'readonly' : '' }}>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label fw-bold text-dark">ค่าใช้จ่าย (บาท)</label>
                            <input type="number" step="0.01" name="repair_cost" class="form-control border-light shadow-sm" value="{{ $repairLog->repair_cost }}" {{ $repairLog->status == 'completed' ? 'readonly' : '' }}>
                        </div>
                    </div>
                    
                    @if($repairLog->status != 'completed')
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกการอัปเดต</button>
                    </div>
                    @else
                    <div class="alert {{ $repairLog->fireExtinguisher->status == 'disposed' ? 'alert-danger' : 'alert-success' }} d-flex align-items-center rounded-3 shadow-sm border-0 mb-0">
                        <i class="bi {{ $repairLog->fireExtinguisher->status == 'disposed' ? 'bi-x-circle-fill text-danger' : 'bi-check-circle-fill text-success' }} fs-4 me-3 mt-1"></i>
                        <div>
                            @if($repairLog->fireExtinguisher->status == 'disposed')
                                <strong class="d-block text-dark">งานซ่อมเสร็จสิ้น (ซ่อมไม่ได้ / แทงชำรุด)</strong>
                                <small class="text-dark">เมื่อวันที่ {{ \Carbon\Carbon::parse($repairLog->completed_date)->format('d/m/Y H:i') }} | สถานะถังถูกปรับเป็น "จำหน่าย"</small>
                            @else
                                <strong class="d-block text-dark">งานซ่อมเสร็จสมบูรณ์ร้อยแล้ว</strong>
                                <small class="text-dark">เมื่อวันที่ {{ \Carbon\Carbon::parse($repairLog->completed_date)->format('d/m/Y H:i') }} | สถานะถังกลับมาใช้งานตามปกติ</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status_select');
        const repairResultDiv = document.getElementById('repair_result_div');
        
        if (statusSelect) {
            function toggleRepairResult() {
                if (statusSelect.value === 'completed') {
                    repairResultDiv.style.display = 'block';
                } else {
                    repairResultDiv.style.display = 'none';
                }
            }
            
            statusSelect.addEventListener('change', toggleRepairResult);
            // initial check
            toggleRepairResult();
        }
    });
</script>
@endsection
