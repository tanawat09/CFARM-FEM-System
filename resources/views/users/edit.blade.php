@extends('layouts.app')

@section('page_title', 'แก้ไขข้อมูลผู้ใช้')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-gear text-primary me-2"></i> แบบฟอร์มอัปเดตข้อมูลผู้ใช้งาน</h5>
                <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2 fw-normal">{{ $user->status == 'active' ? 'กำลังใช้งาน (Active)' : 'ระงับการใช้งาน (Inactive)' }}</span>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ชื่อ - นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">อีเมล (ใช้สำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4 border border-warning shadow-sm">
                        <small class="text-warning text-dark fw-bold d-block mb-2"><i class="bi bi-info-circle-fill me-1 text-warning"></i> อัปเดตรหัสผ่าน (กรณีลืมรหัสผ่านหรือต้องการเปลี่ยน)</small>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">รหัสผ่านใหม่ (ไม่ต้องกรอกถ้าใช้เดิม)</label>
                                <input type="password" name="password" class="form-control border-light @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation" class="form-control border-light" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom text-secondary">ข้อมูลเพิ่มเติม และ สิทธิ์การใช้งาน</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">สิทธิ์การใช้งาน <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="safety_officer" {{ old('role', $user->role) == 'safety_officer' ? 'selected' : '' }}>จป. ตรวจสอบ</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">สถานะบัญชี <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>ปกติ (Active)</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>ระงับชั่วคราว (Inactive)</option>
                            </select>
                            @if(auth()->id() == $user->id)
                                <input type="hidden" name="status" value="active">
                            @endif
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อ</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">รหัสพนักงาน (Employee ID)</label>
                            <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $user->employee_id) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">สังกัดแผนก / ฝ่าย</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-5 border-top pt-4">
                        <div>
                            @if(auth()->id() != $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้งานท่านนี้อย่างถาวร?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-lg px-4 rounded-pill"><i class="bi bi-trash"></i> ลบบัญชีผู้ใช้</button>
                                </form>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกการเปลี่ยนแปลง</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
