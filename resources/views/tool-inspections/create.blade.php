@extends('layouts.mobile')

@section('page_title', ($inspectionType === 'pre_work' ? 'ตรวจสอบก่อนเริ่มงาน' : 'ตรวจสอบประจำเดือน') . ': ' . $tool->type_name)

@section('content')
<div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
    <div class="bg-{{ $inspectionType === 'pre_work' ? 'primary' : 'success' }} text-white p-3">
        <div class="d-flex align-items-center">
            <i class="bi {{ $inspectionType === 'pre_work' ? 'bi-clipboard2-check' : 'bi-calendar-check' }} display-6 me-3"></i>
            <div>
                <h5 class="mb-0 fw-bold">{{ $inspectionType === 'pre_work' ? 'ตรวจสอบก่อนเริ่มงาน' : 'ตรวจสอบประจำเดือน' }}</h5>
                <small>{{ $tool->tool_code }} | {{ $tool->type_name }}</small>
            </div>
        </div>
    </div>
    <div class="card-body p-3 bg-white">
        <div class="row text-sm">
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">ชื่อเครื่องมือ:</span>
                <span class="fw-semibold text-dark">{{ $tool->tool_name }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">พื้นที่:</span>
                <span class="fw-semibold text-dark">{{ $tool->location->location_name ?? 'N/A' }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">อาคาร/โซน:</span>
                <span class="fw-semibold text-dark">{{ $tool->house ?? '-' }} / {{ $tool->zone ?? '-' }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">ยี่ห้อ/รุ่น:</span>
                <span class="fw-semibold">{{ $tool->brand ?? '-' }} {{ $tool->model ? '/ '.$tool->model : '' }}</span>
            </div>
        </div>
    </div>
</div>

<form id="inspectionForm" action="{{ route('tool-inspections.store') }}" method="POST">
    @csrf
    <input type="hidden" name="tool_id" value="{{ $tool->id }}">
    <input type="hidden" name="inspection_type" value="{{ $inspectionType }}">
    
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h6 class="fw-bold mb-0 text-dark">รายการตรวจสอบ <span class="badge bg-primary ms-1" id="progressBadge">0/{{ count($checklist) }}</span></h6>
    </div>

    @foreach($checklist as $index => $item)
    <div class="card border-0 shadow-sm mb-3 rounded-4 check-item-card">
        <div class="card-body p-3">
            <input type="hidden" name="items[{{ $index }}][code]" value="{{ $item['code'] }}">
            <input type="hidden" name="items[{{ $index }}][item]" value="{{ $item['item'] }}">
            <input type="hidden" name="items[{{ $index }}][category]" value="{{ $item['category'] }}">
            
            <p class="mb-1"><span class="badge bg-light text-secondary me-1 small">{{ $item['category'] }}</span></p>
            <p class="mb-3 fw-medium text-dark"><span class="badge bg-light text-secondary me-2">{{ $index + 1 }}</span> {{ $item['item'] }}</p>
            
            <div class="d-flex justify-content-between gap-2">
                <div class="flex-fill">
                    <input type="radio" class="btn-check inspection-check" name="results[{{ $item['code'] }}]" id="ok_{{ $item['code'] }}" value="ok" required data-item-idx="{{ $index }}" autocomplete="off">
                    <label class="btn btn-outline-success w-100 rounded-pill py-2 text-wrap" for="ok_{{ $item['code'] }}">
                        <i class="bi bi-check-circle fs-5 d-block mb-1"></i> 
                        <span class="small fw-bold">ปกติ</span>
                    </label>
                </div>

                <div class="flex-fill">
                    <input type="radio" class="btn-check inspection-check" name="results[{{ $item['code'] }}]" id="not_ok_{{ $item['code'] }}" value="not_ok" data-item-idx="{{ $index }}" autocomplete="off">
                    <label class="btn btn-outline-danger w-100 rounded-pill py-2 text-wrap" for="not_ok_{{ $item['code'] }}">
                        <i class="bi bi-x-circle fs-5 d-block mb-1"></i> 
                        <span class="small fw-bold">ไม่ผ่าน</span>
                    </label>
                </div>
                
                <div class="flex-fill">
                    <input type="radio" class="btn-check inspection-check" name="results[{{ $item['code'] }}]" id="na_{{ $item['code'] }}" value="na" data-item-idx="{{ $index }}" autocomplete="off">
                    <label class="btn btn-outline-secondary w-100 rounded-pill py-2 text-wrap" for="na_{{ $item['code'] }}">
                        <i class="bi bi-dash-circle fs-5 d-block mb-1"></i> 
                        <span class="small fw-bold">N/A</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="card border-0 shadow-sm mb-5 pb-4 rounded-4">
         <div class="card-body p-3">
             <label class="form-label fw-bold">หมายเหตุ (ถ้ามี)</label>
             <textarea name="remark" class="form-control rounded-3" rows="3" placeholder="ระบุสิ่งที่พบเจอเพิ่มเติม..."></textarea>
         </div>
    </div>

    <!-- Floating Action Button Area -->
    <div class="floating-action">
        <button type="submit" class="btn btn-{{ $inspectionType === 'pre_work' ? 'primary' : 'success' }} w-100 py-3 rounded-pill fw-bold shadow-lg" id="submitBtn" disabled>
            <i class="bi bi-save me-2"></i> บันทึกผลการตรวจสอบ
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const totalItems = {{ count($checklist) }};
        
        $('.inspection-check').change(function() {
            const card = $(this).closest('.check-item-card');
            if($(this).val() === 'ok' || $(this).val() === 'na') {
                card.removeClass('border-danger border-opacity-50 border-2').addClass('border-success border-opacity-25 border-1');
            } else {
                card.removeClass('border-success border-opacity-25 border-1').addClass('border-danger border-opacity-50 border-2');
            }
            
            let checkedItems = new Set();
            $('.inspection-check:checked').each(function() {
                checkedItems.add($(this).data('item-idx'));
            });
            
            let checkedCount = checkedItems.size;
            $('#progressBadge').text(`${checkedCount}/${totalItems}`);
            
            if (checkedCount === totalItems) {
                $('#submitBtn').prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
            }
        });
    });
</script>
@endsection
