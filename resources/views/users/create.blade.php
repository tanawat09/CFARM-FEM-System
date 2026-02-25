@extends('layouts.app')

@section('page_title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus text-primary me-2"></i> สร้างบัญชีผู้ใช้งานระบบใหม่</h5>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ชื่อ - นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">อีเมล (ใช้สำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                            <small class="text-muted d-block mt-1">ตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร</small>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom text-secondary">ข้อมูลเพิ่มเติมสำหรับการปฏิบัติงาน</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">สิทธิ์การใช้งาน (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="safety_officer" {{ old('role') == 'safety_officer' ? 'selected' : '' }}>จป. ตรวจสอบ (จัดการเฉพาะตรวจถัง)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อ</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">รหัสพนักงาน (Employee ID)</label>
                            <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">สังกัดแผนก / ฝ่าย (Department)</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5 border-top pt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกผู้ใช้งาน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
