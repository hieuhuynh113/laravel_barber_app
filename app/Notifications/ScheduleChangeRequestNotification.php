<?php

namespace App\Notifications;

use App\Models\ScheduleChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleChangeRequestNotification extends Notification
{
    use Queueable;

    protected $request;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(ScheduleChangeRequest $request, $status = 'pending')
    {
        $this->request = $request;
        $this->status = $status; // 'pending', 'approved', 'rejected'
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
        $mailData = $this->getMailData($notifiable);

        $mail = (new MailMessage)
            ->subject($mailData['subject'])
            ->greeting($mailData['greeting'])
            ->line($mailData['line1'])
            ->line('Ngày: ' . $this->request->day_name);

        if ($this->request->is_day_off) {
            $mail->line('Loại yêu cầu: Đăng ký ngày nghỉ');
        } else {
            $mail->line('Giờ bắt đầu: ' . $this->request->start_time)
                 ->line('Giờ kết thúc: ' . $this->request->end_time);
        }

        return $mail->line('Lý do: ' . $this->request->reason)
            ->action($mailData['action'][0], $mailData['action'][1])
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Lấy dữ liệu cho email dựa trên trạng thái
     *
     * @param object $notifiable
     * @return array
     */
    private function getMailData(object $notifiable): array
    {
        $data = [
            'greeting' => 'Xin chào ' . $notifiable->name . '!',
        ];

        switch ($this->status) {
            case 'pending':
                $data['subject'] = 'Yêu cầu thay đổi lịch làm việc mới';
                $data['line1'] = 'Có một yêu cầu thay đổi lịch làm việc mới từ thợ cắt tóc ' . $this->request->barber->user->name . '.';
                $data['action'] = ['Xem chi tiết', url('/admin/schedule-requests/' . $this->request->id)];
                break;

            case 'approved':
                $data['subject'] = 'Yêu cầu thay đổi lịch làm việc đã được phê duyệt';
                $data['line1'] = 'Yêu cầu thay đổi lịch làm việc của bạn đã được phê duyệt.';
                $data['action'] = ['Xem lịch làm việc', url('/barber/schedules')];
                break;

            case 'rejected':
                $data['subject'] = 'Yêu cầu thay đổi lịch làm việc đã bị từ chối';
                $data['line1'] = 'Yêu cầu thay đổi lịch làm việc của bạn đã bị từ chối.';
                $data['action'] = ['Xem chi tiết', url('/barber/schedules')];
                break;

            default:
                $data['subject'] = 'Thông báo về lịch làm việc';
                $data['line1'] = 'Có cập nhật về lịch làm việc của bạn.';
                $data['action'] = ['Xem chi tiết', url('/barber/schedules')];
        }

        return $data;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'barber_id' => $this->request->barber_id,
            'barber_name' => $this->request->barber->user->name,
            'day_of_week' => $this->request->day_of_week,
            'day_name' => $this->request->day_name,
            'start_time' => $this->request->start_time,
            'end_time' => $this->request->end_time,
            'is_day_off' => $this->request->is_day_off,
            'reason' => $this->request->reason,
            'status' => $this->status,
            'created_at' => $this->request->created_at->format('d/m/Y H:i'),
            'message' => $this->getNotificationMessage()
        ];
    }

    /**
     * Get notification message based on status
     *
     * @return string
     */
    private function getNotificationMessage(): string
    {
        $requestType = $this->request->is_day_off ? 'ngày nghỉ' : 'lịch làm việc';

        switch ($this->status) {
            case 'pending':
                return 'Có một yêu cầu thay đổi ' . $requestType . ' mới từ thợ cắt tóc ' . $this->request->barber->user->name;
            case 'approved':
                return 'Yêu cầu thay đổi ' . $requestType . ' của bạn đã được phê duyệt';
            case 'rejected':
                return 'Yêu cầu thay đổi ' . $requestType . ' của bạn đã bị từ chối';
            default:
                return 'Có cập nhật về yêu cầu thay đổi ' . $requestType;
        }
    }
}
