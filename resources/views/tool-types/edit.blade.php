@extends('layouts.app')

@section('page_title', 'แก้ไขประเภท: ' . $toolType->name)

@section('content')
<div class="row">
    <!-- Left: Edit Type Info -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>ข้อมูลประเภท</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tool-types.update', $toolType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อประเภท <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $toolType->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" class="form-control bg-light" value="{{ $toolType->slug }}" readonly disabled>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">ไอคอน</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi {{ $toolType->icon }}"></i></span>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon', $toolType->icon) }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">สี</label>
                            <select name="color" class="form-select">
                                @foreach(['primary'=>'🔵 Primary','success'=>'🟢 Success','danger'=>'🔴 Danger','warning'=>'🟡 Warning','info'=>'🔵 Info','secondary'=>'⚪ Secondary','dark'=>'⚫ Dark'] as $val => $label)
                                <option value="{{ $val }}" {{ old('color', $toolType->color) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $toolType->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">เปิดใช้งาน</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tool-types.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> กลับ
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save"></i> บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Checklist Items -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-check text-success me-2"></i>รายการตรวจสอบ ({{ $toolType->checklistItems->count() }})</h5>
                <button class="btn btn-sm btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#addItemForm">
                    <i class="bi bi-plus-lg"></i> เพิ่มรายการ
                </button>
            </div>

            <!-- Add New Item Form -->
            <div class="collapse" id="addItemForm">
                <div class="card-body bg-light border-bottom">
                    <form action="{{ route('tool-types.checklist.store', $toolType) }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">รหัส</label>
                                <input type="text" name="item_code" class="form-control form-control-sm" required placeholder="เช่น ED-001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">หมวดหมู่</label>
                                <input type="text" name="category" class="form-control form-control-sm" required placeholder="เช่น สภาพทั่วไป">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">รายการตรวจ</label>
                                <input type="text" name="item_name" class="form-control form-control-sm" required placeholder="รายละเอียดการตรวจ">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-plus"></i> เพิ่ม
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-3" width="5%">#</th>
                                <th class="border-0" width="10%">รหัส</th>
                                <th class="border-0" width="15%">หมวดหมู่</th>
                                <th class="border-0">รายการตรวจ</th>
                                <th class="border-0 text-center" width="8%">สถานะ</th>
                                <th class="border-0 text-end pe-3" width="12%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($toolType->checklistItems as $idx => $item)
                            <tr id="item-row-{{ $item->id }}">
                                <td class="ps-3">{{ $idx + 1 }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->item_code }}</span></td>
                                <td><span class="badge bg-light text-secondary">{{ $item->category }}</span></td>
                                <td class="small">{{ $item->item_name }}</td>
                                <td class="text-center">
                                    @if($item->is_active)
                                        <span class="badge bg-success rounded-pill">เปิด</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">ปิด</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('tool-types.checklist.destroy', [$toolType, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('ลบรายการ {{ $item->item_code }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-list-check display-6 opacity-25 d-block mb-2"></i>
                                    ยังไม่มีรายการตรวจสอบ คลิก "เพิ่มรายการ" เพื่อเริ่มต้น
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($toolType->checklistItems as $item)
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tool-types.checklist.update', [$toolType, $item]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">แก้ไขรายการ {{ $item->item_code }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัส</label>
                        <input type="text" name="item_code" class="form-control" value="{{ $item->item_code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">หมวดหมู่</label>
                        <input type="text" name="category" class="form-control" value="{{ $item->category }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รายการตรวจ</label>
                        <textarea name="item_name" class="form-control" rows="3" required>{{ $item->item_name }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="itemActive{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="itemActive{{ $item->id }}">เปิดใช้งาน</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
