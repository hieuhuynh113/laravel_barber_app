<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách thông báo
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications();

        // Lọc theo loại thông báo
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->filter === 'appointments') {
                $query->where('type', 'App\\Notifications\\BarberAppointmentNotification');
            } elseif ($request->filter === 'schedules') {
                $query->where('type', 'App\\Notifications\\ScheduleChangeRequestNotification');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('barber.notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
    }
}
