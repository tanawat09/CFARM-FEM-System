<?php

namespace App\Http\Controllers;

use App\Models\FireExtinguisher;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

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
            'extinguisher_types' => 'nullable|string',
        ]);

        $sensitiveKeys = ['telegram_bot_token'];

        foreach ($validated as $key => $value) {
            $storeValue = $value ?? '';

            if (in_array($key, $sensitiveKeys, true) && $storeValue !== '') {
                $storeValue = encrypt($storeValue);
            }

            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $storeValue]
            );
        }

        $expireYears = (int) $validated['expire_years'];
        $refillIntervalMonths = (int) $validated['refill_interval_months'];

        FireExtinguisher::select(['id', 'manufacture_date', 'install_date'])
            ->chunkById(200, function ($extinguishers) use ($expireYears, $refillIntervalMonths) {
                foreach ($extinguishers as $extinguisher) {
                    $extinguisher->forceFill([
                        'expire_date' => $extinguisher->manufacture_date
                            ? $extinguisher->manufacture_date->copy()->addYears($expireYears)
                            : $extinguisher->expire_date,
                        'next_refill_date' => $extinguisher->install_date
                            ? $extinguisher->install_date->copy()->addMonths($refillIntervalMonths)
                            : $extinguisher->next_refill_date,
                    ])->saveQuietly();
                }
            });

        return redirect()->route('settings.index')->with('success', 'บันทึกการตั้งค่าระบบสำเร็จ');
    }
}
