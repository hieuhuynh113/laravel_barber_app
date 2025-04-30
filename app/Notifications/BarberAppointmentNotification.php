<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BarberAppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, $type = 'new')
    {
        $this->appointment = $appointment;
        $this->type = $type; // 'new', 'confirmed', 'canceled'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = '';
        $greeting = 'Xin chào ' . $notifiable->name . '!';
        $line1 = '';
        $line2 = 'Ngày: ' . $this->appointment->appointment_date->format('d/m/Y');
        $line3 = 'Giờ: ' . $this->appointment->time_slot;
        
        if ($this->type == 'new') {
            $subject = 'Lịch hẹn mới';
            $line1 = 'Bạn có một lịch hẹn mới từ khách hàng ' . $this->appointment->customer_name . '.';
        } elseif ($this->type == 'confirmed') {
            $subject = 'Lịch hẹn đã được xác nhận';
            $line1 = 'Lịch hẹn của khách hàng ' . $this->appointment->customer_name . ' đã được xác nhận.';
        } elseif ($this->type == 'canceled') {
            $subject = 'Lịch hẹn đã bị hủy';
            $line1 = 'Lịch hẹn của khách hàng ' . $this->appointment->customer_name . ' đã bị hủy.';
        }
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->line($line2)
            ->line($line3)
            ->action('Xem chi tiết', url('/barber/appointments/' . $this->appointment->id))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
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
            'customer_name' => $this->appointment->customer_name,
            'appointment_date' => $this->appointment->appointment_date,
            'time_slot' => $this->appointment->time_slot,
            'services' => $services,
            'created_at' => $this->appointment->created_at->format('d/m/Y H:i'),
            'type' => $this->type,
            'message' => $this->getNotificationMessage()
        ];
    }
    
    /**
     * Get notification message based on type
     */
    private function getNotificationMessage()
    {
        if ($this->type == 'new') {
            return 'Bạn có một lịch hẹn mới từ khách hàng ' . $this->appointment->customer_name;
        } elseif ($this->type == 'confirmed') {
            return 'Lịch hẹn của khách hàng ' . $this->appointment->customer_name . ' đã được xác nhận';
        } elseif ($this->type == 'canceled') {
            return 'Lịch hẹn của khách hàng ' . $this->appointment->customer_name . ' đã bị hủy';
        }
        
        return '';
    }
}
