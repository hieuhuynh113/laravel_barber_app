<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\BarberSchedule;
use App\Models\User;
use App\Notifications\ScheduleChangeRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ScheduleController extends Controller
{
    /**
     * Hiển thị lịch làm việc của thợ cắt tóc
     */
    public function index()
    {
        $barber = Auth::user()->barber;

        // Lấy lịch làm việc của thợ cắt tóc
        $schedules = BarberSchedule::where('barber_id', $barber->id)
            ->orderBy('day_of_week')
            ->get();

        // Đảm bảo có đủ 7 ngày trong tuần
        $existingDays = $schedules->pluck('day_of_week')->toArray();

        for ($day = 0; $day <= 6; $day++) {
            if (!in_array($day, $existingDays)) {
                $defaultStartTime = Carbon::createFromTime(8, 0, 0);
                $defaultEndTime = Carbon::createFromTime(17, 0, 0);

                $schedules->push(new BarberSchedule([
                    'barber_id' => $barber->id,
                    'day_of_week' => $day,
                    'start_time' => $defaultStartTime,
                    'end_time' => $defaultEndTime,
                    'is_day_off' => false,
                    'max_appointments' => 3,
                ]));
            }
        }

        $schedules = $schedules->sortBy('day_of_week');

        return view('barber.schedules.index', compact('schedules'));
    }

    /**
     * Gửi yêu cầu thay đổi lịch làm việc
     */
    public function requestChange(Request $request)
    {
        // Validate dữ liệu đầu vào
        $this->validateScheduleChangeRequest($request);

        $barber = Auth::user()->barber;

        try {
            // Tạo yêu cầu thay đổi lịch làm việc
            $scheduleChangeRequest = $this->createScheduleChangeRequest($request, $barber);

            // Gửi thông báo cho admin
            $this->notifyAdmins($scheduleChangeRequest);

            return redirect()->route('barber.schedules.index')
                ->with('success', 'Yêu cầu thay đổi lịch làm việc đã được gửi và đang chờ phê duyệt.');
        } catch (\Exception $e) {
            return redirect()->route('barber.schedules.index')
                ->with('error', 'Đã xảy ra lỗi khi gửi yêu cầu: ' . $e->getMessage());
        }
    }

    /**
     * Validate dữ liệu yêu cầu thay đổi lịch làm việc
     *
     * @param Request $request
     * @return void
     */
    private function validateScheduleChangeRequest(Request $request)
    {
        // Kiểm tra xem có phải là ngày nghỉ không để áp dụng quy tắc validation phù hợp
        if ($request->has('is_day_off')) {
            $request->validate([
                'day_of_week' => 'required|integer|min:0|max:6',
                'reason' => 'required|string|max:500',
            ]);
        } else {
            $request->validate([
                'day_of_week' => 'required|integer|min:0|max:6',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'reason' => 'required|string|max:500',
            ]);
        }
    }

    /**
     * Tạo yêu cầu thay đổi lịch làm việc
     *
     * @param Request $request
     * @param \App\Models\Barber $barber
     * @return \App\Models\ScheduleChangeRequest
     */
    private function createScheduleChangeRequest(Request $request, $barber)
    {
        return \App\Models\ScheduleChangeRequest::create([
            'barber_id' => $barber->id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->has('is_day_off') ? '08:00' : $request->start_time,
            'end_time' => $request->has('is_day_off') ? '17:00' : $request->end_time,
            'is_day_off' => $request->has('is_day_off') ? true : false,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
    }

    /**
     * Gửi thông báo cho admin về yêu cầu thay đổi lịch làm việc
     *
     * @param \App\Models\ScheduleChangeRequest $scheduleChangeRequest
     * @return void
     */
    private function notifyAdmins($scheduleChangeRequest)
    {
        $admins = User::where('role', 'admin')->get();
        Notification::send(
            $admins,
            new ScheduleChangeRequestNotification($scheduleChangeRequest)
        );
    }
}
