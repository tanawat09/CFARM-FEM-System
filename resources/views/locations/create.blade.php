@extends('layouts.app')

@section('page_title', 'เพิ่มพื้นที่ติดตั้งใหม่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pin-map text-primary me-2"></i> สร้างจุดติดตั้งถังดับเพลิงใหม่</h5>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('locations.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">สถานที่ติดตั้ง <span class="text-danger">*</span></label>
                            <input type="text" name="location_name" class="form-control border-light shadow-sm @error('location_name') is-invalid @enderror" value="{{ old('location_name') }}" list="locationOptions" placeholder="เลือกสถานที่ติดตั้ง หรือพิมพ์เพิ่มใหม่ด้วยตัวเอง..." required autocomplete="off">
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
                    </div>

                    <div class="d-flex justify-content-end mt-5 border-top pt-4">
                        <a href="{{ route('locations.index') }}" class="btn btn-light btn-lg px-4 me-2 rounded-pill">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm"><i class="bi bi-save me-2"></i> บันทึกข้อมูลพื้นที่</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
