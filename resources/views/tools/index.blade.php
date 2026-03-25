@extends('layouts.app')

@section('page_title', 'ตรวจสอบเครื่องมือช่าง')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form class="d-flex flex-wrap gap-2" action="{{ route('tools.index') }}" method="GET" id="searchForm">
            <select name="type" class="form-select" style="max-width: 200px;" onchange="document.getElementById('searchForm').submit()">
                <option value="">-- ทุกประเภท --</option>
                @foreach($toolTypes as $tt)
                <option value="{{ $tt->slug }}" {{ request('type') == $tt->slug ? 'selected' : '' }}>{{ $tt->name }}</option>
                @endforeach
            </select>
            <select name="location_id" class="form-select" style="max-width: 200px;" onchange="document.getElementById('searchForm').submit()">
                <option value="">-- ทุกพื้นที่ --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select" style="max-width: 160px;" onchange="document.getElementById('searchForm').submit()">
                <option value="">-- ทุกสถานะ --</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งานปกติ</option>
                <option value="under_repair" {{ request('status') == 'under_repair' ? 'selected' : '' }}>ซ่อมบำรุง</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
            </select>
        </form>

        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'safety_officer']))
        <a href="{{ route('tools.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> เพิ่มเครื่องมือ
        </a>
        @endif
    </div>
    
    <div class="card-body p-0">
        <form action="{{ route('tools.bulk-qr') }}" method="POST" target="_blank" id="bulkQrForm">
            @csrf

            <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <div class="form-check d-inline-block me-3">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label fw-bold" for="selectAll">
                            เลือกทั้งหมดในหน้านี้
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-sm btn-dark" id="btnBulkQr" disabled>
                    <i class="bi bi-printers"></i> พิมพ์ QR (ที่เลือก)
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 border-0" width="3%"></th>
                        <th class="border-0">รหัส</th>
                        <th class="border-0">ชื่อเครื่องมือ</th>
                        <th class="border-0">ประเภท</th>
                        <th class="border-0">พื้นที่</th>
                        <th class="border-0">ยี่ห้อ / รุ่น</th>
                        <th class="border-0">ตรวจครั้งถัดไป</th>
                        <th class="border-0">สถานะ</th>
                        <th class="text-end pe-3 border-0">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $tool)
                    <tr>
                        <td class="ps-3">
                            <input class="form-check-input row-checkbox" type="checkbox" name="ids[]" value="{{ $tool->id }}">
                        </td>
                        <td><span class="fw-bold">{{ $tool->tool_code }}</span></td>
                        <td>{{ $tool->tool_name }}</td>
                        <td>
                            <span class="badge bg-{{ $tool->toolType->color ?? 'secondary' }} rounded-pill">{{ $tool->type_name }}</span>
                        </td>
                        <td>
                            <span class="fw-medium text-dark"><i class="bi bi-pin-map text-danger"></i> {{ $tool->location->location_name ?? '-' }}</span><br>
                            @if($tool->house || $tool->zone)
                            <div class="mt-1">
                                @if($tool->house)
                                <span class="badge bg-light text-dark border fw-normal me-1"><i class="bi bi-house text-secondary me-1"></i>{{ $tool->house }}</span>
                                @endif
                                @if($tool->zone)
                                <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-signpost-split text-secondary me-1"></i>โซน: {{ $tool->zone }}</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="d-block fw-bold text-dark">{{ $tool->brand ?? '-' }}</span>
                            <span class="small text-muted"><i class="bi bi-tag"></i> {{ $tool->model ?? '-' }}</span>
                        </td>
                        <td>
                            @if($tool->next_inspection_date)
                                @if($tool->next_inspection_date->isPast())
                                    <span class="badge bg-danger rounded-pill"><i class="bi bi-exclamation-triangle"></i> เกินกำหนด</span><br>
                                    <small class="text-danger">{{ $tool->next_inspection_date->format('d/m/Y') }}</small>
                                @elseif($tool->next_inspection_date->diffInDays(now()) <= 7)
                                    <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-clock"></i> ใกล้ถึง</span><br>
                                    <small class="text-warning">{{ $tool->next_inspection_date->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted">{{ $tool->next_inspection_date->format('d/m/Y') }}</span>
                                @endif
                            @else
                                <span class="badge bg-info rounded-pill">ยังไม่เคยตรวจ</span>
                            @endif
                        </td>
                        <td>
                            @if($tool->status == 'active')
                                <span class="badge bg-success rounded-pill px-3">พร้อมใช้งาน</span>
                            @elseif($tool->status == 'under_repair')
                                <span class="badge bg-warning text-dark rounded-pill px-3">ซ่อมบำรุง</span>
                            @elseif($tool->status == 'inactive')
                                <span class="badge bg-secondary rounded-pill px-3">ไม่ใช้งาน</span>
                            @else
                                <span class="badge bg-dark rounded-pill px-3">จำหน่ายแล้ว</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('tools.qr', $tool->id) }}" class="btn btn-sm btn-outline-dark" title="พิมพ์ QR Code" target="_blank">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                <a href="{{ route('tool-inspections.create', ['tool_id' => $tool->id]) }}" class="btn btn-sm btn-outline-success" title="ตรวจสอบ">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                                <a href="{{ route('tools.show', $tool->id) }}" class="btn btn-sm btn-outline-info" title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'safety_officer']))
                                <a href="{{ route('tools.edit', $tool->id) }}" class="btn btn-sm btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="ลบ" onclick="confirmDelete({{ $tool->id }}, '{{ $tool->tool_code }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-wrench display-4 text-secondary opacity-25 d-block mb-3"></i>
                            <span class="fs-5">ยังไม่มีเครื่องมือในระบบ</span><br>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'safety_officer']))
                            <a href="{{ route('tools.create') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-lg"></i> เพิ่มเครื่องมือตัวแรก
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </form>
    </div>
    
    @if(isset($tools) && $tools->lastPage() > 1)
    <div class="card-footer bg-white pt-3 pb-1">
        {{ $tools->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Hidden Delete Form -->
<form id="deleteToolForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const btnBulkQr = document.getElementById('btnBulkQr');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            btnBulkQr.disabled = checkedCount === 0;
            btnBulkQr.innerHTML = '<i class="bi bi-printers"></i> พิมพ์ QR (' + checkedCount + ')';
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateButtonState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if(!this.checked && selectAll.checked) {
                    selectAll.checked = false;
                } else if(this.checked && document.querySelectorAll('.row-checkbox:checked').length === checkboxes.length) {
                    selectAll.checked = true;
                }
                updateButtonState();
            });
        });
    });

    function confirmDelete(id, code) {
        if(confirm('ยืนยันการลบเครื่องมือ ' + code + ' ?')) {
            const form = document.getElementById('deleteToolForm');
            form.action = '/tools/' + id;
            form.submit();
        }
    }
</script>
@endsection
