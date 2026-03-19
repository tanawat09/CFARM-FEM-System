@extends('layouts.app')

@section('page_title', 'ประวัติการตรวจเช็คอุปกรณ์')

@section('content')
<!-- Tab Navigation -->
<ul class="nav nav-pills mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $type == 'emergency_light' ? 'active' : '' }}" href="{{ route('equipment-inspections.index', ['type' => 'emergency_light']) }}">
            <i class="bi bi-lightbulb me-1"></i> ไฟฉุกเฉิน
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $type == 'eyewash_shower' ? 'active' : '' }}" href="{{ route('equipment-inspections.index', ['type' => 'eyewash_shower']) }}">
            <i class="bi bi-droplet me-1"></i> ที่ล้างตา/ฝักบัวฉุกเฉิน
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-3 d-flex justify-content-between align-items-center">
        <form class="d-flex w-50" action="{{ route('equipment-inspections.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="input-group">
                <select name="location_id" class="form-select" style="max-width: 250px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">-- ทุกพื้นที่ --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->location_name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 ps-3">เลขที่ใบตรวจ</th>
                    <th class="border-0">อุปกรณ์</th>
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
                    <td class="ps-3"><span class="fw-bold small">{{ $insp->inspection_no }}</span></td>
                    <td>
                        <a href="{{ route('safety-equipment.show', $insp->equipment_id) }}" class="text-decoration-none fw-bold">
                            {{ $insp->equipment->asset_code ?? '-' }}
                        </a>
                    </td>
                    <td>
                        <span class="fw-medium"><i class="bi bi-pin-map text-danger"></i> {{ $insp->equipment->location->location_name ?? '-' }}</span>
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
                        <a href="{{ route('equipment-inspections.show', $insp->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard display-4 text-secondary opacity-25 d-block mb-3"></i>
                        <span class="fs-5">ยังไม่มีประวัติการตรวจเช็ค</span>
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
