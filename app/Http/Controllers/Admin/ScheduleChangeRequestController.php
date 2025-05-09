<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleChangeRequest;
use App\Models\BarberSchedule;
use App\Models\Barber;
use App\Notifications\ScheduleChangeRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ScheduleChangeRequestController extends Controller
{
    /**
     * Hiển thị danh sách các yêu cầu thay đổi lịch làm việc
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $barberId = $request->input('barber_id');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $perPage = $request->input('per_page', 10);

        $query = ScheduleChangeRequest::with(['barber.user']);

        // Lọc theo trạng thái
        if ($status) {
            $query->where('status', $status);
        }

        // Lọc theo thợ cắt tóc
        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        // Tìm kiếm theo tên thợ cắt tóc hoặc lý do
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('barber.user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sắp xếp
        if ($sortBy === 'barber_name') {
            // Sử dụng subquery để tránh join nhiều bảng
            $query->orderBy(function($query) use ($sortOrder) {
                $query->select('users.name')
                      ->from('users')
                      ->join('barbers', 'users.id', '=', 'barbers.user_id')
                      ->whereColumn('barbers.id', 'schedule_change_requests.barber_id')
                      ->limit(1);
            }, $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $requests = $query->paginate($perPage)->withQueryString();
        $barbers = Barber::with('user')->get();

        return view('admin.schedule-requests.index_new', compact(
            'requests',
            'barbers',
            'status',
            'barberId',
            'search',
            'sortBy',
            'sortOrder',
            'perPage'
        ));
    }

    /**
     * Hiển thị chi tiết yêu cầu thay đổi lịch làm việc
     */
    public function show($id)
    {
        $request = ScheduleChangeRequest::with(['barber.user'])->findOrFail($id);
        return view('admin.schedule-requests.show_new', compact('request'));
    }

    /**
     * Xử lý yêu cầu thay đổi lịch làm việc (phê duyệt hoặc từ chối)
     *
     * @param Request $request
     * @param int $id
     * @param string $action 'approve' hoặc 'reject'
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processRequest(Request $request, $id, $action)
    {
        $scheduleRequest = ScheduleChangeRequest::with(['barber.user'])->findOrFail($id);

        // Cập nhật trạng thái yêu cầu
        $scheduleRequest->status = $action;
        $scheduleRequest->admin_notes = $request->admin_notes;
        $scheduleRequest->save();

        // Nếu phê duyệt, cập nhật lịch làm việc
        if ($action === 'approved') {
            $this->updateBarberSchedule($scheduleRequest);
        }

        // Gửi thông báo cho thợ cắt tóc
        $scheduleRequest->barber->user->notify(new ScheduleChangeRequestNotification($scheduleRequest, $action));

        $message = $action === 'approved'
            ? 'Yêu cầu thay đổi lịch làm việc đã được phê duyệt thành công.'
            : 'Yêu cầu thay đổi lịch làm việc đã bị từ chối.';

        return redirect()->route('admin.schedule-requests.index')->with('success', $message);
    }

    /**
     * Cập nhật lịch làm việc của thợ cắt tóc
     *
     * @param ScheduleChangeRequest $scheduleRequest
     * @return void
     */
    private function updateBarberSchedule(ScheduleChangeRequest $scheduleRequest)
    {
        $schedule = BarberSchedule::where('barber_id', $scheduleRequest->barber_id)
            ->where('day_of_week', $scheduleRequest->day_of_week)
            ->first();

        if ($schedule) {
            // Lưu giá trị cũ để kiểm tra thay đổi
            $oldIsDayOff = $schedule->is_day_off;

            // Cập nhật lịch làm việc
            $schedule->start_time = $scheduleRequest->start_time;
            $schedule->end_time = $scheduleRequest->end_time;
            $schedule->is_day_off = $scheduleRequest->is_day_off;
            $schedule->save();

            // Cập nhật các time slots nếu trạng thái ngày nghỉ thay đổi
            if ($oldIsDayOff != $schedule->is_day_off) {
                // Nếu cần, thêm logic để cập nhật time slots ở đây
            }
        } else {
            // Tạo lịch làm việc mới nếu chưa tồn tại
            $schedule = new BarberSchedule();
            $schedule->barber_id = $scheduleRequest->barber_id;
            $schedule->day_of_week = $scheduleRequest->day_of_week;
            $schedule->start_time = $scheduleRequest->start_time;
            $schedule->end_time = $scheduleRequest->end_time;
            $schedule->is_day_off = $scheduleRequest->is_day_off;
            $schedule->max_appointments = 2; // Giá trị mặc định
            $schedule->save();
        }
    }

    /**
     * Phê duyệt yêu cầu thay đổi lịch làm việc
     */
    public function approve(Request $request, $id)
    {
        return $this->processRequest($request, $id, 'approved');
    }

    /**
     * Từ chối yêu cầu thay đổi lịch làm việc
     */
    public function reject(Request $request, $id)
    {
        return $this->processRequest($request, $id, 'rejected');
    }

    /**
     * Xóa một yêu cầu thay đổi lịch làm việc
     */
    public function destroy($id)
    {
        $scheduleRequest = ScheduleChangeRequest::findOrFail($id);
        $scheduleRequest->delete();

        return redirect()->route('admin.schedule-requests.index')
            ->with('success', 'Yêu cầu thay đổi lịch làm việc đã được xóa thành công.');
    }

    /**
     * Xóa nhiều yêu cầu thay đổi lịch làm việc cùng lúc
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:schedule_change_requests,id'
        ]);

        if (empty($request->ids)) {
            return redirect()->route('admin.schedule-requests.index')
                ->with('error', 'Vui lòng chọn ít nhất một yêu cầu để xóa.');
        }

        $count = ScheduleChangeRequest::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.schedule-requests.index')
            ->with('success', "Đã xóa thành công {$count} yêu cầu thay đổi lịch làm việc.");
    }
}
