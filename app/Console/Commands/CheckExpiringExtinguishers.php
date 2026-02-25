<?php

namespace App\Console\Commands;

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
        $thirtyDaysFromNow = \Carbon\Carbon::today()->addDays(30);

        // Find extinguishers expiring within 30 days but not yet expired
        $expiringSoon = \App\Models\FireExtinguisher::with('location')
            ->where('expire_date', '<=', $thirtyDaysFromNow)
            ->where('expire_date', '>=', $today)
            ->where('status', '!=', 'disposed')
            ->get();

        foreach ($expiringSoon as $ext) {
            $message = "ถังดับเพลิงหมายเลข {$ext->serial_number} ณ " . ($ext->location->location_name ?? '-') . " กำลังจะหมดอายุในวันที่ " . \Carbon\Carbon::parse($ext->expire_date)->translatedFormat('d M Y');
            
            // Avoid duplicate notifications for the same extinguisher on the same day
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

        // Find extinguishers already expired
        $expired = \App\Models\FireExtinguisher::with('location')
            ->where('expire_date', '<', $today)
            ->where('status', '!=', 'disposed')
            ->get();

        foreach ($expired as $ext) {
            $message = "ถังดับเพลิงหมายเลข {$ext->serial_number} ณ " . ($ext->location->location_name ?? '-') . " หมดอายุแล้วเมื่อวันที่ " . \Carbon\Carbon::parse($ext->expire_date)->translatedFormat('d M Y');
            
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
