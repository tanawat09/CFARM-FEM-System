@extends('layouts.app')

@section('page_title', 'รายงานสรุปการตรวจเช็คเครื่องมือช่างประจำปี')

@section('styles')
<style>
    @media print {
        body { background-color: #fff !important; }
        .sidebar, .topbar, .d-print-none { display: none !important; }
        .main-content { width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-header { padding: 0 0 1rem 0 !important; border-bottom: 2px solid #000 !important; }
        .card-body { padding: 1rem 0 0 0 !important; }
    }
</style>
@endsection

@section('content')
<div class="d-none d-print-block text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 100px;">
</div>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar3 text-primary me-2"></i> รายงานสรุปการตรวจเช็คเครื่องมือช่างประจำปี (ภาพรวม)</h5>
            <small class="text-muted">แสดงข้อมูลสถิติประจำปี {{ $year + 543 }}</small>
        </div>
        <div class="d-flex gap-2 d-print-none">
            <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ / PDF</button>
            <a href="{{ route('reports.export-tools-annual-pdf', ['year' => $year]) }}" class="btn btn-danger btn-sm rounded-pill px-3"><i class="bi bi-file-pdf"></i> ดาวน์โหลด PDF</a>
        </div>
    </div>
    
    <div class="card-body p-4 p-md-5">
        
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover align-middle flex-nowrap" style="min-width: 1000px;">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="5%" rowspan="2">ลำดับ</th>
                        <th class="py-3" width="15%" rowspan="2">สถานที่ติดตั้ง</th>
                        <th class="py-3 text-center" width="10%" rowspan="2">จำนวนอุปกรณ์</th>
                        <th class="py-2 text-center" colspan="12">ผลการตรวจสอบแบบประจำเดือน ({{ $year + 543 }})</th>
                        <th class="py-3 text-center text-success" width="8%" rowspan="2">รวมตรวจผ่าน</th>
                        <th class="py-3 text-center text-danger" width="10%" rowspan="2">พบชำรุดสะสม</th>
                    </tr>
                    <tr>
                        @php
                            $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                        @endphp
                        @foreach($thaiMonths as $month)
                            <th class="text-center small">{{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($annualData as $index => $data)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $data['location_name'] }}</td>
                            <td class="text-center fw-bold">{{ $data['tool_count'] }}</td>
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $stats = $data['monthly_stats'][$m];
                                @endphp
                                <td class="text-center">
                                    @if($stats['total_inspected'] == 0)
                                        <span class="text-muted opacity-25">-</span>
                                    @elseif($stats['failed'] > 0)
                                        <span class="text-danger fw-bold" title="พบชำรุด {{ $stats['failed'] }} รายการ"><i class="bi bi-x-circle-fill"></i></span>
                                    @else
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i></span>
                                    @endif
                                </td>
                            @endfor
                            <td class="text-center fw-bold text-success bg-success bg-opacity-10">{{ $data['total_passed'] }}</td>
                            <td class="text-center fw-bold text-danger bg-danger bg-opacity-10">{{ $data['total_failed'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-5 text-muted">ไม่พบข้อมูลในระบบประจำปี {{ $year + 543 }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 border-top pt-4">
            <h6 class="fw-bold">คำอธิบายสัญลักษณ์</h6>
            <div class="d-flex gap-4 mt-2 text-sm flex-wrap">
                <div><span class="text-success"><i class="bi bi-check-circle-fill"></i></span> ตรวจสอบแล้ว ปกติทั้งหมด</div>
                <div><span class="text-danger"><i class="bi bi-x-circle-fill"></i></span> ตรวจสอบแล้ว พบอุปกรณ์ชำรุด/ไม่ผ่าน</div>
                <div><span class="text-muted opacity-50">-</span> ยังไม่มีข้อมูลการตรวจสอบในเดือนนั้น</div>
            </div>
        </div>
    </div>
</div>
@endsection
