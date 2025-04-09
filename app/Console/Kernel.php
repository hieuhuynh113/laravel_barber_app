<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Lịch tự động gửi nhắc nhở cho các cuộc hẹn sắp tới
        $schedule->command('app:send-appointment-reminders')->dailyAt('8:00');
        
        // Cập nhật trạng thái các cuộc hẹn đã hoàn thành
        $schedule->command('app:update-completed-appointments')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
