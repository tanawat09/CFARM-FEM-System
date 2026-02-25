<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานสรุปการตรวจเช็คประจำเดือน</title>
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
            font-size: 16px;
            color: #333;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h2>รายงานสรุปผลการตรวจสอบถังดับเพลิงประจำเดือน</h2>
        <p>
            ประจำเดือน: {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} 
            ปี: {{ $year + 543 }}
        </p>
    </div>

    <div style="margin-bottom: 20px;">
        <span class="font-bold">สรุปข้อมูลภาพรวม:</span>
        @php
            $totalExtinguishers = $locationStats->sum('fire_extinguishers_count');
            $totalPassed = $locationStats->sum('inspections_passed');
            $totalFailed = $locationStats->sum('inspections_failed');
            $totalInspected = $totalPassed + $totalFailed;
            $totalUninspected = $totalExtinguishers - $totalInspected > 0 ? $totalExtinguishers - $totalInspected : 0;
        @endphp
        ถังดับเพลิงทั้งหมด: {{ $totalExtinguishers }} ถัง | 
        ตรวจผ่าน (ปกติ): <span style="color: green;">{{ $totalPassed }} ถัง</span> | 
        ตรวจไม่ผ่าน (ชำรุด): <span style="color: red;">{{ $totalFailed }} ถัง</span> | 
        ยังไม่ได้ตรวจ: <span style="color: gray;">{{ $totalUninspected }} ถัง</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="10%">ลำดับ</th>
                <th width="30%">สถานที่/พื้นที่ติดตั้ง</th>
                <th class="text-center" width="15%">จำนวนถังทั้งหมด</th>
                <th class="text-center" width="15%" style="color: green;">ผ่านการตรวจ</th>
                <th class="text-center" width="15%" style="color: red;">ไม่ผ่าน/ชำรุด</th>
                <th class="text-center" width="15%" style="color: gray;">ยังไม่ได้ตรวจ</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumTotal = 0; 
                $sumPassed = 0; 
                $sumFailed = 0; 
                $sumUninspected = 0;
            @endphp
            @forelse($locationStats as $index => $loc)
                @php
                    $inspected = $loc->inspections_passed + $loc->inspections_failed;
                    $uninspected = $loc->fire_extinguishers_count - $inspected;
                    $uninspected = $uninspected < 0 ? 0 : $uninspected;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $loc->location_name }}</td>
                    <td class="text-center">{{ $loc->fire_extinguishers_count }}</td>
                    <td class="text-center">{{ $loc->inspections_passed }}</td>
                    <td class="text-center">{{ $loc->inspections_failed }}</td>
                    <td class="text-center">{{ $uninspected }}</td>
                </tr>
                @php
                    $sumTotal += $loc->fire_extinguishers_count;
                    $sumPassed += $loc->inspections_passed;
                    $sumFailed += $loc->inspections_failed;
                    $sumUninspected += $uninspected;
                @endphp
            @empty
                <tr>
                    <td colspan="6" class="text-center p-4 text-muted">ไม่พบข้อมูลสถานที่</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="2" class="text-right">รวมทั้งหมด</td>
                <td class="text-center">{{ $sumTotal }}</td>
                <td class="text-center" style="color: green;">{{ $sumPassed }}</td>
                <td class="text-center" style="color: red;">{{ $sumFailed }}</td>
                <td class="text-center" style="color: gray;">{{ $sumUninspected }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 14px;" class="text-right">
        พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
