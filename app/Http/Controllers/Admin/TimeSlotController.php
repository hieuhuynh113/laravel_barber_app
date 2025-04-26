<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    /**
     * Hiển thị danh sách các time slots
     */
    public function index(Request $request)
    {
        $barberId = $request->input('barber_id');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $barbers = Barber::with('user')->get();
        $timeSlots = collect();

        if ($barberId) {
            $timeSlots = TimeSlot::where('barber_id', $barberId)
                ->where('date', $date)
                ->orderBy('time_slot')
                ->get();
        }

        return view('admin.time_slots.index', compact('barbers', 'timeSlots', 'barberId', 'date'));
    }

    /**
     * Cập nhật số lượng khách hàng tối đa cho một time slot
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'max_bookings' => 'required|integer|min:1|max:20',
        ]);

        $timeSlot = TimeSlot::findOrFail($id);
        $timeSlot->max_bookings = $request->max_bookings;
        $timeSlot->save();

        return redirect()->route('admin.time-slots.index', [
            'barber_id' => $timeSlot->barber_id,
            'date' => $timeSlot->date,
        ])->with('success', 'Số lượng khách hàng tối đa đã được cập nhật thành công.');
    }

    /**
     * Cập nhật hàng loạt các time slots
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date',
            'max_bookings' => 'required|integer|min:1|max:20',
        ]);

        $barberId = $request->barber_id;
        $date = $request->date;
        $maxBookings = $request->max_bookings;

        // Cập nhật tất cả time slots của thợ cắt tóc trong ngày
        TimeSlot::where('barber_id', $barberId)
            ->where('date', $date)
            ->update(['max_bookings' => $maxBookings]);

        return redirect()->route('admin.time-slots.index', [
            'barber_id' => $barberId,
            'date' => $date,
        ])->with('success', 'Tất cả các khung giờ đã được cập nhật thành công.');
    }

    /**
     * Tạo các time slots cho một ngày cụ thể
     */
    public function generate(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date',
        ]);

        $barberId = $request->barber_id;
        $date = $request->date;

        // Lấy lịch làm việc của thợ cắt tóc
        $barber = Barber::with(['schedules' => function($query) use ($date) {
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $query->where('day_of_week', $dayOfWeek);
        }])->findOrFail($barberId);

        // Nếu là ngày nghỉ hoặc không có lịch làm việc
        if ($barber->schedules->isEmpty() || $barber->schedules->first()->is_day_off) {
            return redirect()->route('admin.time-slots.index', [
                'barber_id' => $barberId,
                'date' => $date,
            ])->with('error', 'Không thể tạo khung giờ cho ngày nghỉ.');
        }

        $schedule = $barber->schedules->first();
        $maxBookings = $schedule->max_appointments ?? 2;

        // Tạo các giờ cụ thể từ giờ bắt đầu đến giờ kết thúc (mỗi slot 60 phút)
        $startTime = Carbon::parse($date . ' ' . $schedule->start_time->format('H:i:s'));
        $endTime = Carbon::parse($date . ' ' . $schedule->end_time->format('H:i:s'));

        $currentTime = clone $startTime;
        $count = 0;

        while ($currentTime < $endTime) {
            $formattedTime = $currentTime->format('H:i');

            // Tìm hoặc tạo time slot trong database
            $timeSlot = TimeSlot::firstOrCreate(
                [
                    'barber_id' => $barberId,
                    'date' => $date,
                    'time_slot' => $formattedTime,
                ],
                [
                    'booked_count' => 0,
                    'max_bookings' => $maxBookings,
                ]
            );

            // Cập nhật max_bookings nếu khác với giá trị trong lịch làm việc
            if ($timeSlot->max_bookings != $maxBookings) {
                $timeSlot->max_bookings = $maxBookings;
                $timeSlot->save();
            }

            $currentTime->addMinutes(60);
            $count++;
        }

        return redirect()->route('admin.time-slots.index', [
            'barber_id' => $barberId,
            'date' => $date,
        ])->with('success', "Đã tạo $count khung giờ thành công.");
    }
}
