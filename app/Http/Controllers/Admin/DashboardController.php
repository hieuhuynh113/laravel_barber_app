<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Review;
use App\Models\Service;
use App\Models\Barber;
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

        // Thống kê đánh giá
        $totalReviews = Review::count();
        $averageRating = Review::avg('rating') ?? 0;

        // Đánh giá gần đây
        $recentReviews = Review::with(['user', 'service', 'barber.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Đánh giá cần chú ý (1-2 sao)
        $lowRatingReviews = Review::with(['user', 'service', 'barber.user'])
            ->where('rating', '<=', 2)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Dịch vụ được đánh giá cao nhất
        $topServices = Service::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Thợ cắt tóc được đánh giá cao nhất
        $topBarbers = Barber::with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Thống kê doanh thu
        $todayRevenue = Invoice::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid')
            ->sum('total');

        $todayInvoiceCount = Invoice::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid')
            ->count();

        $weekRevenue = Invoice::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('payment_status', 'paid')
            ->sum('total');

        $monthRevenue = Invoice::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid')
            ->sum('total');

        $yearRevenue = Invoice::whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid')
            ->sum('total');

        // Hóa đơn gần đây
        $recentInvoices = Invoice::with(['user', 'barber.user', 'services', 'appointment'])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayAppointments',
            'pendingAppointments',
            'customers',
            'unreadMessages',
            'upcomingAppointments',
            'totalReviews',
            'averageRating',
            'recentReviews',
            'lowRatingReviews',
            'topServices',
            'topBarbers',
            'todayRevenue',
            'todayInvoiceCount',
            'weekRevenue',
            'monthRevenue',
            'yearRevenue',
            'recentInvoices'
        ));
    }
}
