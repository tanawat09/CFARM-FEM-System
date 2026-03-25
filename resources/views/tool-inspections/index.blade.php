@extends('layouts.app')

@section('page_title', 'ประวัติการตรวจสอบเครื่องมือช่าง')

@section('content')
<!-- Tab Navigation -->
<ul class="nav nav-pills mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $type == '' ? 'active' : '' }}" href="{{ route('tool-inspections.index') }}">
            <i class="bi bi-list me-1"></i> ทั้งหมด
        </a>
    </li>
    @foreach($toolTypes as $tt)
    <li class="nav-item">
        <a class="nav-link {{ $type == $tt->slug ? 'active' : '' }}" href="{{ route('tool-inspections.index', ['type' => $tt->slug]) }}">
            <i class="bi {{ $tt->icon }} me-1"></i> {{ $tt->name }}
        </a>
    </li>
    @endforeach
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form class="d-flex w-auto gap-2 align-items-center" action="{{ route('tool-inspections.index') }}" method="GET" id="filterForm">
            @if($type)
            <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <input type="hidden" name="inspection_type" id="inspectionTypeInput" value="{{ $inspectionType }}">
            
            <div class="input-group shrink-0">
                <select name="location_id" class="form-select" style="max-width: 250px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- ทุกพื้นที่ --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('tool-inspections.index') }}" class="btn btn-light btn-sm text-nowrap"><i class="bi bi-arrow-counterclockwise"></i> ล้างค่า</a>
        </form>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm {{ $inspectionType == 'pre_work' ? 'btn-primary' : 'btn-outline-primary' }}" 
                    onclick="document.getElementById('inspectionTypeInput').value='pre_work'; document.getElementById('filterForm').submit();">
                <i class="bi bi-clipboard2-check me-1"></i> ก่อนเริ่มงาน
            </button>
            <button type="button" class="btn btn-sm {{ $inspectionType == 'monthly' ? 'btn-success' : 'btn-outline-success' }}"
                    onclick="document.getElementById('inspectionTypeInput').value='monthly'; document.getElementById('filterForm').submit();">
                <i class="bi bi-calendar-check me-1"></i> ประจำเดือน
            </button>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 ps-3">เลขที่ใบตรวจ</th>
                    <th class="border-0">เครื่องมือ</th>
                    <th class="border-0">ประเภท</th>
                    <th class="border-0">พื้นที่</th>
                    <th class="border-0">วันที่ตรวจ</th>
                    <th class="border-0">ผู้ตรวจ</th>
                    <th class="border-0">ผลตรวจ</th>
                    <th class="text-end pe-3 border-0">ดู</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $insp)
                <tr>
                    <td class="ps-3">
                        <span class="fw-bold small d-block">{{ $insp->inspection_no }}</span>
                        @if($insp->inspection_type == 'pre_work')
                            <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">ก่อนเริ่มงาน</span>
                        @else
                            <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">ประจำเดือน</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tools.show', $insp->tool_id) }}" class="text-decoration-none fw-bold">
                            {{ $insp->tool->tool_code ?? '-' }}
                        </a>
                        <div class="small text-muted">{{ $insp->tool->tool_name ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $insp->tool->toolType->color ?? 'secondary' }} rounded-pill">{{ $insp->tool->type_name ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="fw-medium"><i class="bi bi-pin-map text-danger"></i> {{ $insp->tool->location->location_name ?? '-' }}</span>
                    </td>
                    <td>{{ $insp->inspected_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $insp->inspectedBy->name ?? '-' }}</td>
                    <td>
                        @if($insp->overall_result == 'pass')
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i> ผ่าน</span>
                        @else
                            <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle"></i> ไม่ผ่าน</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('tool-inspections.show', $insp->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard display-4 text-secondary opacity-25 d-block mb-3"></i>
                        <span class="fs-5">ยังไม่มีประวัติการตรวจสอบ</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    
    @if(isset($inspections) && $inspections->lastPage() > 1)
    <div class="card-footer bg-white pt-3 pb-1">
        {{ $inspections->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
