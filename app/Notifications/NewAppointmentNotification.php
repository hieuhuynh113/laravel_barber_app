<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Lịch hẹn mới từ ' . $this->appointment->user->name)
            ->line('Có một lịch hẹn mới từ ' . $this->appointment->user->name)
            ->line('Ngày: ' . \Carbon\Carbon::parse($this->appointment->appointment_date)->format('d/m/Y'))
            ->line('Giờ: ' . $this->appointment->appointment_time)
            ->line('Thợ cắt tóc: ' . $this->appointment->barber->user->name)
            ->action('Xem lịch hẹn', url('/admin/appointments/' . $this->appointment->id))
            ->line('Cảm ơn bạn đã sử dụng ứng dụng của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Lấy danh sách dịch vụ
        $services = $this->appointment->services->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name
            ];
        })->toArray();

        return [
            'appointment_id' => $this->appointment->id,
            'booking_code' => $this->appointment->booking_code,
            'user_id' => $this->appointment->user_id,
            'user_name' => $this->appointment->user->name,
            'barber_id' => $this->appointment->barber_id,
            'barber_name' => $this->appointment->barber->user->name,
            'appointment_date' => $this->appointment->appointment_date,
            'appointment_time' => $this->appointment->appointment_time,
            'services' => $services,
            'created_at' => $this->appointment->created_at->format('d/m/Y H:i'),
            'is_urgent' => $this->appointment->appointment_date == now()->addDay()->toDateString(),
            'status' => $this->appointment->status
        ];
    }
}
