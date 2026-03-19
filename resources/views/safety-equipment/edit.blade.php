@extends('layouts.app')

@section('page_title', 'แก้ไขอุปกรณ์ ' . $safetyEquipment->asset_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-{{ $safetyEquipment->type == 'emergency_light' ? 'lightbulb' : 'droplet' }} me-2 text-primary"></i>
                    แก้ไข{{ $safetyEquipment->type_name }} : {{ $safetyEquipment->asset_code }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('safety-equipment.update', $safetyEquipment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">รหัสทรัพย์สิน <span class="text-danger">*</span></label>
                            <input type="text" name="asset_code" class="form-control @error('asset_code') is-invalid @enderror" value="{{ old('asset_code', $safetyEquipment->asset_code) }}" required>
                            @error('asset_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">หมายเลขซีเรียล (S/N)</label>
                            <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $safetyEquipment->serial_number) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ยี่ห้อ</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand', $safetyEquipment->brand) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">รุ่น</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model', $safetyEquipment->model) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">พื้นที่ติดตั้ง <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">-- เลือกพื้นที่ --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id', $safetyEquipment->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">อาคาร</label>
                            <input type="text" name="house" class="form-control" value="{{ old('house', $safetyEquipment->house) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">โซน</label>
                            <input type="text" name="zone" class="form-control" value="{{ old('zone', $safetyEquipment->zone) }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">วันที่ติดตั้ง</label>
                            <input type="date" name="install_date" class="form-control" value="{{ old('install_date', $safetyEquipment->install_date ? $safetyEquipment->install_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $safetyEquipment->status) == 'active' ? 'selected' : '' }}>ใช้งานปกติ</option>
                                <option value="inactive" {{ old('status', $safetyEquipment->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                <option value="under_repair" {{ old('status', $safetyEquipment->status) == 'under_repair' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                                <option value="disposed" {{ old('status', $safetyEquipment->status) == 'disposed' ? 'selected' : '' }}>จำหน่ายแล้ว</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">หมายเหตุ</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $safetyEquipment->note) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('safety-equipment.show', $safetyEquipment) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
