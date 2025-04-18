<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    protected $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
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
            ->subject('Đánh giá mới từ ' . $this->review->user->name)
            ->line('Có một đánh giá mới từ ' . $this->review->user->name . ' cho dịch vụ ' . $this->review->service->name)
            ->line('Đánh giá: ' . $this->review->rating . ' sao')
            ->line('Nhận xét: ' . $this->review->comment)
            ->action('Xem đánh giá', url('/admin/reviews/' . $this->review->id))
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
            'review_id' => $this->review->id,
            'user_id' => $this->review->user_id,
            'user_name' => $this->review->user->name,
            'service_id' => $this->review->service_id,
            'service_name' => $this->review->service->name,
            'barber_id' => $this->review->barber_id,
            'barber_name' => $this->review->barber->user->name,
            'rating' => $this->review->rating,
            'comment' => $this->review->comment,
            'created_at' => $this->review->created_at->format('d/m/Y H:i'),
            'is_low_rating' => $this->review->rating <= 2
        ];
    }
}
