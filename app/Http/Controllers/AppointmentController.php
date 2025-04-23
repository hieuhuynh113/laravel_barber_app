<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\PaymentReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewAppointmentNotification;

class AppointmentController extends Controller
{
    /**
     * Kiểm tra các session cần thiết cho từng bước
     *
     * @param int $step Số bước hiện tại
     * @return \Illuminate\Http\RedirectResponse|null Chuyển hướng nếu thiếu session, null nếu đủ session
     */
    private function checkRequiredSessions($step)
    {
        $requiredSessions = [
            1 => [],
            2 => ['appointment_services'],
            3 => ['appointment_services', 'appointment_barber'],
            4 => ['appointment_services', 'appointment_barber', 'appointment_date'],
            5 => ['appointment_services', 'appointment_barber', 'appointment_date', 'appointment_customer_name'],
            6 => ['appointment_services', 'appointment_barber', 'appointment_date', 'appointment_customer_name', 'appointment_payment_method'],
        ];

        if (!isset($requiredSessions[$step])) {
            return null;
        }

        foreach ($requiredSessions[$step] as $sessionKey) {
            if (!session($sessionKey)) {
                return redirect()->route('appointment.step1');
            }
        }

        return null;
    }

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
        if ($redirect = $this->checkRequiredSessions(2)) {
            return $redirect;
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
        if ($redirect = $this->checkRequiredSessions(3)) {
            return $redirect;
        }

        $barber = session('appointment_barber');
        $currentDate = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $timeSlots = $this->getTimeSlots($barber->id, $currentDate);

        return view('frontend.appointment.step3', compact('currentDate', 'timeSlots'));
    }

    /**
     * Lấy tất cả các khung giờ khả dụng của ngày đó
     * Việc lọc theo thời gian hiện tại sẽ được thực hiện ở phía client
     */
    private function getTimeSlots($barberId, $date)
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

        // Tạo các giờ cụ thể từ giờ bắt đầu đến giờ kết thúc (mỗi slot 30 phút)
        $startTime = Carbon::parse($date . ' ' . $schedule->start_time->format('H:i:s'));
        $endTime = Carbon::parse($date . ' ' . $schedule->end_time->format('H:i:s'));

        $slots = [];
        $currentTime = clone $startTime;

        // Tạo tất cả các khung giờ
        while ($currentTime < $endTime) {
            $formattedTime = $currentTime->format('H:i');

            $slots[] = [
                'time' => $formattedTime,
                'formatted' => $formattedTime,
            ];

            $currentTime->addMinutes(30);
        }

        // Lấy hoặc tạo các time slot trong cơ sở dữ liệu
        foreach ($slots as &$slot) {
            // Lấy giá trị max_appointments từ lịch làm việc của thợ cắt tóc
            $maxBookings = $schedule->max_appointments ?? 2;

            // Tìm hoặc tạo time slot trong database
            $timeSlot = TimeSlot::firstOrCreate(
                [
                    'barber_id' => $barberId,
                    'date' => $date,
                    'time_slot' => $slot['formatted'],
                ],
                [
                    'booked_count' => 0,
                    'max_bookings' => $maxBookings, // Sử dụng giá trị từ lịch làm việc
                ]
            );

            // Cập nhật max_bookings nếu khác với giá trị trong lịch làm việc
            if ($timeSlot->max_bookings != $maxBookings) {
                $timeSlot->max_bookings = $maxBookings;
                $timeSlot->save();
            }

            // Thêm thông tin về số lượng đã đặt và còn trống
            $slot['booked_count'] = $timeSlot->booked_count;
            $slot['max_bookings'] = $timeSlot->max_bookings;
            $slot['available_spots'] = $timeSlot->availableSpots();
            $slot['is_available'] = $timeSlot->isAvailable();
            $slot['booked'] = !$timeSlot->isAvailable();
            $slot['time_slot_id'] = $timeSlot->id;
        }

        // Lọc ra các giờ còn trống
        $availableSlots = array_filter($slots, function($slot) {
            return $slot['is_available'];
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

        // Sử dụng múi giờ của Việt Nam
        $vietnamTimezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($vietnamTimezone);

        // Kiểm tra xem có phải ngày hiện tại không
        $selectedDate = Carbon::parse($date, $vietnamTimezone)->startOfDay();
        $currentDate = $now->copy()->startOfDay();
        $isToday = $selectedDate->equalTo($currentDate);

        // Lấy tất cả các khung giờ của ngày đó
        // Nếu là ngày hiện tại, chúng ta sẽ lọc các khung giờ đã qua ở phía client
        $timeSlots = $this->getTimeSlots($barberId, $date);

        return response()->json([
            'timeSlots' => $timeSlots,
            'date' => $date,
            'current_time' => $now->format('H:i'),
            'is_today' => $isToday,
            'vietnam_time' => $now->format('Y-m-d H:i:s'),
            'timezone' => $vietnamTimezone,
        ]);
    }

    public function postStep3(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required',
        ]);

