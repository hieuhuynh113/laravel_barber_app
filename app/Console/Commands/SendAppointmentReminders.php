<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email nhắc nhở cho các cuộc hẹn sắp tới';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $appointments = Appointment::where('appointment_date', $tomorrow)
            ->where('status', 'confirmed')
            ->with('customer', 'services', 'barber')
            ->get();
            
        $this->info("Tìm thấy {$appointments->count()} cuộc hẹn cho ngày mai.");
        
        foreach ($appointments as $appointment) {
            Mail::to($appointment->customer->email)
                ->send(new AppointmentReminder($appointment));
            
            $this->info("Đã gửi nhắc nhở cho cuộc hẹn #{$appointment->id}");
        }
        
        $this->info('Hoàn thành gửi nhắc nhở.');
    }
}
