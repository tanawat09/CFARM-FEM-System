@extends('layouts.app')

@section('page_title', 'แก้ไขข้อมูลถังดับเพลิง')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('extinguishers.update', $extinguisher->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h5 class="fw-bold mb-4 border-bottom pb-2">ข้อมูลทั่วไป</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">หมายเลขซีเรียล (S/N) <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number', $extinguisher->serial_number) }}" required>
                    @error('serial_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ประเภท <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">-- เลือก --</option>
                        <option value="Dry_Chemical" {{ old('type', $extinguisher->type) == 'Dry_Chemical' ? 'selected' : '' }}>Dry Chemical (ผงเคมีแห้ง)</option>
                        <option value="CO2" {{ old('type', $extinguisher->type) == 'CO2' ? 'selected' : '' }}>CO2 (คาร์บอนไดออกไซด์)</option>
                        <option value="Foam" {{ old('type', $extinguisher->type) == 'Foam' ? 'selected' : '' }}>Foam (โฟม)</option>
                        <option value="Water" {{ old('type', $extinguisher->type) == 'Water' ? 'selected' : '' }}>Water (น้ำ)</option>
                        <option value="Clean_Agent" {{ old('type', $extinguisher->type) == 'Clean_Agent' ? 'selected' : '' }}>Clean Agent (สารสะอาด)</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ขนาด (น้ำหนัก) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="size" class="form-control @error('size') is-invalid @enderror" value="{{ old('size', $extinguisher->size) }}" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">หน่วย <span class="text-danger">*</span></label>
                    <select name="size_unit" class="form-select @error('size_unit') is-invalid @enderror" required>
                        <option value="lbs" {{ old('size_unit', $extinguisher->size_unit) == 'lbs' ? 'selected' : '' }}>lbs (ปอนด์)</option>
                        <option value="kg" {{ old('size_unit', $extinguisher->size_unit) == 'kg' ? 'selected' : '' }}>kg (กิโลกรัม)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">ยี่ห้อ (Brand) <span class="text-danger">*</span></label>
                    <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand', $extinguisher->brand) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">รุ่น (Model) <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $extinguisher->model) }}" required>
                </div>
            </div>

            <h5 class="fw-bold mb-4 border-bottom pb-2">เริ่มใช้งาน</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">วันที่ติดตั้ง / วันเริ่มอายุ <span class="text-danger">*</span></label>
                    <input type="date" name="install_date" class="form-control @error('install_date') is-invalid @enderror" value="{{ old('install_date', $extinguisher->install_date ? \Carbon\Carbon::parse($extinguisher->install_date)->format('Y-m-d') : '') }}" required>
                </div>
            </div>

            <h5 class="fw-bold mb-4 border-bottom pb-2">สถานที่ติดตั้งและสถานะปัจจุบัน</h5>
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">พื้นที่ติดตั้ง (ฟาร์ม) <span class="text-danger">*</span></label>
                    <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                        <option value="">-- เลือกพื้นที่ติดตั้ง --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $extinguisher->location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">เล้า (House)</label>
                    <select name="house" class="form-select @error('house') is-invalid @enderror">
                        <option value="">-- ไม่ระบุ --</option>
                        @for($i = 1; $i <= 20; $i++)
                            <option value="เล้า {{ $i }}" {{ old('house', $extinguisher->house) == "เล้า $i" ? 'selected' : '' }}>เล้า {{ $i }}</option>
                        @endfor
                        <option value="อื่นๆ" {{ old('house', $extinguisher->house) == "อื่นๆ" ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">โซนติดตั้ง</label>
                    <input type="text" name="zone" class="form-control @error('zone') is-invalid @enderror" value="{{ old('zone', $extinguisher->zone) }}" placeholder="เช่น โซนหน้า, หอพักพนักงาน, อาคารสนง.">
                    @error('zone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label fw-semibold">สถานะระบบ <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $extinguisher->status) == 'active' ? 'selected' : '' }}>ปกติ (พร้อมใช้งาน)</option>
                        <option value="under_repair" {{ old('status', $extinguisher->status) == 'under_repair' ? 'selected' : '' }}>ส่งซ่อม (Under Repair)</option>
                        <option value="damage" {{ old('status', $extinguisher->status) == 'damage' ? 'selected' : '' }}>ชำรุดรอซ่อม (Damage)</option>
                        <option value="disposed" {{ old('status', $extinguisher->status) == 'disposed' ? 'selected' : '' }}>จำหน่ายออก/ทำลาย (Disposed)</option>
                    </select>
                </div>
                
                <div class="col-md-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-save me-2"></i> บันทึกการเปลี่ยนแปลง</button>
                    <a href="{{ route('extinguishers.index') }}" class="btn btn-light btn-lg px-4 ms-2">ยกเลิก</a>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection
