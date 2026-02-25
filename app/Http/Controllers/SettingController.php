<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'inspection_interval_days' => 'required|integer|min:1',
            'refill_interval_months' => 'required|integer|min:1',
            'expire_years' => 'required|integer|min:1',
            'warning_days_before' => 'required|integer|min:1',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()->route('settings.index')->with('success', 'บันทึกการตั้งค่าระบบสำเร็จ');
    }
}
