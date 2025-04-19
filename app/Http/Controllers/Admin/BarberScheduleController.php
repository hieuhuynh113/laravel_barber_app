<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\BarberSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BarberScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $barberId = $request->get('barber_id');
        $barbers = Barber::with('user')->get();

        if (!$barberId && $barbers->isNotEmpty()) {
            $barberId = $barbers->first()->id;
        }

        // Kiểm tra xem thợ cắt tóc có tồn tại không
        if ($barberId) {
            $barber = Barber::find($barberId);
            if (!$barber) {
                return redirect()->route('admin.barbers.index')
                    ->with('error', 'Không tìm thấy thông tin thợ cắt tóc.');
            }
        }

        $schedules = collect();

        if ($barberId) {
            $schedules = BarberSchedule::where('barber_id', $barberId)
                ->orderBy('day_of_week')
                ->get();

            // Đảm bảo có đủ 7 ngày trong tuần
            $existingDays = $schedules->pluck('day_of_week')->toArray();

            for ($day = 0; $day <= 6; $day++) {
                if (!in_array($day, $existingDays)) {
                    $defaultStartTime = Carbon::createFromTime(8, 0, 0);
                    $defaultEndTime = Carbon::createFromTime(17, 0, 0);

                    $schedules->push(new BarberSchedule([
                        'barber_id' => $barberId,
                        'day_of_week' => $day,
                        'start_time' => $defaultStartTime,
                        'end_time' => $defaultEndTime,
                        'is_day_off' => false,
                        'max_appointments' => 3,
                    ]));
                }
            }

            $schedules = $schedules->sortBy('day_of_week');
        }

        return view('admin.schedules.index', compact('barbers', 'schedules', 'barberId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barbers = Barber::with('user')->get();
        return view('admin.schedules.create', compact('barbers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_day_off' => 'boolean',
            'max_appointments' => 'required|integer|min:1|max:20',
        ]);

        // Kiểm tra xem đã có lịch cho ngày này chưa
        $existingSchedule = BarberSchedule::where('barber_id', $request->barber_id)
            ->where('day_of_week', $request->day_of_week)
            ->first();

        if ($existingSchedule) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['day_of_week' => 'Lịch làm việc cho ngày này đã tồn tại.']);
        }

        $schedule = new BarberSchedule();
        $schedule->barber_id = $request->barber_id;
        $schedule->day_of_week = $request->day_of_week;
        $schedule->start_time = $request->start_time;
        $schedule->end_time = $request->end_time;
        $schedule->is_day_off = $request->has('is_day_off');
        $schedule->max_appointments = $request->max_appointments;
        $schedule->save();

        // Cập nhật các time slots cho ngày này nếu không phải là ngày nghỉ
        if (!$schedule->is_day_off) {
            $this->updateTimeSlotsForDay($schedule->barber_id, $schedule->day_of_week, $schedule->max_appointments);
        }

        return redirect()->route('admin.schedules.index', ['barber_id' => $request->barber_id])
            ->with('success', 'Lịch làm việc đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = BarberSchedule::with('barber.user')->findOrFail($id);
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $barbers = Barber::with('user')->get();
        return view('admin.schedules.edit', compact('schedule', 'barbers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_day_off' => 'boolean',
            'max_appointments' => 'required|integer|min:1|max:20',
        ]);

        $schedule = BarberSchedule::findOrFail($id);

        // Lưu giá trị cũ để kiểm tra thay đổi
        $oldMaxAppointments = $schedule->max_appointments;
        $oldIsDayOff = $schedule->is_day_off;

        $schedule->start_time = $request->start_time;
        $schedule->end_time = $request->end_time;
        $schedule->is_day_off = $request->has('is_day_off');
        $schedule->max_appointments = $request->max_appointments;
        $schedule->save();

        // Cập nhật các time slots nếu max_appointments thay đổi hoặc trạng thái ngày nghỉ thay đổi
        if (!$schedule->is_day_off && ($oldMaxAppointments != $schedule->max_appointments || $oldIsDayOff != $schedule->is_day_off)) {
            $this->updateTimeSlotsForDay($schedule->barber_id, $schedule->day_of_week, $schedule->max_appointments);
        }

        return redirect()->route('admin.schedules.index', ['barber_id' => $schedule->barber_id])
            ->with('success', 'Lịch làm việc đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $barberId = $schedule->barber_id;
        $schedule->delete();

        return redirect()->route('admin.schedules.index', ['barber_id' => $barberId])
            ->with('success', 'Lịch làm việc đã được xóa thành công.');
    }

    /**
     * Update multiple schedules at once.
     */
    public function updateBatch(Request $request)
    {
        $barberId = $request->barber_id;
        $dayData = $request->days;

        if (!$barberId || !$dayData) {
            return redirect()->back()->withErrors(['msg' => 'Dữ liệu không hợp lệ.']);
        }

        // Kiểm tra xem thợ cắt tóc có tồn tại không
        $barber = Barber::find($barberId);
        if (!$barber) {
            return redirect()->route('admin.barbers.index')
                ->with('error', 'Không tìm thấy thông tin thợ cắt tóc.');
        }

        foreach ($dayData as $day => $data) {
            // Kiểm tra ngày làm việc hay ngày nghỉ
            $isDayOff = isset($data['is_day_off']);

            $schedule = BarberSchedule::where('barber_id', $barberId)
                ->where('day_of_week', $day)
                ->first();

            if (!$schedule) {
                $schedule = new BarberSchedule();
                $schedule->barber_id = $barberId;
                $schedule->day_of_week = $day;
            }

            $schedule->is_day_off = $isDayOff;

            // Thiết lập giá trị mặc định cho các trường bắt buộc
            $defaultStartTime = '08:00';
            $defaultEndTime = '17:00';
            $defaultMaxAppointments = 3;

            // Lưu giá trị max_appointments cũ để kiểm tra xem có thay đổi không
            $oldMaxAppointments = $schedule->exists ? $schedule->max_appointments : null;

            if (!$isDayOff) {
                // Nếu là ngày làm việc, cập nhật thông tin thời gian
                $schedule->start_time = $data['start_time'] ?? $defaultStartTime;
                $schedule->end_time = $data['end_time'] ?? $defaultEndTime;
                $schedule->max_appointments = $data['max_appointments'] ?? $defaultMaxAppointments;
            } else {
                // Nếu là ngày nghỉ, vẫn cần thiết lập giá trị cho các trường bắt buộc
                // để tránh lỗi SQL khi tạo mới bản ghi
                if (!$schedule->exists) {
                    $schedule->start_time = $defaultStartTime;
                    $schedule->end_time = $defaultEndTime;
                    $schedule->max_appointments = $defaultMaxAppointments;
                }
                // Không cập nhật các trường này nếu bản ghi đã tồn tại
            }

            $schedule->save();

            // Cập nhật các time slots nếu max_appointments thay đổi
            if (!$isDayOff && $oldMaxAppointments !== null && $oldMaxAppointments != $schedule->max_appointments) {
                $this->updateTimeSlotsForDay($barberId, $day, $schedule->max_appointments);
            }
        }

        return redirect()->route('admin.schedules.index', ['barber_id' => $barberId])
            ->with('success', 'Lịch làm việc đã được cập nhật thành công.');
    }

    /**
     * Cập nhật các time slots cho một ngày trong tuần cụ thể
     */
    private function updateTimeSlotsForDay($barberId, $dayOfWeek, $maxBookings)
    {
        // Lấy tất cả các ngày trong tương lai có cùng thứ trong tuần
        $today = \Carbon\Carbon::today();
        $futureDates = [];

        // Lấy các ngày trong 3 tháng tới
        for ($i = 0; $i < 90; $i++) {
            $date = $today->copy()->addDays($i);
            if ($date->dayOfWeek == $dayOfWeek) {
                $futureDates[] = $date->format('Y-m-d');
            }
        }

        // Cập nhật tất cả các time slots cho các ngày này
        foreach ($futureDates as $date) {
            \App\Models\TimeSlot::where('barber_id', $barberId)
                ->where('date', $date)
                ->update(['max_bookings' => $maxBookings]);
        }
    }
}
