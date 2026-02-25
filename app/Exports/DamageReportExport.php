<?php

namespace App\Exports;

use App\Models\RepairLog;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DamageReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        return RepairLog::with(['fireExtinguisher.location'])
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->latest('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ลำดับ',
            'รหัสถังดับเพลิง',
            'สถานที่ติดตั้ง',
            'อาการชำรุด',
            'วันที่แจ้ง',
            'สถานะ',
            'ผู้แจ้งซ่อม / ค่าซ่อม (บาท)',
        ];
    }

    public function map($log): array
    {
        static $index = 1;
        
        $statusMap = [
            'pending' => 'รอซ่อม',
            'in_progress' => 'กำลังซ่อม',
            'completed' => 'เสร็จสิ้น',
        ];

        return [
            $index++,
            $log->fireExtinguisher->serial_number ?? 'N/A',
            $log->fireExtinguisher->location->location_name ?? '-',
            $log->problem,
            Carbon::parse($log->created_at)->translatedFormat('d M Y'),
            $statusMap[$log->status] ?? $log->status,
            $log->repair_cost ? number_format($log->repair_cost, 2) : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
