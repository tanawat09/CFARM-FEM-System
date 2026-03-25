<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานสรุปการตรวจเช็คเครื่องมือช่างประจำปี {{ $year + 543 }}</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew.ttf')) }}") format("truetype");
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ str_replace('\\', '/', public_path('fonts/THSarabunNew-Bold.ttf')) }}") format("truetype");
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            text-align: center;
            font-size: 13px;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            margin-bottom: 15px;
        }
        .pass { color: green; }
        .fail { color: red; }
        .none { color: #ccc; }
        @php
            $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        @endphp
    </style>
</head>
<body>
    <div class="header text-center" style="margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px;">
        <img src="{{ str_replace('\\', '/', public_path('images/logo.png')) }}" style="height: 56px; margin-bottom: 5px;">
        <h2 style="margin: 0;">รายงานสรุปผลการตรวจเช็คเครื่องมือช่างประจำปี</h2>
        <p style="margin: 5px 0;">ประจำปี {{ $year + 543 }}</p>
    </div>

    <div style="margin-bottom: 15px;">
        <span class="font-bold">สรุปภาพรวม:</span>
        @php
            $grandEquipment = collect($annualData)->sum('tool_count');
            $grandPassed = collect($annualData)->sum('total_passed');
            $grandFailed = collect($annualData)->sum('total_failed');
        @endphp
        อุปกรณ์ทั้งหมด: {{ $grandEquipment }} ชิ้น | 
        ผ่านทั้งปี: <span class="pass">{{ $grandPassed }}</span> | 
        ไม่ผ่านทั้งปี: <span class="fail">{{ $grandFailed }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="4%">ลำดับ</th>
                <th rowspan="2" width="14%">สถานที่</th>
                <th rowspan="2" width="5%">อุปกรณ์</th>
                <th colspan="12">ผลการตรวจสอบ ({{ $year + 543 }})</th>
                <th rowspan="2" width="5%">รวมผ่าน</th>
                <th rowspan="2" width="5%">รวมไม่ผ่าน</th>
            </tr>
            <tr>
                @foreach($thaiMonths as $mn)
                    <th>{{ $mn }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($annualData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $data['location_name'] }}</td>
                    <td>{{ $data['tool_count'] }}</td>
                    @for($m = 1; $m <= 12; $m++)
                        @php $stats = $data['monthly_stats'][$m]; @endphp
                        <td>
                            @if($stats['total_inspected'] == 0)
                                <span class="none">-</span>
                            @elseif($stats['failed'] > 0)
                                <span class="fail">✗</span>
                            @else
                                <span class="pass">✓</span>
                            @endif
                        </td>
                    @endfor
                    <td class="pass font-bold">{{ $data['total_passed'] }}</td>
                    <td class="fail font-bold">{{ $data['total_failed'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17">ไม่พบข้อมูลอุปกรณ์ในระบบประจำปี {{ $year + 543 }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 13px;">
        <span class="font-bold">คำอธิบาย:</span>
        <span class="pass">✓</span> = ปกติทั้งหมด  |  
        <span class="fail">✗</span> = พบชำรุด  |  
        <span class="none">-</span> = ยังไม่ได้ตรวจ
    </div>

    <div style="margin-top: 20px; font-size: 12px;" class="text-right">
        พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
