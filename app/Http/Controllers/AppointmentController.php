<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function step1()
    {
        $services = Service::active()->with('category')->get();
        
        return view('frontend.appointment.step1', compact('services'));
    }
    
    public function postStep1(Request $request)
    {
        $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
        ]);
        
        $services = Service::whereIn('id', $request->services)->get();
        
        // Lưu dịch vụ đã chọn vào session
        session(['appointment_services' => $services]);
        
        return redirect()->route('appointment.step2');
    }
    
    public function step2()
    {
        if (!session('appointment_services')) {
            return redirect()->route('appointment.step1');
        }
        
        $barbers = Barber::active()->with('user')->get();
        
        return view('frontend.appointment.step2', compact('barbers'));
    }
    
    public function postStep2(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
        ]);
        
        $barber = Barber::findOrFail($request->barber_id);
        
        // Lưu thợ cắt tóc đã chọn vào session
        session(['appointment_barber' => $barber]);
        
        return redirect()->route('appointment.step3');
    }
    
    public function step3()
    {
        if (!session('appointment_services') || !session('appointment_barber')) {
            return redirect()->route('appointment.step1');
        }
        
        $barber = session('appointment_barber');
        $currentDate = Carbon::now()->format('Y-m-d');
        $timeSlots = $this->getAvailableTimeSlots($barber->id, $currentDate);
        
        return view('frontend.appointment.step3', compact('currentDate', 'timeSlots'));
    }
    
    private function getAvailableTimeSlots($barberId, $date)
    {
        // Lấy lịch làm việc của thợ
        $barber = Barber::with(['schedules' => function($query) use ($date) {
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $query->where('day_of_week', $dayOfWeek);
        }])->findOrFail($barberId);

        // Nếu là ngày nghỉ
        if ($barber->schedules->isEmpty() || $barber->schedules->first()->is_day_off) {
            return [];
        }

        $schedule = $barber->schedules->first();
        
        // Tạo các slot thời gian từ giờ bắt đầu đến giờ kết thúc (mỗi slot 30 phút)
        $startTime = Carbon::parse($date . ' ' . $schedule->start_time->format('H:i:s'));
        $endTime = Carbon::parse($date . ' ' . $schedule->end_time->format('H:i:s'));
        
        $slots = [];
        $currentTime = clone $startTime;
        
        while ($currentTime < $endTime) {
            $slotStart = clone $currentTime;
            $currentTime->addMinutes(30);
            
            if ($currentTime <= $endTime) {
                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end' => $currentTime->format('H:i'),
                    'formatted' => $slotStart->format('H:i') . ' - ' . $currentTime->format('H:i'),
                ];
            }
        }
        
        // Lấy các lịch hẹn đã đặt trong ngày
        $appointments = Appointment::where('barber_id', $barberId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'canceled')
            ->get();
        
        // Đánh dấu các slot đã được đặt
        foreach ($slots as &$slot) {
            $slotStart = Carbon::parse($date . ' ' . $slot['start']);
            $slotEnd = Carbon::parse($date . ' ' . $slot['end']);
            
            $booked = false;
            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($date . ' ' . $appointment->start_time->format('H:i:s'));
                $appointmentEnd = Carbon::parse($date . ' ' . $appointment->end_time->format('H:i:s'));
                
                if (
                    ($slotStart >= $appointmentStart && $slotStart < $appointmentEnd) ||
                    ($slotEnd > $appointmentStart && $slotEnd <= $appointmentEnd) ||
                    ($slotStart <= $appointmentStart && $slotEnd >= $appointmentEnd)
                ) {
                    $booked = true;
                    break;
                }
            }
            
            $slot['booked'] = $booked;
        }
        
        // Kiểm tra số lượng lịch hẹn trong ngày theo thiết lập của thợ cắt tóc
        $appointmentCount = $appointments->count();
        $maxAppointments = $schedule->max_appointments ?? 3; // Mặc định là 3 nếu không được thiết lập
        
        // Nếu đã đạt tới số lượng lịch hẹn tối đa, đánh dấu tất cả slot là đã đặt
        if ($appointmentCount >= $maxAppointments) {
            foreach ($slots as &$slot) {
                $slot['booked'] = true;
            }
        }
        
        // Lọc ra các khung giờ còn trống
        $availableSlots = array_filter($slots, function($slot) {
            return !$slot['booked'];
        });
        
        return array_values($availableSlots);
    }
    
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'barber_id' => 'required|exists:barbers,id',
        ]);
        
        $barberId = $request->barber_id;
        $date = $request->date;
        
        $timeSlots = $this->getAvailableTimeSlots($barberId, $date);
        
        return response()->json([
            'timeSlots' => $timeSlots,
            'date' => $date,
        ]);
    }
    
    public function postStep3(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required',
        ]);
        
        list($startTime, $endTime) = explode(' - ', $request->time_slot);
        
        // Lưu thời gian đã chọn vào session
        session([
            'appointment_date' => $request->date,
            'appointment_start_time' => $startTime,
            'appointment_end_time' => $endTime,
        ]);
        
        return redirect()->route('appointment.step4');
    }
    
    public function step4()
    {
        if (!session('appointment_services') || !session('appointment_barber') || !session('appointment_date')) {
            return redirect()->route('appointment.step1');
        }
        
        // Nếu đã đăng nhập, lấy thông tin người dùng
        $user = Auth::user();
        
        return view('frontend.appointment.step4', compact('user'));
    }
    
    public function postStep4(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);
        
        // Lưu thông tin khách hàng vào session
        session([
            'appointment_customer_name' => $request->name,
            'appointment_customer_email' => $request->email,
            'appointment_customer_phone' => $request->phone,
            'appointment_notes' => $request->notes,
        ]);
        
        return redirect()->route('appointment.step5');
    }
    
    public function step5()
    {
        if (!session('appointment_services') || !session('appointment_barber') || !session('appointment_date') || !session('appointment_customer_name')) {
            return redirect()->route('appointment.step1');
        }
        
        return view('frontend.appointment.step5');
    }
    
    public function postStep5(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer',
        ]);
        
        // Lưu phương thức thanh toán vào session
        session(['appointment_payment_method' => $request->payment_method]);
        
        return redirect()->route('appointment.step6');
    }
    
    public function step6()
    {
        if (!session('appointment_services') || !session('appointment_barber') || !session('appointment_date') || !session('appointment_customer_name') || !session('appointment_payment_method')) {
            return redirect()->route('appointment.step1');
        }
        
        // Nếu lịch hẹn đã được tạo, lấy từ database
        if (session('appointment_created') && session('appointment_id')) {
            $appointment = \App\Models\Appointment::with(['services', 'barber.user'])->find(session('appointment_id'));
            
            if ($appointment) {
                return view('frontend.appointment.step6', compact('appointment'));
            }
        }
        
        // Nếu chưa tạo lịch hẹn, chuyển hướng đến trang lưu lịch hẹn
        return redirect()->route('appointment.complete');
    }
    
    public function complete(Request $request)
    {
        if (!session('appointment_services') || !session('appointment_barber') || !session('appointment_date') || !session('appointment_customer_name') || !session('appointment_payment_method')) {
            return redirect()->route('appointment.step1');
        }
        
        // Nếu đã tạo lịch hẹn rồi, chuyển hướng đến trang xác nhận
        if (session('appointment_created')) {
            return redirect()->route('appointment.step6');
        }
        
        // Lấy dữ liệu từ session
        $services = session('appointment_services');
        $barber = session('appointment_barber');
        $date = session('appointment_date');
        $startTime = session('appointment_start_time');
        $endTime = session('appointment_end_time');
        $customerName = session('appointment_customer_name');
        $customerEmail = session('appointment_customer_email');
        $customerPhone = session('appointment_customer_phone');
        $notes = session('appointment_notes');
        $paymentMethod = session('appointment_payment_method');
        
        // Tạo mã đặt chỗ
        $bookingCode = 'BK-' . strtoupper(\Illuminate\Support\Str::random(8));
        
        // Tạo lịch hẹn
        $appointment = new \App\Models\Appointment();
        $appointment->user_id = \Illuminate\Support\Facades\Auth::id(); // Nếu đã đăng nhập
        $appointment->barber_id = $barber->id;
        $appointment->appointment_date = $date;
        $appointment->start_time = $startTime;
        $appointment->end_time = $endTime;
        $appointment->status = 'pending';
        $appointment->booking_code = $bookingCode;
        $appointment->customer_name = $customerName;
        $appointment->email = $customerEmail;
        $appointment->phone = $customerPhone;
        $appointment->payment_method = $paymentMethod;
        $appointment->payment_status = 'pending';
        $appointment->notes = $notes;
        $appointment->save();
        
        // Thêm các dịch vụ đã chọn
        foreach ($services as $service) {
            $appointment->services()->attach($service->id, ['price' => $service->price]);
        }
        
        // Gửi email xác nhận
        // TODO: Tạo email template và gửi email
        
        // Đánh dấu lịch hẹn đã được tạo
        session(['appointment_created' => true]);
        session(['appointment_id' => $appointment->id]);
        
        // Trả về trang xác nhận
        return redirect()->route('appointment.step6');
    }
}
