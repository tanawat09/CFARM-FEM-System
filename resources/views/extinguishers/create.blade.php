@extends('layouts.app')

@section('page_title', 'เพิ่มถังดับเพลิงใหม่')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('extinguishers.store') }}" method="POST">
            @csrf
            
            <h5 class="fw-bold mb-4 border-bottom pb-2">ข้อมูลทั่วไป</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">หมายเลขซีเรียล (S/N) <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" required>
                    @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ประเภท <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">-- เลือก --</option>
                        <option value="Dry_Chemical">Dry Chemical (ผงเคมีแห้ง)</option>
                        <option value="CO2">CO2 (คาร์บอนไดออกไซด์)</option>
                        <option value="Foam">Foam (โฟม)</option>
                        <option value="Water">Water (น้ำ)</option>
                        <option value="Clean_Agent">Clean Agent (สารสะอาด)</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ขนาด (น้ำหนัก) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size') }}" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">หน่วย <span class="text-danger">*</span></label>
                    <select name="size_unit" class="form-select @error('size_unit') is-invalid @enderror" required>
                        <option value="lbs">lbs (ปอนด์)</option>
                        <option value="kg">kg (กิโลกรัม)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">ยี่ห้อ (Brand) <span class="text-danger">*</span></label>
                    <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">รุ่น (Model) <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" required>
                </div>
            </div>

            <h5 class="fw-bold mb-4 border-bottom pb-2">วันที่ผลิตและเริ่มใช้งาน</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">วันที่ผลิต <span class="text-danger">*</span></label>
                    <input type="date" name="manufacture_date" class="form-control @error('manufacture_date') is-invalid @enderror" value="{{ old('manufacture_date') }}" required>
                    <div class="form-text text-muted">วันหมดอายุจะถูกคำนวณอัตโนมัติ (+5 ปี)</div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">วันที่ติดตั้ง / วันเริ่มอายุ <span class="text-danger">*</span></label>
                    <input type="date" name="install_date" class="form-control @error('install_date') is-invalid @enderror" value="{{ old('install_date') }}" required>
                </div>
            </div>

            <h5 class="fw-bold mb-4 border-bottom pb-2">สถานที่ติดตั้ง</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">พื้นที่ติดตั้ง (ฟาร์ม) <span class="text-danger">*</span></label>
                    <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                        <option value="">-- เลือกพื้นที่ติดตั้ง --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->location_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">เล้า (House)</label>
                    <select name="house" class="form-select @error('house') is-invalid @enderror">
                        <option value="">-- ไม่ระบุ --</option>
                        @for($i = 1; $i <= 20; $i++)
                            <option value="เล้า {{ $i }}" {{ old('house') == "เล้า $i" ? 'selected' : '' }}>เล้า {{ $i }}</option>
                        @endfor
                        <option value="อื่นๆ" {{ old('house') == "อื่นๆ" ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">โซนติดตั้ง</label>
                    <input type="text" name="zone" class="form-control @error('zone') is-invalid @enderror" value="{{ old('zone') }}" placeholder="เช่น โซนหน้า, หอพักพนักงาน, อาคารสนง.">
                    @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-12 mt-4">
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-save me-2"></i> บันทึกข้อมูล</button>
                    <a href="{{ route('extinguishers.index') }}" class="btn btn-light btn-lg px-4 ms-2">ยกเลิก</a>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection
