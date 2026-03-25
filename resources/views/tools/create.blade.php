@extends('layouts.app')

@section('page_title', 'เพิ่มเครื่องมือช่าง')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>เพิ่มเครื่องมือช่างใหม่</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tools.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ประเภทเครื่องมือ <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- เลือกประเภท --</option>
                                @foreach($toolTypes as $tt)
                                <option value="{{ $tt->slug }}" {{ old('type') == $tt->slug ? 'selected' : '' }}>{{ $tt->name }}</option>
                                @endforeach
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">รหัสเครื่องมือ <span class="text-danger">*</span></label>
                            <input type="text" name="tool_code" class="form-control @error('tool_code') is-invalid @enderror" value="{{ old('tool_code') }}" required>
                            @error('tool_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อเครื่องมือ <span class="text-danger">*</span></label>
                        <input type="text" name="tool_name" class="form-control @error('tool_name') is-invalid @enderror" value="{{ old('tool_name') }}" required>
                        @error('tool_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ยี่ห้อ</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">รุ่น</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">S/N</label>
                            <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">พื้นที่ติดตั้ง <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">-- เลือกพื้นที่ --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                                @endforeach
                            </select>
                            @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">อาคาร</label>
                            <input type="text" name="house" class="form-control" value="{{ old('house') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">โซน</label>
                            <input type="text" name="zone" class="form-control" value="{{ old('zone') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">วันที่ซื้อ</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">หมายเหตุ</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tools.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
