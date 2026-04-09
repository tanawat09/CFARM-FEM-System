<?php

namespace App\Console\Commands;

use App\Models\FireExtinguisher;
use Illuminate\Console\Command;

class CheckExpiringExtinguishers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extinguishers:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for fire extinguishers that are expiring or have expired and create notifications.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting extinguisher expiry check...');

        $today = \Carbon\Carbon::today();
        $expireYears = FireExtinguisher::getConfiguredExpireYears();
        $warningDaysBefore = FireExtinguisher::getWarningDaysBefore();

        $expiringSoon = FireExtinguisher::with('location')
            ->expiringSoonByCurrentSetting($expireYears, $warningDaysBefore)
            ->where('status', '!=', 'disposed')
            ->get();

        foreach ($expiringSoon as $ext) {
            $expireDate = $ext->getConfiguredExpireDate($expireYears);
            $message = "ถังดับเพลิงหมายเลข {$ext->serial_number} ณ " . ($ext->location->location_name ?? '-') . " กำลังจะหมดอายุในวันที่ " . $expireDate?->translatedFormat('d M Y');

            $existingLog = \App\Models\NotificationLog::where('title', 'แจ้งเตือนถังใกล้หมดอายุ')
                ->where('message', $message)
                ->whereDate('created_at', $today)
                ->first();

            if (!$existingLog) {
                \App\Models\NotificationLog::create([
                    'title' => 'แจ้งเตือนถังใกล้หมดอายุ',
                    'type' => 'warning',
                    'message' => $message,
                ]);
                $this->line("Created warning for {$ext->serial_number}.");
            }
        }

        $expired = FireExtinguisher::with('location')
            ->expiredByCurrentSetting($expireYears)
            ->where('status', '!=', 'disposed')
            ->get();

        foreach ($expired as $ext) {
            $expireDate = $ext->getConfiguredExpireDate($expireYears);
            $message = "ถังดับเพลิงหมายเลข {$ext->serial_number} ณ " . ($ext->location->location_name ?? '-') . " หมดอายุแล้วเมื่อวันที่ " . $expireDate?->translatedFormat('d M Y');

            $existingLog = \App\Models\NotificationLog::where('title', 'แจ้งเตือนถังหมดอายุ')
                ->where('message', $message)
                ->whereDate('created_at', $today)
                ->first();

            if (!$existingLog) {
                \App\Models\NotificationLog::create([
                    'title' => 'แจ้งเตือนถังหมดอายุ',
                    'type' => 'danger',
                    'message' => $message,
                ]);
                $this->line("Created danger alert for {$ext->serial_number}.");
            }
        }

        $this->info('Expiry check completed successfully.');

        return Command::SUCCESS;
    }
}
