@extends('layouts.app')

@section('page_title', 'ระบบรายงาน (Reports)')

@section('content')
<div class="row g-4">
    <!-- Monthly Inspection Report -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจเช็คประจำเดือน</h5>
                        <small class="text-muted">สรุปผลการตรวจสอบถังดับเพลิงแยกตามพื้นที่</small>
                    </div>
                </div>

                <form action="{{ route('reports.monthly') }}" method="GET" class="mt-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">เดือน</label>
                            <select name="month" class="form-select border-light shadow-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">ปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-monthly-pdf') }}" formmethod="GET" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Damage / Repair Report -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                        <i class="bi bi-tools fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานแจ้งซ่อม/อุปกรณ์ชำรุด</h5>
                        <small class="text-muted">ข้อมูลสถิติการชำรุดและสถานะการซ่อมแซม</small>
                    </div>
                </div>

                <form action="{{ route('reports.damage') }}" method="GET" class="mt-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">ตั้งแต่ดือน</label>
                            <input type="month" name="start_month" class="form-control border-light shadow-sm" value="{{ date('Y-m', strtotime('-6 months')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">ถึงเดือน</label>
                            <input type="month" name="end_month" class="form-control border-light shadow-sm" value="{{ date('Y-m') }}">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-pdf') }}" formmethod="GET" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                        <button type="submit" formaction="{{ route('reports.export-excel') }}" formmethod="GET" class="btn btn-outline-success rounded-pill px-4"><i class="bi bi-file-earmark-excel"></i> Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Annual Inspection Report -->
    <div class="col-md-6 mt-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success me-3">
                        <i class="bi bi-calendar3 fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจเช็คประจำปี</h5>
                        <small class="text-muted">สรุปแนวโน้มและภาพรวมแยกตามปี</small>
                    </div>
                </div>

                <form action="{{ route('reports.annual') }}" method="GET" class="mt-4">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-medium">ประจำปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <a href="{{ route('reports.export-excel') }}" class="btn btn-outline-success rounded-pill px-4"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Safety Equipment: Emergency Light Report -->
    <div class="col-md-6 mt-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                        <i class="bi bi-lightbulb fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจเช็คไฟฉุกเฉินประจำเดือน</h5>
                        <small class="text-muted">สรุปผลการตรวจไฟฉุกเฉิน (Emergency Light) แยกตามพื้นที่</small>
                    </div>
                </div>

                <form action="{{ route('reports.equipment-monthly') }}" method="GET" class="mt-4">
                    <input type="hidden" name="type" value="emergency_light">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">เดือน</label>
                            <select name="month" class="form-select border-light shadow-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">ปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-equipment-monthly-pdf') }}" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Safety Equipment: Eyewash & Shower Report -->
    <div class="col-md-6 mt-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-info border-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info me-3">
                        <i class="bi bi-droplet fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจที่ล้างตา/ฝักบัวฉุกเฉินประจำเดือน</h5>
                        <small class="text-muted">สรุปผลการตรวจ Eyewash & Shower แยกตามพื้นที่</small>
                    </div>
                </div>

                <form action="{{ route('reports.equipment-monthly') }}" method="GET" class="mt-4">
                    <input type="hidden" name="type" value="eyewash_shower">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">เดือน</label>
                            <select name="month" class="form-select border-light shadow-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">ปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-info rounded-pill px-4 text-white"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-equipment-monthly-pdf') }}" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Annual: Emergency Light Report -->
    <div class="col-md-6 mt-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                        <i class="bi bi-calendar3 fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจเช็คไฟฉุกเฉินประจำปี</h5>
                        <small class="text-muted">สรุปแนวโน้มและภาพรวม Emergency Light รายปี</small>
                    </div>
                </div>

                <form action="{{ route('reports.equipment-annual') }}" method="GET" class="mt-4">
                    <input type="hidden" name="type" value="emergency_light">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-medium">ประจำปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-equipment-annual-pdf') }}" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Annual: Eyewash & Shower Report -->
    <div class="col-md-6 mt-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-info border-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info me-3">
                        <i class="bi bi-calendar3 fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">รายงานสรุปการตรวจที่ล้างตา/ฝักบัวฉุกเฉินประจำปี</h5>
                        <small class="text-muted">สรุปแนวโน้มและภาพรวม Eyewash & Shower รายปี</small>
                    </div>
                </div>

                <form action="{{ route('reports.equipment-annual') }}" method="GET" class="mt-4">
                    <input type="hidden" name="type" value="eyewash_shower">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-medium">ประจำปี</label>
                            <select name="year" class="form-select border-light shadow-sm">
                                @foreach(range(date('Y'), date('Y') - 5) as $y)
                                    <option value="{{ $y }}">{{ $y + 543 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-info rounded-pill px-4 text-white"><i class="bi bi-search me-1"></i> ดูรายงาน</button>
                        <button type="submit" formaction="{{ route('reports.export-equipment-annual-pdf') }}" class="btn btn-outline-danger rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
