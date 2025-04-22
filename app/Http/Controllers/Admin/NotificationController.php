<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Lọc theo loại thông báo
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->filter === 'reviews') {
                $query->where('type', 'App\\Notifications\\NewReviewNotification');
            } elseif ($request->filter === 'appointments') {
                $query->where('type', 'App\\Notifications\\NewAppointmentNotification');
            } elseif ($request->filter === 'payments') {
                $query->where('type', 'App\\Notifications\\NewPaymentNotification');
            } elseif ($request->filter === 'contacts') {
                $query->where('type', 'App\\Notifications\\NewContactNotification');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($notification->notifiable_id === Auth::id()) {
            $notification->markAsRead();
        }

        return redirect()->back()->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
    }
}
