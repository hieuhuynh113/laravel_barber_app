<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $barber = Auth::user()->barber;

        // Lấy các thống kê cho trang dashboard
        $todayAppointments = Appointment::where('barber_id', $barber->id)
            ->whereDate('appointment_date', Carbon::today())
            ->count();

        $upcomingAppointments = Appointment::where('barber_id', $barber->id)
            ->where('status', 'confirmed')
            ->whereDate('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $totalAppointments = Appointment::where('barber_id', $barber->id)->count();

        $completedAppointments = Appointment::where('barber_id', $barber->id)
            ->where('status', 'completed')
            ->count();

        // Lấy tên của thợ cắt tóc
        $barberName = Auth::user()->name;

        return view('barber.dashboard', compact('todayAppointments', 'upcomingAppointments', 'totalAppointments', 'completedAppointments', 'barberName'));
    }
}