<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
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
        <h2>รายงานอุปกรณ์ดับเพลิงชำรุด (แจ้งซ่อม)</h2>
        <p>
            ช่วงเวลา: {{ \Carbon\Carbon::createFromFormat('Y-m', $start_month)->translatedFormat('F Y') }} - 
            {{ \Carbon\Carbon::createFromFormat('Y-m', $end_month)->translatedFormat('F Y') }}
        </p>
    </div>

    <div style="margin-bottom: 15px;">
        <span class="font-bold">สรุปข้อมูล:</span>
        แจ้งซ่อมทั้งหมด: {{ $totalReported }} รายการ | 
        ซ่อมเสร็จ: {{ $totalCompleted }} รายการ | 
        รอซ่อม: {{ $totalPending }} รายการ
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">ลำดับ</th>
                <th width="15%">รหัสอุปกรณ์</th>
                <th width="20%">สถานที่ติดตั้ง</th>
                <th width="25%">ปัญหา/อาการชำรุด</th>
                <th class="text-center" width="15%">วันที่แจ้ง</th>
                <th class="text-center" width="10%">สถานะ</th>
                <th class="text-center" width="10%">ค่าซ่อม (บาท)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($repairLogs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $log->fireExtinguisher->serial_number ?? 'N/A' }}</td>
                    <td>{{ $log->fireExtinguisher->location->location_name ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($log->problem, 100) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M y') }}</td>
                    <td class="text-center">
                        @if($log->status == 'pending')
                            รอซ่อม
                        @elseif($log->status == 'in_progress')
                            กำลังซ่อม
                        @else
                            เสร็จสิ้น
                        @endif
                    </td>
                    <td class="text-right">{{ $log->repair_cost ? number_format($log->repair_cost, 2) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-4 text-muted">ไม่พบข้อมูลการแจ้งซ่อมในช่วงเวลาที่เลือก</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 14px;" class="text-right">
        พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
