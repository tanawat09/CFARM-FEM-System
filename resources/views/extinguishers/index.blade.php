@extends('layouts.app')

@section('page_title', 'รายการถังดับเพลิง')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-3 d-flex justify-content-between align-items-center">
        <!-- Search -->
        <form class="d-flex" action="{{ route('extinguishers.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาหมายเลขซีเรียล (S/N)" value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <!-- Action -->
        <div>
            <!-- Import will be implemented later -->
            <!-- <button class="btn btn-outline-success border-2 me-2"><i class="bi bi-file-excel"></i> Import</button> -->
            <a href="{{ route('extinguishers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> เพิ่มถังดับเพลิง</a>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 border-0">หมายเลขซีเรียล (S/N)</th>
                        <th class="border-0">พื้นที่ติดตั้ง</th>
                        <th class="border-0">ประเภท / ขนาด</th>
                        <th class="border-0">ยี่ห้อ / รุ่น</th>
                        <th class="border-0">วันที่เริ่มใช้งาน</th>
                        <th class="border-0">สถานะ</th>
                        <th class="text-end pe-3 border-0">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extinguishers as $ext)
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold fs-6">{{ $ext->serial_number }}</span>
                        </td>
                        <td>
                            <span class="fw-medium text-dark"><i class="bi bi-pin-map text-danger"></i> {{ $ext->location->location_name ?? '-' }}</span><br>
                            @if($ext->house || $ext->zone)
                            <div class="mt-1">
                                @if($ext->house)
                                <span class="badge bg-light text-dark border fw-normal me-1"><i class="bi bi-house text-secondary me-1"></i>{{ $ext->house }}</span>
                                @endif
                                @if($ext->zone)
                                <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-signpost-split text-secondary me-1"></i>โซน: {{ $ext->zone }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-muted small opacity-50">- ไม่ได้ระบุตำแหน่งย่อย -</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary px-2 py-1 mb-1">{{ str_replace('_', ' ', $ext->type) }}</span><br>
                            <span class="small text-muted fw-bold"><i class="bi bi-box-seam"></i> {{ (float)$ext->size }} {{ $ext->size_unit }}</span>
                        </td>
                        <td>
                            <span class="d-block fw-bold text-dark">{{ $ext->brand }}</span>
                            <span class="small text-muted"><i class="bi bi-tag"></i> {{ $ext->model }}</span>
                        </td>
                        <td>
                            <span class="fw-medium text-primary">{{ $ext->install_date ? \Carbon\Carbon::parse($ext->install_date)->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            @if($ext->status == 'active')
                                <span class="badge bg-success rounded-pill px-3">พร้อมใช้งาน</span>
                            @elseif($ext->status == 'under_repair')
                                <span class="badge bg-warning text-dark rounded-pill px-3">ส่งซ่อม</span>
                            @elseif($ext->status == 'damage')
                                <span class="badge bg-danger rounded-pill px-3">ชำรุด</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">จำหน่าย</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('extinguishers.qr', $ext->id) }}" class="btn btn-sm btn-outline-dark" title="พิมพ์ QR Code" target="_blank">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                <a href="{{ route('extinguishers.show', $ext->id) }}" class="btn btn-sm btn-outline-info" title="ดูประวัติ">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('extinguishers.edit', $ext->id) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x display-4 text-secondary opacity-25 d-block mb-3"></i>
                            <span class="fs-5">ไม่พบข้อมูลถังดับเพลิงในระบบ</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if(isset($extinguishers) && $extinguishers->lastPage() > 1)
    <div class="card-footer bg-white pt-3 pb-1">
        {{ $extinguishers->links() }}
    </div>
    @endif
</div>
@endsection
