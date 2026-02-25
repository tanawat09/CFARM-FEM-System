<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'inspection_interval_days', 'value' => '30', 'description' => 'ระยะเวลาตรวจเช็ค (วัน)'],
            ['key' => 'refill_interval_months', 'value' => '6', 'description' => 'ระยะเวลาเติมสาร (เดือน)'],
            ['key' => 'expire_years', 'value' => '5', 'description' => 'อายุการใช้งาน (ปี)'],
            ['key' => 'warning_days_before', 'value' => '7', 'description' => 'แจ้งเตือนล่วงหน้า (วัน)'],
            ['key' => 'company_name', 'value' => 'บริษัท ตัวอย่าง จำกัด', 'description' => 'ชื่อบริษัท'],
            ['key' => 'company_logo', 'value' => '', 'description' => 'โลโก้บริษัท'],
            ['key' => 'line_notify_token', 'value' => '', 'description' => 'LINE Notify Token'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
