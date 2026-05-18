@extends('layouts.app')

@section('page_title', $title)

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1 text-dark">{{ $title }}</h5>
            <div class="text-muted">{{ $subtitle }}</div>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-light rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> กลับ
        </a>
    </div>

    <div class="card-body p-4">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
                <label class="form-label fw-medium">เดือน</label>
                <select name="month" class="form-select shadow-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ (int) $month === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('th')->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">ปี</label>
                <select name="year" class="form-select shadow-sm">
                    @foreach(range((int) date('Y'), (int) date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ (int) $year === $y ? 'selected' : '' }}>
                            {{ $y + 543 }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">พื้นที่</label>
                <select name="location_id" class="form-select shadow-sm">
                    <option value="">ทั้งหมด</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ (string) $selectedLocationId === (string) $location->id ? 'selected' : '' }}>
                            {{ $location->location_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-search"></i> แสดง
                </button>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-4 h-100">
                    <small class="text-muted d-block mb-2">เดือนรายงาน</small>
                    <h5 class="fw-bold mb-0">{{ $monthLabel }}</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-primary bg-opacity-10 rounded-4 h-100 text-primary">
                    <small class="d-block mb-2">จำนวนรายการตรวจทั้งหมด</small>
                    <h2 class="fw-bold mb-0">{{ number_format($records->total()) }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-success bg-opacity-10 rounded-4 h-100 text-success">
                    <small class="d-block mb-2">จำนวนรายการในหน้านี้</small>
                    <h2 class="fw-bold mb-0">{{ number_format($records->count()) }}</h2>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>เลขที่ใบตรวจ</th>
                        <th>อุปกรณ์ / พื้นที่</th>
                        <th>ผู้ตรวจ</th>
                        <th>วันเวลา</th>
                        <th class="text-center">ผลลัพธ์</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @php
                            $row = $rowResolver($record);
                            $inspectedAt = $row['inspected_at']->copy()->timezone(config('app.timezone'));
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $row['inspection_no'] }}</td>
                            <td>
                                <div class="fw-medium text-dark">{{ $row['asset_label'] }}</div>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $row['location_label'] }}
                                </small>
                            </td>
                            <td>{{ $row['inspector'] }}</td>
                            <td>
                                <div class="fw-medium">{{ $inspectedAt->locale('th')->translatedFormat('d M') }} {{ $inspectedAt->year + 543 }}</div>
                                <small class="text-muted">{{ $inspectedAt->format('H:i') }} น.</small>
                            </td>
                            <td class="text-center">
                                @if($row['result'] === 'pass')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> ปกติ
                                    </span>
                                @elseif($row['result'] === 'fail')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50 px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> ไม่ผ่าน
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-50 px-3 py-2 rounded-pill">
                                        {{ $row['result'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-x display-5 d-block mb-3 opacity-50"></i>
                                ไม่พบข้อมูลรายงานในเงื่อนไขที่เลือก
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $records->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
