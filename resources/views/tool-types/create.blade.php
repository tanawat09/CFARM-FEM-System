@extends('layouts.app')

@section('page_title', 'เพิ่มประเภทเครื่องมือ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>เพิ่มประเภทเครื่องมือใหม่</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tool-types.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อประเภท <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="เช่น สว่านมือไฟฟ้า">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug (อัตโนมัติ)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="ถ้าไม่กรอกจะสร้างจากชื่อ">
                        <small class="text-muted">ใช้เป็นตัวอ้างอิงในระบบ เช่น electric_drill</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ไอคอน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-wrench"></i></span>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon', 'bi-wrench') }}" placeholder="เช่น bi-wrench">
                            </div>
                            <small class="text-muted">ใช้ class ของ <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">สี Badge</label>
                            <select name="color" class="form-select">
                                <option value="primary" {{ old('color') == 'primary' ? 'selected' : '' }}>🔵 Primary</option>
                                <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>🟢 Success</option>
                                <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>🔴 Danger</option>
                                <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>🟡 Warning</option>
                                <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>🔵 Info</option>
                                <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>⚪ Secondary</option>
                                <option value="dark" {{ old('color') == 'dark' ? 'selected' : '' }}>⚫ Dark</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label fw-semibold" for="isActive">เปิดใช้งาน</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tool-types.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            หลังจากเพิ่มประเภทแล้ว สามารถจัดการรายการตรวจสอบ (Checklist) ได้ที่หน้าแก้ไข
        </div>
    </div>
</div>
@endsection
