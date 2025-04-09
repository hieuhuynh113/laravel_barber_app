<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends BaseAdminController
{    
    public function index()
    {
        // Tổng số lịch hẹn hôm nay
        $todayAppointments = Appointment::whereDate('appointment_date', Carbon::today())->count();
        
        // Tổng số lịch hẹn đang chờ xác nhận
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        
        // Tổng số khách hàng
        $customers = User::where('role', 'customer')->count();
        
        // Tổng số tin nhắn chưa đọc
        $unreadMessages = Contact::where('status', 0)->count();
        
        // Lịch hẹn sắp tới (7 ngày tới)
        $upcomingAppointments = Appointment::whereBetween('appointment_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->where('status', '!=', 'canceled')
            ->with(['barber.user', 'services'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'todayAppointments',
            'pendingAppointments',
            'customers',
            'unreadMessages',
            'upcomingAppointments'
        ));
    }
}
