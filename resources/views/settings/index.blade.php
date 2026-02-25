@extends('layouts.app')

@section('page_title', 'การตั้งค่าระบบ (System Settings)')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-gear-fill text-muted me-2"></i> กำหนดค่าเงื่อนไขของระบบ</h5>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-primary">ข้อมูลองค์กรและทั่วไป</h6>
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">ชื่อองค์กร / บริษัท</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? 'CFARM FEM System') }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-5 mb-3 pb-2 border-bottom text-primary">การคำนวณวงรอบแจ้งเตือน</h6>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ความถี่ในการตรวจเช็ค (วัน)</label>
                            <div class="input-group">
                                <input type="number" name="inspection_interval_days" class="form-control form-control-lg text-center fw-bold" value="{{ old('inspection_interval_days', $settings['inspection_interval_days'] ?? 30) }}" required min="1">
                                <span class="input-group-text bg-light">วัน</span>
                            </div>
                            <small class="text-muted d-block mt-1">ใช้คำนวณวันกำหนดตรวจครั้งถัดไป</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">รอบการเติมสารเคมี/ทดสอบ (เดือน)</label>
                            <div class="input-group">
                                <input type="number" name="refill_interval_months" class="form-control form-control-lg text-center fw-bold" value="{{ old('refill_interval_months', $settings['refill_interval_months'] ?? 60) }}" required min="1">
                                <span class="input-group-text bg-light">เดือน</span>
                            </div>
                            <small class="text-muted d-block mt-1">มักจะเป็น 60 เดือน (5 ปี) สำหรับชนิดผงเคมีแห้ง</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">จำนวนปีที่ถือว่า "หมดอายุ" (ปี)</label>
                            <div class="input-group">
                                <input type="number" name="expire_years" class="form-control form-control-lg text-center fw-bold text-danger" value="{{ old('expire_years', $settings['expire_years'] ?? 5) }}" required min="1">
                                <span class="input-group-text bg-light">ปี</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">แจ้งเตือนล่วงหน้าก่อนหมดอายุ (วัน)</label>
                            <div class="input-group">
                                <input type="number" name="warning_days_before" class="form-control form-control-lg text-center fw-bold text-warning" value="{{ old('warning_days_before', $settings['warning_days_before'] ?? 30) }}" required min="1">
                                <span class="input-group-text bg-light">วัน</span>
                            </div>
                            <small class="text-muted d-block mt-1">เพื่อให้มีเวลาสั่งซื้อก่อนถึงกำหนดหมดอายุ</small>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-5 mb-3 pb-2 border-bottom text-info"><i class="bi bi-telegram me-2"></i> การเชื่อมต่อแจ้งเตือน (Telegram)</h6>
                    <div class="row mb-5 pb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telegram Bot Token</label>
                            <input type="text" name="telegram_bot_token" class="form-control" value="{{ old('telegram_bot_token', $settings['telegram_bot_token'] ?? '') }}" placeholder="123456789:ABCdefGHIjklMNOpqrSTUvwxyz...">
                            <small class="text-muted d-block mt-2">รับ Token จากการสร้าง Bot ผ่าน BotFather ใน Telegram</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telegram Chat ID</label>
                            <input type="text" name="telegram_chat_id" class="form-control" value="{{ old('telegram_chat_id', $settings['telegram_chat_id'] ?? '') }}" placeholder="เช่น -1001234567890">
                            <small class="text-muted d-block mt-2">ระบุ Chat ID ของกลุ่ม หรือบุคคลที่ต้องการส่งแจ้งเตือนไปหา</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกและนำไปใช้</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
