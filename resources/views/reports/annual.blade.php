@extends('layouts.app')

@section('page_title', 'รายงานสรุปการตรวจเช็คประจำปี')

@section('content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar3 text-success me-2"></i> รายงานสรุปการตรวจเช็คประจำปี (ภาพรวม)</h5>
            <small class="text-muted">แสดงข้อมูลสถิติประจำปี {{ $year + 543 }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ / PDF</button>
            <a href="{{ route('reports.export-excel', ['year' => $year]) }}" class="btn btn-success btn-sm rounded-pill px-3"><i class="bi bi-file-earmark-excel"></i> ส่งออก Excel</a>
        </div>
    </div>
    
    <div class="card-body p-4 p-md-5">
        
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-center" width="5%" rowspan="2">ลำดับ</th>
                        <th class="py-3" width="15%" rowspan="2">สถานที่ติดตั้ง</th>
                        <th class="py-2 text-center" colspan="12">ผลการตรวจสอบ ({{ $year + 543 }})</th>
                        <th class="py-3 text-center" width="8%" rowspan="2">รวมตรวจผ่าน</th>
                        <th class="py-3 text-center" width="10%" rowspan="2">พบชำรุดสะสม</th>
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
                            <td class="text-center fw-bold text-success">{{ $data['total_passed'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $data['total_failed'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center py-5 text-muted">ไม่พบข้อมูลในระบบประจำปี {{ $year + 543 }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 border-top pt-4">
            <h6 class="fw-bold">คำอธิบายสัญลักษณ์</h6>
            <div class="d-flex gap-4 mt-2">
                <div><span class="text-success"><i class="bi bi-check-circle-fill"></i></span> ตรวจสอบแล้ว ปกติทั้งหมด</div>
                <div><span class="text-danger"><i class="bi bi-x-circle-fill"></i></span> ตรวจสอบแล้ว พบอุปกรณ์ชำรุด/แจ้งซ่อม</div>
                <div><span class="text-muted opacity-50">-</span> ยังไม่มีข้อมูลการตรวจสอบในเดือนนั้น</div>
            </div>
        </div>
    </div>
</div>
@endsection
