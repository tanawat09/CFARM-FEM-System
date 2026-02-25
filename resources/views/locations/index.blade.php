@extends('layouts.app')

@section('page_title', 'พื้นที่ติดตั้ง (Locations)')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
        <!-- Search -->
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control rounded-start-pill border-end-0 bg-light" placeholder="ค้นหาพื้นที่...">
            <button class="btn btn-outline-secondary border-start-0 rounded-end-pill bg-light" type="button"><i class="bi bi-search text-muted"></i></button>
        </div>

        <!-- Action -->
        <div>
            <a href="{{ route('locations.create') }}" class="btn btn-primary rounded-pill fw-medium shadow-sm"><i class="bi bi-plus-lg me-1"></i> เพิ่มพื้นที่ใหม่</a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3 fw-medium small text-uppercase" width="15%">รหัสพื้นที่</th>
                        <th class="py-3 fw-medium small text-uppercase" width="45%">ชื่อพื้นที่ติดตั้ง</th>
                        <th class="py-3 fw-medium small text-uppercase text-center" width="20%">สถานะ</th>
                        <th class="pe-4 py-3 fw-medium small text-uppercase text-end" width="20%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $loc)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3 fw-semibold text-dark">
                            {{ $loc->location_code }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill text-danger me-2 bg-danger bg-opacity-10 p-2 rounded-circle fs-5"></i>
                                <span class="fw-medium text-dark">{{ $loc->location_name }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                             @if($loc->is_active)
                                 <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">ใช้งาน</span>
                             @else
                                 <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill">ปิดใช้งาน</span>
                             @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('locations.edit', $loc->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"><i class="bi bi-pencil-square"></i> แก้ไข</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-building display-4 d-block mb-3 opacity-25"></i>
                            <h5 class="fw-normal">ยังไม่มีข้อมูลพื้นที่ติดตั้งในระบบ</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(isset($locations) && $locations->hasPages())
    <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-center">
        {{ $locations->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
