<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานสรุปการตรวจเช็คก่อนใช้งานเครื่องมือช่าง</title>
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
    <div class="header text-center" style="margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px;">
        <img src="{{ str_replace('\\', '/', public_path('images/logo.png')) }}" style="height: 70px; margin-bottom: 10px;">
        <h2 style="margin: 0;">รายงานสรุปผลการประเมินความปลอดภัยก่อนใช้งาน (เครื่องมือช่าง)</h2>
        <p style="margin: 5px 0;">
            ประจำเดือน: {{ \Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} 
            ปี: {{ $year + 543 }}
        </p>
    </div>

    <div style="margin-bottom: 20px;">
        <span class="font-bold">สรุปข้อมูลภาพรวม:</span>
        @php
            $totalEquipment = $locationStats->sum('tool_count');
            $totalPassed = $locationStats->sum('prework_passed');
            $totalFailed = $locationStats->sum('prework_failed');
            $totalInspected = $totalPassed + $totalFailed;
        @endphp
        อุปกรณ์ทั้งหมด: {{ $totalEquipment }} ชิ้น | 
        ประเมินก่อนใช้งานทั้งหมด: {{ $totalInspected }} ครั้ง | 
        ผ่าน / ปลอดภัย: <span style="color: green;">{{ $totalPassed }} ครั้ง</span> | 
        ไม่ผ่าน / พบชำรุด: <span style="color: red;">{{ $totalFailed }} ครั้ง</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="10%">ลำดับ</th>
                <th width="30%">สถานที่/พื้นที่ติดตั้ง</th>
                <th class="text-center" width="15%">อุปกรณ์ (ชิ้น)</th>
                <th class="text-center" width="15%">ตรวจก่อนใช้งาน (ครั้ง)</th>
                <th class="text-center" width="15%" style="color: green;">ปกติ (ครั้ง)</th>
                <th class="text-center" width="15%" style="color: red;">พบชำรุด (ครั้ง)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sumTotal = 0; 
                $sumInspected = 0;
                $sumPassed = 0; 
                $sumFailed = 0; 
            @endphp
            @forelse($locationStats as $index => $loc)
                @php
                    $inspected = $loc->prework_passed + $loc->prework_failed;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $loc->location_name }}</td>
                    <td class="text-center">{{ $loc->tool_count }}</td>
                    <td class="text-center font-bold">{{ $inspected }}</td>
                    <td class="text-center" style="color: green;">{{ $loc->prework_passed }}</td>
                    <td class="text-center" style="color: red;">{{ $loc->prework_failed }}</td>
                </tr>
                @php
                    $sumTotal += $loc->tool_count;
                    $sumInspected += $inspected;
                    $sumPassed += $loc->prework_passed;
                    $sumFailed += $loc->prework_failed;
                @endphp
            @empty
                <tr>
                    <td colspan="6" class="text-center">ไม่พบข้อมูลสถานที่</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="2" class="text-right">รวมทั้งหมด</td>
                <td class="text-center">{{ $sumTotal }}</td>
                <td class="text-center">{{ $sumInspected }}</td>
                <td class="text-center" style="color: green;">{{ $sumPassed }}</td>
                <td class="text-center" style="color: red;">{{ $sumFailed }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 14px;" class="text-right">
        พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
