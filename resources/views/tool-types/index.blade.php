@extends('layouts.app')

@section('page_title', 'จัดการประเภทเครื่องมือช่าง')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-tags text-primary me-2"></i>ประเภทเครื่องมือช่าง</h5>
    <a href="{{ route('tool-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> เพิ่มประเภท
    </a>
</div>

<div class="row g-4">
    @forelse($toolTypes as $type)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-{{ $type->color }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi {{ $type->icon }} text-{{ $type->color }} fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $type->name }}</h6>
                        <small class="text-muted">{{ $type->slug }}</small>
                    </div>
                    @if(!$type->is_active)
                    <span class="badge bg-secondary ms-auto">ปิดใช้งาน</span>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-list-check text-primary"></i> {{ $type->checklist_items_count }} รายการตรวจ
                        </span>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('tool-types.edit', $type) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข & จัดการ Checklist">
                            <i class="bi bi-pencil"></i> แก้ไข
                        </a>
                        <form action="{{ route('tool-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบประเภท {{ $type->name }} ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 text-muted">
            <i class="bi bi-tags display-4 opacity-25 d-block mb-3"></i>
            <p class="fs-5">ยังไม่มีประเภทเครื่องมือ</p>
            <a href="{{ route('tool-types.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg"></i> เพิ่มประเภทแรก
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection
