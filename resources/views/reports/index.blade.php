@extends('layouts.app')

@section('page_title', 'ระบบรายงาน')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                <i class="bi bi-file-earmark-bar-graph text-primary fs-3 me-3"></i>
                <h5 class="fw-bold mb-0 text-dark">เลือกรายงานที่ต้องการ</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-5">
                    <label class="form-label fw-bold">ประเภทรายงาน <span class="text-danger">*</span></label>
                    <select id="reportSelector" class="form-select form-select-lg shadow-sm border-light">
                        <option value="">-- กรุณาเลือกรายงาน --</option>
                        <optgroup label="ถังดับเพลิง">
                            <option value="fe_monthly">รายงานสรุปการตรวจเช็คประจำเดือน (ถังดับเพลิง)</option>
                            <option value="fe_annual">รายงานสรุปการตรวจเช็คประจำปี (ถังดับเพลิง)</option>
                            <option value="fe_detail">รายงานรายละเอียดการตรวจเช็ค (ถังดับเพลิง)</option>
                        </optgroup>
                        <optgroup label="ไฟฉุกเฉิน (Emergency Light)">
                            <option value="el_monthly">รายงานสรุปการตรวจเช็คประจำเดือน (ไฟฉุกเฉิน)</option>
                            <option value="el_annual">รายงานสรุปการตรวจเช็คประจำปี (ไฟฉุกเฉิน)</option>
                        </optgroup>
                        <optgroup label="ที่ล้างตา/ฝักบัวฉุกเฉิน (Eyewash & Shower)">
                            <option value="es_monthly">รายงานสรุปการตรวจเช็คประจำเดือน (ที่ล้างตา/ฝักบัว)</option>
                            <option value="es_annual">รายงานสรุปการตรวจเช็คประจำปี (ที่ล้างตา/ฝักบัว)</option>
                            <option value="es_detail">รายงานรายละเอียดการตรวจเช็ค (ที่ล้างตา/ฝักบัว)</option>
                        </optgroup>
                        <optgroup label="เครื่องมือช่าง (Tools)">
                            <option value="tools_monthly">รายงานสรุปการตรวจเช็คประจำเดือน (เครื่องมือช่าง)</option>
                            <option value="tools_prework">รายงานสรุปการตรวจเช็คก่อนใช้งาน (เครื่องมือช่าง)</option>
                            <option value="tools_annual">รายงานสรุปการตรวจเช็คประจำปี (เครื่องมือช่าง)</option>
                            <option value="tools_detail">รายงานรายละเอียดการตรวจเช็ค (เครื่องมือช่าง)</option>
                        </optgroup>
                        <optgroup label="ซ่อมบำรุง">
                            <option value="damage">รายงานแจ้งซ่อม / อุปกรณ์ชำรุด</option>
                        </optgroup>
                    </select>
                </div>

                <div id="formContainer" class="d-none">
                    <hr class="mb-4 opacity-25">

                    <div id="dynamicHeader" class="d-flex align-items-center mb-4">
                        <div id="dynamicIcon" class="p-2 rounded-circle me-3">
                            <i class="fs-4"></i>
                        </div>
                        <div>
                            <h6 id="dynamicTitle" class="fw-bold mb-0"></h6>
                            <small id="dynamicSubtitle" class="text-muted"></small>
                        </div>
                    </div>

                    <form id="dynamicForm" method="GET">
                        <div id="typeInputContainer"></div>
                        <div class="row g-3" id="formInputs"></div>
                        <div class="mt-4 d-flex gap-2" id="actionButtons"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('reportSelector');
    const container = document.getElementById('formContainer');
    const form = document.getElementById('dynamicForm');
    const inputs = document.getElementById('formInputs');
    const buttons = document.getElementById('actionButtons');
    const headerTitle = document.getElementById('dynamicTitle');
    const headerSubtitle = document.getElementById('dynamicSubtitle');
    const headerIcon = document.getElementById('dynamicIcon');
    const typeInput = document.getElementById('typeInputContainer');

    const currentMonth = '{{ date('m') }}';
    const currentYear = '{{ date('Y') }}';

    const locationOptions = `
        <option value="">ทั้งหมด</option>
        @foreach(\App\Models\Location::where('is_active', true)->orderBy('location_name')->get() as $location)
            <option value="{{ $location->id }}">{{ $location->location_name }}</option>
        @endforeach
    `;

    const pastYearList = [
        @foreach(range(date('Y'), date('Y') - 5) as $y)
            { value: '{{ $y }}', label: '{{ $y + 543 }}' },
        @endforeach
    ];

    const monthList = [
        @foreach(range(1, 12) as $m)
            { value: '{{ sprintf('%02d', $m) }}', label: '{{ \Carbon\Carbon::create()->month($m)->locale('th')->translatedFormat('F') }}' },
        @endforeach
    ];

    const monthlyInputs = `
        <div class="col-md-6">
            <label class="form-label small fw-medium">เดือน</label>
            <select name="month" class="form-select border-light shadow-sm">
                ${monthList.map(m => `<option value="${m.value}" ${m.value === currentMonth ? 'selected' : ''}>${m.label}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-medium">ปี</label>
            <select name="year" class="form-select border-light shadow-sm">
                ${pastYearList.map(y => `<option value="${y.value}" ${y.value === currentYear ? 'selected' : ''}>${y.label}</option>`).join('')}
            </select>
        </div>
    `;

    const annualInputs = `
        <div class="col-md-12">
            <label class="form-label small fw-medium">ปี</label>
            <select name="year" class="form-select border-light shadow-sm">
                ${pastYearList.map(y => `<option value="${y.value}" ${y.value === currentYear ? 'selected' : ''}>${y.label}</option>`).join('')}
            </select>
        </div>
    `;

    const detailInputs = `
        ${monthlyInputs}
        <div class="col-md-12">
            <label class="form-label small fw-medium">พื้นที่</label>
            <select name="location_id" class="form-select border-light shadow-sm">
                ${locationOptions}
            </select>
        </div>
    `;

    const damageInputs = `
        <div class="col-md-6">
            <label class="form-label small fw-medium">ตั้งแต่</label>
            <input type="month" name="start_month" class="form-control border-light shadow-sm" value="{{ date('Y-m', strtotime('-6 months')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-medium">ถึง</label>
            <input type="month" name="end_month" class="form-control border-light shadow-sm" value="{{ date('Y-m') }}">
        </div>
    `;

    const configs = {
        fe_monthly: {
            title: 'ถังดับเพลิง: รายงานสรุปประจำเดือน',
            subtitle: 'สรุปผลการตรวจเช็คถังดับเพลิงแยกตามพื้นที่',
            icon: 'bi-fire',
            iconClass: 'bg-danger bg-opacity-10 text-danger',
            route: '{{ route('reports.monthly') }}',
            pdfRoute: '{{ route('reports.export-monthly-pdf') }}',
            inputs: monthlyInputs,
            btnClass: 'btn-danger'
        },
        fe_annual: {
            title: 'ถังดับเพลิง: รายงานสรุปประจำปี',
            subtitle: 'สรุปภาพรวมการตรวจเช็คถังดับเพลิงรายปี',
            icon: 'bi-calendar3',
            iconClass: 'bg-success bg-opacity-10 text-success',
            route: '{{ route('reports.annual') }}',
            pdfRoute: '{{ route('reports.export-annual-pdf') }}',
            excelRoute: '{{ route('reports.export-excel') }}',
            inputs: annualInputs,
            btnClass: 'btn-success'
        },
        fe_detail: {
            title: 'ถังดับเพลิง: รายงานรายละเอียดการตรวจเช็ค',
            subtitle: 'แสดงเลขที่ใบตรวจ อุปกรณ์ พื้นที่ ผู้ตรวจ วันเวลา และผลลัพธ์',
            icon: 'bi-card-list',
            iconClass: 'bg-danger bg-opacity-10 text-danger',
            route: '{{ route('reports.extinguisher-detail') }}',
            inputs: detailInputs,
            btnClass: 'btn-danger'
        },
        el_monthly: {
            title: 'ไฟฉุกเฉิน: รายงานสรุปประจำเดือน',
            subtitle: 'สรุปผลการตรวจเช็คไฟฉุกเฉินแยกตามพื้นที่',
            icon: 'bi-lightbulb',
            iconClass: 'bg-warning bg-opacity-10 text-warning',
            route: '{{ route('reports.equipment-monthly') }}',
            pdfRoute: '{{ route('reports.export-equipment-monthly-pdf') }}',
            typeParam: 'emergency_light',
            inputs: monthlyInputs,
            btnClass: 'btn-warning text-dark'
        },
        el_annual: {
            title: 'ไฟฉุกเฉิน: รายงานสรุปประจำปี',
            subtitle: 'สรุปภาพรวมการตรวจเช็คไฟฉุกเฉินรายปี',
            icon: 'bi-calendar3',
            iconClass: 'bg-warning bg-opacity-10 text-warning',
            route: '{{ route('reports.equipment-annual') }}',
            pdfRoute: '{{ route('reports.export-equipment-annual-pdf') }}',
            typeParam: 'emergency_light',
            inputs: annualInputs,
            btnClass: 'btn-warning text-dark'
        },
        es_monthly: {
            title: 'ที่ล้างตา/ฝักบัว: รายงานสรุปประจำเดือน',
            subtitle: 'สรุปผลการตรวจเช็คที่ล้างตา/ฝักบัวแยกตามพื้นที่',
            icon: 'bi-droplet',
            iconClass: 'bg-info bg-opacity-10 text-info',
            route: '{{ route('reports.equipment-monthly') }}',
            pdfRoute: '{{ route('reports.export-equipment-monthly-pdf') }}',
            typeParam: 'eyewash_shower',
            inputs: monthlyInputs,
            btnClass: 'btn-info text-white'
        },
        es_annual: {
            title: 'ที่ล้างตา/ฝักบัว: รายงานสรุปประจำปี',
            subtitle: 'สรุปภาพรวมการตรวจเช็คที่ล้างตา/ฝักบัวรายปี',
            icon: 'bi-calendar3',
            iconClass: 'bg-info bg-opacity-10 text-info',
            route: '{{ route('reports.equipment-annual') }}',
            pdfRoute: '{{ route('reports.export-equipment-annual-pdf') }}',
            typeParam: 'eyewash_shower',
            inputs: annualInputs,
            btnClass: 'btn-info text-white'
        },
        es_detail: {
            title: 'ที่ล้างตา/ฝักบัว: รายงานรายละเอียดการตรวจเช็ค',
            subtitle: 'แสดงเลขที่ใบตรวจ อุปกรณ์ พื้นที่ ผู้ตรวจ วันเวลา และผลลัพธ์',
            icon: 'bi-card-list',
            iconClass: 'bg-info bg-opacity-10 text-info',
            route: '{{ route('reports.eyewash-detail') }}',
            inputs: detailInputs,
            btnClass: 'btn-info text-white'
        },
        tools_monthly: {
            title: 'เครื่องมือช่าง: รายงานสรุปประจำเดือน',
            subtitle: 'สรุปผลการตรวจเช็คเครื่องมือช่างแยกตามพื้นที่',
            icon: 'bi-tools',
            iconClass: 'bg-primary bg-opacity-10 text-primary',
            route: '{{ route('reports.tools-monthly') }}',
            pdfRoute: '{{ route('reports.export-tools-monthly-pdf') }}',
            inputs: monthlyInputs,
            btnClass: 'btn-primary'
        },
        tools_prework: {
            title: 'เครื่องมือช่าง: รายงานก่อนใช้งาน',
            subtitle: 'สรุปการตรวจเช็คก่อนเริ่มงานของเครื่องมือช่าง',
            icon: 'bi-file-earmark-check',
            iconClass: 'bg-secondary bg-opacity-10 text-secondary',
            route: '{{ route('reports.tools-prework') }}',
            pdfRoute: '{{ route('reports.export-tools-prework-pdf') }}',
            inputs: monthlyInputs,
            btnClass: 'btn-secondary'
        },
        tools_annual: {
            title: 'เครื่องมือช่าง: รายงานสรุปประจำปี',
            subtitle: 'สรุปภาพรวมการตรวจเช็คเครื่องมือช่างรายปี',
            icon: 'bi-calendar3',
            iconClass: 'bg-primary bg-opacity-10 text-primary',
            route: '{{ route('reports.tools-annual') }}',
            pdfRoute: '{{ route('reports.export-tools-annual-pdf') }}',
            inputs: annualInputs,
            btnClass: 'btn-primary'
        },
        tools_detail: {
            title: 'เครื่องมือช่าง: รายงานรายละเอียดการตรวจเช็ค',
            subtitle: 'แสดงเลขที่ใบตรวจ อุปกรณ์ พื้นที่ ผู้ตรวจ วันเวลา และผลลัพธ์',
            icon: 'bi-card-list',
            iconClass: 'bg-primary bg-opacity-10 text-primary',
            route: '{{ route('reports.tools-detail') }}',
            inputs: detailInputs,
            btnClass: 'btn-primary'
        },
        damage: {
            title: 'รายงานแจ้งซ่อม / อุปกรณ์ชำรุด',
            subtitle: 'ข้อมูลสถิติการแจ้งซ่อมและสถานะการซ่อมบำรุง',
            icon: 'bi-tools',
            iconClass: 'bg-secondary bg-opacity-10 text-secondary',
            route: '{{ route('reports.damage') }}',
            pdfRoute: '{{ route('reports.export-pdf') }}',
            excelRoute: '{{ route('reports.export-excel') }}',
            inputs: damageInputs,
            btnClass: 'btn-secondary'
        }
    };

    selector.addEventListener('change', function() {
        const val = this.value;
        if (!val || !configs[val]) {
            container.classList.add('d-none');
            return;
        }

        const conf = configs[val];
        headerTitle.textContent = conf.title;
        headerSubtitle.textContent = conf.subtitle;
        headerIcon.className = `p-3 rounded-circle me-3 ${conf.iconClass}`;
        headerIcon.innerHTML = `<i class="bi ${conf.icon} fs-4"></i>`;

        form.action = conf.route;
        inputs.innerHTML = conf.inputs;
        typeInput.innerHTML = conf.typeParam ? `<input type="hidden" name="type" value="${conf.typeParam}">` : '';

        let btnsHtml = `<button type="submit" class="btn ${conf.btnClass} rounded-pill px-4 shadow-sm"><i class="bi bi-search me-1"></i> ดูรายงานหน้าจอ</button>`;

        if (conf.pdfRoute) {
            const pdfClass = val.startsWith('el_') ? 'btn-outline-dark' : 'btn-outline-danger';
            btnsHtml += `<button type="submit" formaction="${conf.pdfRoute}" formmethod="GET" class="btn ${pdfClass} rounded-pill px-4"><i class="bi bi-file-earmark-pdf"></i> ดาวน์โหลด PDF</button>`;
        }

        if (conf.excelRoute) {
            btnsHtml += `<button type="submit" formaction="${conf.excelRoute}" formmethod="GET" class="btn btn-outline-success rounded-pill px-4"><i class="bi bi-file-earmark-excel"></i> ดาวน์โหลด Excel</button>`;
        }

        buttons.innerHTML = btnsHtml;
        container.classList.remove('d-none');
        container.style.opacity = 0;

        setTimeout(() => {
            container.style.transition = 'opacity 0.3s ease';
            container.style.opacity = 1;
        }, 10);
    });
});
</script>
@endsection
