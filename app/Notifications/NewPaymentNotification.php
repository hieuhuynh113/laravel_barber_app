<?php

namespace App\Notifications;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPaymentNotification extends Notification
{
    use Queueable;

    protected $receipt;
    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(PaymentReceipt $receipt)
    {
        $this->receipt = $receipt;
        $this->appointment = $receipt->appointment;
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
            ->subject('Biên lai thanh toán mới cho lịch hẹn #' . $this->appointment->booking_code)
            ->line('Có một biên lai thanh toán mới từ ' . $this->appointment->user->name)
            ->line('Mã lịch hẹn: ' . $this->appointment->booking_code)
            ->line('Ngày hẹn: ' . \Carbon\Carbon::parse($this->appointment->appointment_date)->format('d/m/Y'))
            ->line('Tổng tiền: ' . number_format($this->calculateTotal()) . ' VNĐ')
            ->action('Xem biên lai', url('/admin/payment-receipts/' . $this->receipt->id))
            ->line('Cảm ơn bạn đã sử dụng ứng dụng của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'receipt_id' => $this->receipt->id,
            'appointment_id' => $this->appointment->id,
            'booking_code' => $this->appointment->booking_code,
            'user_id' => $this->appointment->user_id,
            'user_name' => $this->appointment->user->name,
            'appointment_date' => $this->appointment->appointment_date,
            'appointment_time' => $this->appointment->appointment_time,
            'total_amount' => $this->calculateTotal(),
            'created_at' => $this->receipt->created_at->format('d/m/Y H:i'),
            'notes' => $this->receipt->notes,
            'file_path' => $this->receipt->file_path
        ];
    }

    /**
     * Tính tổng tiền từ các dịch vụ trong lịch hẹn
     */
    private function calculateTotal()
    {
        $total = 0;
        foreach ($this->appointment->services as $service) {
            $total += $service->price;
        }
        return $total;
    }
}
