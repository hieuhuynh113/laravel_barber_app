<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactNotification extends Notification
{
    use Queueable;

    protected $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
            ->subject('Tin nhắn liên hệ mới từ ' . $this->contact->name)
            ->line('Có một tin nhắn liên hệ mới từ ' . $this->contact->name)
            ->line('Email: ' . $this->contact->email)
            ->line('Chủ đề: ' . $this->contact->subject)
            ->line('Nội dung: ' . $this->contact->message)
            ->action('Xem tin nhắn', url('/admin/contacts/' . $this->contact->id))
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
            'contact_id' => $this->contact->id,
            'name' => $this->contact->name,
            'email' => $this->contact->email,
            'phone' => $this->contact->phone,
            'subject' => $this->contact->subject,
            'message' => $this->contact->message,
            'created_at' => $this->contact->created_at->format('d/m/Y H:i')
        ];
    }
}
