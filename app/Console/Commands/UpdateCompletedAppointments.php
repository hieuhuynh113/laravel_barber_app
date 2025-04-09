<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class UpdateCompletedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-completed-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái cuộc hẹn đã hoàn thành';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Lấy tất cả cuộc hẹn đã xác nhận và thời gian đã qua
        $appointments = Appointment::where('status', 'confirmed')
            ->where('appointment_date', '<', $now->toDateString())
            ->orWhere(function($query) use ($now) {
                $query->where('appointment_date', '=', $now->toDateString())
                      ->where('appointment_time', '<', $now->format('H:i:s'));
            })
            ->get();
            
        $count = 0;
        foreach ($appointments as $appointment) {
            $appointment->status = 'completed';
            $appointment->save();
            $count++;
        }
        
        $this->info("Đã cập nhật {$count} cuộc hẹn sang trạng thái hoàn thành.");
    }
}
