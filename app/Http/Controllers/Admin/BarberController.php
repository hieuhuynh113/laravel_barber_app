<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\User;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = User::where('role', 'barber')
            ->with('barber')
            ->latest()
            ->paginate(10);

        return view('admin.barbers.index', compact('barbers'));
    }

    public function create()
    {
        return view('admin.barbers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|integer',
            'specialties' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ], [
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'barber',
            'status' => $request->status,
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }

        $user->barber()->create([
            'bio' => $request->bio,
            'experience' => $request->experience,
            'specialties' => $request->specialties,
        ]);

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt tóc đã được tạo thành công.');
    }

    public function show(User $barber)
    {
        $barber->load('barber', 'appointments.services');

        // Kiểm tra xem người dùng có thông tin thợ cắt tóc hay không
        if (!$barber->barber) {
            return redirect()->route('admin.barbers.index')
                ->with('error', 'Không tìm thấy thông tin thợ cắt tóc.');
        }

        // Lấy đánh giá của thợ cắt tóc
        $reviews = Review::where('barber_id', $barber->barber->id)
            ->with(['user', 'service'])
            ->latest()
            ->paginate(5, ['*'], 'reviews_page');

        // Tính toán thống kê đánh giá
        $reviewsCount = Review::where('barber_id', $barber->barber->id)->count();
        $averageRating = Review::where('barber_id', $barber->barber->id)->avg('rating') ?? 0;

        // Phân bố đánh giá theo số sao
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('barber_id', $barber->barber->id)->where('rating', $i)->count();
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $reviewsCount > 0 ? round(($count / $reviewsCount) * 100, 1) : 0
            ];
        }

        // Dịch vụ được đánh giá cao nhất của thợ cắt tóc
        $topServices = Service::whereHas('reviews', function($query) use ($barber) {
                $query->where('barber_id', $barber->barber->id);
            })
            ->withCount(['reviews' => function($query) use ($barber) {
                $query->where('barber_id', $barber->barber->id);
            }])
            ->withAvg(['reviews' => function($query) use ($barber) {
                $query->where('barber_id', $barber->barber->id);
            }], 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Lấy lịch làm việc của thợ cắt tóc
        $schedules = $barber->barber->schedules()->orderBy('day_of_week')->get();

        // Đảm bảo có đủ 7 ngày trong tuần
        $existingDays = $schedules->pluck('day_of_week')->toArray();

        for ($day = 0; $day <= 6; $day++) {
            if (!in_array($day, $existingDays)) {
                $defaultStartTime = \Carbon\Carbon::createFromTime(8, 0, 0);
                $defaultEndTime = \Carbon\Carbon::createFromTime(17, 0, 0);

                $schedules->push(new \App\Models\BarberSchedule([
                    'barber_id' => $barber->barber->id,
                    'day_of_week' => $day,
                    'start_time' => $defaultStartTime,
                    'end_time' => $defaultEndTime,
                    'is_day_off' => false,
                    'max_appointments' => 3,
                ]));
            }
        }

        $schedules = $schedules->sortBy('day_of_week');

        return view('admin.barbers.show', compact(
            'barber',
            'reviews',
            'reviewsCount',
            'averageRating',
            'ratingDistribution',
            'topServices',
            'schedules'
        ));
    }

    public function edit(User $barber)
    {
        return view('admin.barbers.edit', compact('barber'));
    }

    public function update(Request $request, User $barber)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $barber->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|integer',
            'specialties' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        $barber->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $barber->password = Hash::make($request->password);
            $barber->save();
        }

        if ($request->hasFile('avatar')) {
            if ($barber->avatar) {
                Storage::disk('public')->delete($barber->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $barber->avatar = $avatarPath;
            $barber->save();
        }

        $barber->barber()->update([
            'bio' => $request->bio,
            'experience' => $request->experience,
            'specialties' => $request->specialties,
        ]);

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thông tin thợ cắt tóc đã được cập nhật thành công.');
    }

    public function destroy(User $barber)
    {
        // Xóa avatar nếu có
        if ($barber->avatar) {
            Storage::disk('public')->delete($barber->avatar);
        }

        // Xóa thông tin liên quan trước
        $barber->barber()->delete();
        $barber->delete();

        return redirect()->route('admin.barbers.index')
            ->with('success', 'Thợ cắt tóc đã được xóa thành công.');
    }
}