@extends('layouts.mobile')

@section('page_title', 'บันทึกการตรวจเช็ค')

@section('content')
<div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
    <div class="bg-primary text-white p-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-check display-6 me-3"></i>
            <div>
                <h5 class="mb-0 fw-bold">S/N: {{ $extinguisher->serial_number }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body p-3 bg-white">
        <div class="row text-sm">
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">พื้นที่:</span>
                <span class="fw-semibold text-dark">{{ $extinguisher->location->location_name ?? 'N/A' }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">โซน/ชั้น:</span>
                <span class="fw-semibold text-dark">{{ $extinguisher->location->floor ?? '-' }} / {{ $extinguisher->location->zone ?? '-' }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">ประเภท:</span>
                <span class="badge bg-secondary rounded-pill fw-normal">{{ str_replace('_', ' ', $extinguisher->type) }}</span>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block small">ขนาด:</span>
                <span class="fw-semibold">{{ $extinguisher->size }} {{ $extinguisher->size_unit }}</span>
            </div>
        </div>
    </div>
</div>

<form id="inspectionForm" action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="extinguisher_id" value="{{ $extinguisher->id }}">
    
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h6 class="fw-bold mb-0 text-dark">รายการตรวจเช็ค <span class="badge bg-primary ms-1" id="progressBadge">0/14</span></h6>
    </div>

    <!-- Toggle Checklist Area -->
    @foreach($checklist as $index => $item)
    <div class="card border-0 shadow-sm mb-3 rounded-4 check-item-card">
        <div class="card-body p-3">
            <input type="hidden" name="items[{{ $index }}][code]" value="{{ $item['code'] }}">
            <input type="hidden" name="items[{{ $index }}][item]" value="{{ $item['item'] }}">
            
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
                        <span class="small fw-bold">ชำรุด</span>
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

    <div class="card border-0 shadow-sm mb-4 rounded-4 mt-4">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-camera me-2 text-muted"></i> ถ่ายรูป (ทางเลือก)</h6>
            <div class="d-flex gap-2 mb-2 overflow-auto" id="previewArea" style="scroll-snap-type: x mandatory;">
                <!-- Add Image Button -->
                <div class="border rounded-4 d-flex justify-content-center align-items-center bg-light flex-shrink-0" id="addPhotoBtn" style="width: 80px; height: 80px; cursor: pointer; border-style: dashed !important;">
                    <i class="bi bi-plus fs-2 text-secondary"></i>
                </div>
            </div>
            <input type="file" name="photos[]" accept="image/*" capture="environment" class="d-none" id="cameraInput" multiple>
            <small class="text-muted d-block text-end">สูงสุด 5 รูป</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5 pb-4 rounded-4">
         <div class="card-body p-3">
             <label class="form-label fw-bold">หมายเหตุ (ถ้ามี)</label>
             <textarea name="remark" class="form-control rounded-3" rows="3" placeholder="ระบุสิ่งที่พบเจอเพิ่มเติม..."></textarea>
         </div>
    </div>

    <!-- Floating Action Button Area -->
    <div class="floating-action">
        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg" id="submitBtn" disabled>
            <i class="bi bi-save me-2"></i> บันทึกผลการตรวจ
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const totalItems = {{ count($checklist) }};
        
        $('.inspection-check').change(function() {
            // Add visual cue for selected card
            const card = $(this).closest('.check-item-card');
            if($(this).val() === 'ok' || $(this).val() === 'na') {
                card.removeClass('border-danger border-opacity-50 border-2').addClass('border-success border-opacity-25 border-1');
            } else {
                card.removeClass('border-success border-opacity-25 border-1').addClass('border-danger border-opacity-50 border-2');
            }
            
            // Calculate progress
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

        // Photo Upload Logic Simulation
        $('#addPhotoBtn').click(function() {
            $('#cameraInput').click();
        });

        $('#cameraInput').change(function(e) {
            let files = e.target.files;
            if(files.length > 5) {
                alert('อัพโหลดได้สูงสุด 5 รูป');
                return;
            }

            for(let i=0; i<files.length; i++) {
                 let file = files[i];
                 if(file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        let html = `
                            <div class="position-relative flex-shrink-0" style="scroll-snap-align: start;">
                                <img src="${event.target.result}" class="rounded-4 object-fit-cover shadow-sm" style="width: 80px; height: 80px;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle remove-photo" style="transform: translate(30%, -30%); width: 24px; height:24px; padding:0;">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                        $('#previewArea').prepend(html);
                    }
                    reader.readAsDataURL(file);
                 }
            }
        });

        $(document).on('click', '.remove-photo', function() {
            $(this).parent().remove();
            // Need to reset file input logic if implemented fully
        });
    });
</script>
@endsection
