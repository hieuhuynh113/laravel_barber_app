<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Review;
use App\Notifications\NewReviewNotification;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('frontend.profile.index', compact('user'));
    }

    public function appointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('frontend.profile.appointments', compact('appointments', 'user'));
    }

    /**
     * Hiển thị chi tiết lịch hẹn
     */
    public function appointmentDetail($id)
    {
        $user = Auth::user();
        $appointment = Appointment::with(['services', 'barber.user'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return view('frontend.profile.appointment_detail', compact('appointment', 'user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('frontend.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];

        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu avatar mới vào storage/public/avatars
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Cập nhật hồ sơ thành công!');
    }

    public function reviews()
    {
        $user = Auth::user();
        $reviews = Review::where('user_id', $user->id)
            ->with(['service', 'barber'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.profile.reviews', compact('reviews', 'user'));
    }

    public function deleteReview($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Xóa hình ảnh đánh giá nếu có
        if ($review->images) {
            $images = json_decode($review->images, true);
            foreach ($images as $image) {
                $imagePath = str_replace('storage/', 'public/', $image);
                \Illuminate\Support\Facades\Storage::delete($imagePath);
            }
        }

        $review->delete();

        return redirect()->route('profile.reviews')->with('success', 'Đánh giá đã được xóa thành công!');
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Kiểm tra xem người dùng đã đánh giá dịch vụ này trong lịch hẹn này chưa
        $existingReview = Review::where('user_id', Auth::id())
            ->where('service_id', $validated['service_id'])
            ->where('barber_id', $validated['barber_id'])
            ->where('appointment_id', $validated['appointment_id'])
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá dịch vụ này với thợ cắt tóc này rồi!');
        }

        // Kiểm tra xem người dùng đã sử dụng dịch vụ này chưa
        $hasUsedService = \App\Models\Appointment::where('user_id', Auth::id())
            ->where('barber_id', $validated['barber_id'])
            ->where('status', 'completed')
            ->whereHas('services', function($query) use ($validated) {
                $query->where('services.id', $validated['service_id']);
            })
            ->exists();

        if (!$hasUsedService) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá dịch vụ mà bạn đã sử dụng!');
        }

        $review = new Review();
        $review->user_id = Auth::id();
        $review->service_id = $validated['service_id'];
        $review->barber_id = $validated['barber_id'];
        $review->appointment_id = $validated['appointment_id'];
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'] ?? '';

        // Xử lý tải lên hình ảnh
        if ($request->hasFile('review_images')) {
            $images = [];
            foreach ($request->file('review_images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = 'storage/' . $path;
            }
            $review->images = json_encode($images);
        }

        $review->save();

        // Gửi thông báo cho admin về đánh giá mới
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewReviewNotification($review));

        // Gửi thông báo cho thợ cắt tóc nếu đánh giá thấp
        if ($review->rating <= 2) {
            $barberUser = $review->barber->user;
            $barberUser->notify(new NewReviewNotification($review));
        }

        return redirect()->route('profile.appointments')->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.'
        ]);

        $userId = Auth::id();
        $user = User::findOrFail($userId);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Hủy lịch hẹn
     *
     * @param int $id ID của lịch hẹn
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelAppointment($id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra xem lịch hẹn có thể hủy không
        if ($appointment->status === 'canceled') {
            return redirect()->route('profile.appointments')->with('error', 'Lịch hẹn này đã bị hủy trước đó.');
        }

        if ($appointment->status === 'completed') {
            return redirect()->route('profile.appointments')->with('error', 'Không thể hủy lịch hẹn đã hoàn thành.');
        }

        // Kiểm tra thời gian hủy lịch (có thể thêm logic kiểm tra thời gian hủy lịch ở đây)
        // Ví dụ: Chỉ cho phép hủy lịch trước 24 giờ
        $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        $now = \Carbon\Carbon::now();

        if ($appointmentDate->diffInHours($now) < 24 && $appointmentDate > $now) {
            return redirect()->route('profile.appointments')->with('error', 'Bạn chỉ có thể hủy lịch hẹn trước 24 giờ.');
        }

        // Cập nhật trạng thái lịch hẹn
        $oldStatus = $appointment->status;
        $appointment->status = 'canceled';
        $appointment->save();

        // Giảm số lượng đặt chỗ trong time slot
        if ($oldStatus != 'canceled' && $appointment->time_slot) {
            $timeSlot = \App\Models\TimeSlot::where('barber_id', $appointment->barber_id)
                ->where('date', $appointment->appointment_date)
                ->where('time_slot', $appointment->time_slot)
                ->first();

            if ($timeSlot) {
                $timeSlot->decrementBookedCount();
            }
        }

        // Gửi email thông báo hủy lịch hẹn cho khách hàng
        try {
            \Illuminate\Support\Facades\Mail::to($appointment->email)
                ->send(new \App\Mail\AppointmentCanceled($appointment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Không thể gửi email hủy lịch hẹn: " . $e->getMessage());
        }

        // Gửi thông báo cho admin về việc hủy lịch hẹn
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new \App\Notifications\AppointmentCanceledNotification($appointment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Không thể gửi thông báo hủy lịch hẹn: " . $e->getMessage());
        }

        return redirect()->route('profile.appointments')->with('success', 'Lịch hẹn đã được hủy thành công.');
    }
}