        $startTime = $request->time_slot;
        // Tính toán giờ kết thúc (30 phút sau giờ bắt đầu)
        $endTime = Carbon::parse($startTime)->addMinutes(30)->format('H:i');

        // Kiểm tra xem giờ này còn trống không
        $timeSlot = TimeSlot::where([
            'barber_id' => session('appointment_barber')->id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
        ])->first();

        if (!$timeSlot || !$timeSlot->isAvailable()) {
            return back()->withErrors(['time_slot' => 'Giờ này đã hết chỗ. Vui lòng chọn giờ khác.']);
        }

        // Lưu thông tin time slot vào session để sử dụng sau này
        session([
            'appointment_time_slot_id' => $timeSlot->id,
            'appointment_date' => $request->date,
            'appointment_start_time' => $startTime,
            'appointment_end_time' => $endTime,
            'appointment_time_slot' => $request->time_slot,
        ]);

        return redirect()->route('appointment.step4');
    }

    public function step4()
    {
        if ($redirect = $this->checkRequiredSessions(4)) {
            return $redirect;
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
        if ($redirect = $this->checkRequiredSessions(5)) {
            return $redirect;
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

        // Nếu chọn thanh toán bằng tiền mặt, chuyển đến bước 6
        if ($request->payment_method === 'cash') {
            return redirect()->route('appointment.step6');
        }

        try {
            // Nếu chọn chuyển khoản, tạo lịch hẹn ngay và chuyển đến trang xác nhận thanh toán
            // Tạo lịch hẹn
            $appointment = $this->createAppointment();

            // Chuyển đến trang xác nhận thanh toán
            return redirect()->route('appointment.payment.confirmation', $appointment->id);
        } catch (\Exception $e) {
            // Ghi log lỗi
            \Log::error("Lỗi khi tạo lịch hẹn: " . $e->getMessage());

            // Thông báo lỗi cho người dùng
            return redirect()->route('appointment.step3')
                ->with('error', $e->getMessage());
        }
    }

    public function step6()
    {
        if ($redirect = $this->checkRequiredSessions(6)) {
            return $redirect;
        }

        // Nếu lịch hẹn đã được tạo, lấy từ database
        if (session('appointment_created') && session('appointment_id')) {
            $appointment = \App\Models\Appointment::with(['services', 'barber.user'])->find(session('appointment_id'));

            if ($appointment) {
                // Xóa các session liên quan đến đặt lịch sau khi hiển thị trang xác nhận
                $this->clearAppointmentSessions();

                return view('frontend.appointment.step6', compact('appointment'));
            }
        }

        // Nếu chưa tạo lịch hẹn, chuyển hướng đến trang lưu lịch hẹn
        return redirect()->route('appointment.complete');
    }

    /**
     * Xóa các session liên quan đến quá trình đặt lịch
     */
    private function clearAppointmentSessions()
    {
        // Danh sách các session cần xóa
        $sessionKeys = [
            'appointment_services',
            'appointment_barber',
            'appointment_date',
            'appointment_start_time',
            'appointment_end_time',
            'appointment_time_slot',
            'appointment_time_slot_id',
            'appointment_customer_name',
            'appointment_customer_email',
            'appointment_customer_phone',
            'appointment_notes',
            'appointment_payment_method',
            'appointment_created',
            'appointment_id'
        ];

        // Xóa từng session
        foreach ($sessionKeys as $key) {
            session()->forget($key);
        }
    }

    /**
     * Tạo lịch hẹn mới từ dữ liệu session
     *
     * @return \App\Models\Appointment
     */
    private function createAppointment()
    {
        // Lấy dữ liệu từ session
        $services = session('appointment_services');
        $barber = session('appointment_barber');
        $date = session('appointment_date');
        $startTime = session('appointment_start_time');
        $endTime = session('appointment_end_time');
        $timeSlotStr = session('appointment_time_slot');
        $timeSlotId = session('appointment_time_slot_id');
        $customerName = session('appointment_customer_name');
        $customerEmail = session('appointment_customer_email');
        $customerPhone = session('appointment_customer_phone');
        $notes = session('appointment_notes');
        $paymentMethod = session('appointment_payment_method');

        // Kiểm tra lại xem time slot còn trống không
        $timeSlot = \App\Models\TimeSlot::find($timeSlotId);
        if (!$timeSlot || !$timeSlot->isAvailable()) {
            throw new \Exception('Giờ này đã hết chỗ. Vui lòng chọn giờ khác.');
        }

        // Tăng số lượng đặt chỗ cho time slot
        $timeSlot->incrementBookedCount();

        // Tạo mã đặt chỗ
        $bookingCode = 'BK-' . strtoupper(\Illuminate\Support\Str::random(8));

        // Tạo lịch hẹn
        $appointment = new \App\Models\Appointment();
        $appointment->user_id = \Illuminate\Support\Facades\Auth::id(); // Nếu đã đăng nhập
        $appointment->barber_id = $barber->id;
        $appointment->appointment_date = $date;
        $appointment->start_time = $startTime;
        $appointment->end_time = $endTime;
        $appointment->time_slot = $timeSlotStr; // Lưu giờ cụ thể thay vì khoảng thời gian
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

        // Gửi email thông báo đã nhận yêu cầu đặt lịch
        try {
            Mail::to($customerEmail)->send(new \App\Mail\AppointmentReceived($appointment));
            \Log::info("Email thông báo đã nhận yêu cầu đặt lịch đã được gửi đến {$customerEmail} cho lịch hẹn {$bookingCode}");
        } catch (\Exception $e) {
            \Log::error("Không thể gửi email thông báo đã nhận yêu cầu đặt lịch: " . $e->getMessage());
        }

        // Gửi thông báo cho admin về lịch hẹn mới
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewAppointmentNotification($appointment));
            \Log::info("Thông báo lịch hẹn mới đã được gửi đến admin cho lịch hẹn {$bookingCode}");
        } catch (\Exception $e) {
            \Log::error("Không thể gửi thông báo lịch hẹn mới: " . $e->getMessage());
        }

        // Đánh dấu lịch hẹn đã được tạo
        session(['appointment_created' => true]);
        session(['appointment_id' => $appointment->id]);

        return $appointment;
    }

    public function complete(Request $request)
    {
        if ($redirect = $this->checkRequiredSessions(6)) {
            return $redirect;
        }

        // Nếu đã tạo lịch hẹn rồi, chuyển hướng đến trang xác nhận
        if (session('appointment_created')) {
            return redirect()->route('appointment.step6');
        }

        try {
            // Tạo lịch hẹn
            $appointment = $this->createAppointment();

            // Trả về trang xác nhận
            return redirect()->route('appointment.step6');
        } catch (\Exception $e) {
            // Ghi log lỗi
            \Log::error("Lỗi khi tạo lịch hẹn: " . $e->getMessage());

            // Thông báo lỗi cho người dùng
            return redirect()->route('appointment.step3')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị trang xác nhận thanh toán cho lịch hẹn
     *
     * @param int $id ID của lịch hẹn
     * @return \Illuminate\View\View
     */
    public function paymentConfirmation($id)
    {
        $appointment = Appointment::with(['services', 'barber.user'])->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (Auth::check() && Auth::id() != $appointment->user_id && !Auth::user()->isAdmin() && !Auth::user()->isBarber()) {
            abort(403, 'Bạn không có quyền truy cập lịch hẹn này');
        }

        // Xóa các session liên quan đến đặt lịch sau khi hiển thị trang xác nhận
        $this->clearAppointmentSessions();

        return view('frontend.appointment.payment_confirmation', compact('appointment'));
    }

    /**
     * Xử lý tải lên biên lai chuyển khoản
     *
     * @param Request $request
     * @param int $id ID của lịch hẹn
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($id);

        // Kiểm tra quyền truy cập
        if (Auth::check() && Auth::id() != $appointment->user_id && !Auth::user()->isAdmin() && !Auth::user()->isBarber()) {
            abort(403, 'Bạn không có quyền truy cập lịch hẹn này');
        }

        // Lưu biên lai
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');

            // Tạo hoặc cập nhật biên lai
            $receipt = PaymentReceipt::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'file_path' => $path,
                    'notes' => $request->notes,
                    'status' => 'pending',
                ]
            );

            // Gửi email thông báo cho admin
            try {
                Mail::to(config('mail.admin_email', 'admin@example.com'))
                    ->send(new \App\Mail\NewPaymentReceipt($appointment, $receipt));

                // Gửi thông báo cho admin về biên lai thanh toán mới
                $admins = User::where('role', 'admin')->get();
                Notification::send($admins, new \App\Notifications\NewPaymentNotification($receipt));
            } catch (\Exception $e) {
                \Log::error("Không thể gửi email/thông báo biên lai: " . $e->getMessage());
            }

            return redirect()->route('profile.appointments')
                ->with('success', 'Biên lai chuyển khoản đã được gửi thành công. Chúng tôi sẽ xác nhận thanh toán của bạn sớm.');
        }

        return back()->with('error', 'Có lỗi xảy ra khi tải lên biên lai. Vui lòng thử lại.');
    }
}
