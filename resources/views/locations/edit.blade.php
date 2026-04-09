@extends('layouts.app')

@section('page_title', 'แก้ไขพื้นที่ติดตั้ง')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i> ปรับปรุงจุดติดตั้งถังดับเพลิง</h5>
                <span class="badge {{ $location->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2 fw-normal">{{ $location->is_active ? 'กำลังใช้งาน' : 'ปิดใช้งาน' }}</span>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">สถานที่ติดตั้ง <span class="text-danger">*</span></label>
                            <input type="text" name="location_name" class="form-control border-light shadow-sm @error('location_name') is-invalid @enderror" value="{{ old('location_name', $location->location_name) }}" list="locationOptions" placeholder="เลือกสถานที่ติดตั้ง หรือพิมพ์เพิ่มใหม่ด้วยตัวเอง..." required autocomplete="off">
                            <datalist id="locationOptions">
                                <option value="สำนักงานใหญ่">
                                <option value="ฟาร์มละหานทราย">
                                <option value="ฟาร์มศรีสุข">
                                <option value="ฟาร์มบ้านบาตร">
                                <option value="ฟาร์มโคกสนวน">
                                <option value="ฟาร์มหนองถนน">
                                <option value="ฟาร์มหนองบอน">
                                <option value="ฟาร์มนรินทร์">
                                <option value="ฟาร์มก้านเหลือง">
                            </datalist>
                            @error('location_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold">สถานะพื้นที่ <span class="text-danger">*</span></label>
                            <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" required>
                                <option value="1" {{ old('is_active', $location->is_active) ? 'selected' : '' }}>ปกติ (เปิดใช้งาน)</option>
                                <option value="0" {{ !old('is_active', $location->is_active) ? 'selected' : '' }}>ระงับชั่วคราว / ยกเลิกพื้นที่นี้ (ปิดใช้งาน)</option>
                            </select>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-bold">รูปภาพแผนผังอาคาร <span class="text-muted fw-normal">(ถ้ามี)</span></label>
                            @if($location->floor_plan_image)
                                <div class="mb-3">
                                    <img src="{{ $location->floor_plan_image_url }}" alt="Floor Plan" class="img-thumbnail" style="max-height: 200px;">
                                    <div class="form-text mt-1 text-success"><i class="bi bi-check-circle"></i> มีรูปแผนผังติดตั้งอยู่แล้ว หากต้องการเปลี่ยนให้อัปโหลดใหม่</div>
                                </div>
                            @endif
                            <input type="file" name="floor_plan_image" class="form-control @error('floor_plan_image') is-invalid @enderror" accept="image/*">
                            <div class="form-text text-muted">ใช้สำหรับระบบปักหมุดตำแหน่งถังดับเพลิงในเมนูแผนผัง (รองรับไฟล์ jpg, png ขนาดไม่เกิน 4MB)</div>
                            @error('floor_plan_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5 border-top pt-4">
                        <div>
                            <button type="button" class="btn btn-outline-danger btn-lg px-4 rounded-pill" onclick="if(confirm('การลบพื้นที่นี้ ถังดับเพลิงที่ติดอยู่กับพื้นที่นี้อาจได้รับผลกระทบ ยืนยันที่จะลบอย่างถาวรหรือไม่?')) { document.getElementById('delete-form-{{ $location->id }}').submit(); }"><i class="bi bi-trash"></i> ลบพื้นที่</button>
                        </div>
                        <div>
                            <a href="{{ route('locations.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </div>
                </form>
                
                <form id="delete-form-{{ $location->id }}" action="{{ route('locations.destroy', $location->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
