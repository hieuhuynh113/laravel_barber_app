<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Review;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');
        $search = $request->input('search');

        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,barber,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }

        // Nếu người dùng là thợ cắt tóc, tạo hồ sơ thợ cắt tóc
        if ($request->role === 'barber') {
            $user->barber()->create([
                'bio' => $request->bio ?? '',
                'experience' => $request->experience ?? 0,
                'specialties' => $request->specialties ?? '',
            ]);
        }

        return redirect()->route('admin.users.index', ['role' => $request->role])
            ->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function show(User $user)
    {
        // Tải các mối quan hệ cần thiết
        $user->load(['barber', 'news']);

        // Lấy lịch hẹn gần đây cho tất cả các loại người dùng
        if ($user->role === 'barber' && $user->barber) {
            // Nếu là thợ cắt tóc, lấy lịch hẹn dựa trên barber_id
            $recentAppointments = \App\Models\Appointment::where('barber_id', $user->barber->id)
                ->with(['user', 'services', 'barber.user'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Nếu là khách hàng hoặc admin, lấy lịch hẹn dựa trên user_id
            $recentAppointments = \App\Models\Appointment::where('user_id', $user->id)
                ->with(['services', 'barber.user'])
                ->latest()
                ->take(5)
                ->get();
        }

        // Nếu là khách hàng, lấy thêm đánh giá
        if ($user->role === 'customer') {
            // Lấy đánh giá của khách hàng
            $reviews = Review::where('user_id', $user->id)
                ->with(['service', 'barber.user'])
                ->latest()
                ->paginate(5, ['*'], 'reviews_page');

            // Tính toán thống kê đánh giá
            $reviewsCount = Review::where('user_id', $user->id)->count();
            $averageRating = Review::where('user_id', $user->id)->avg('rating') ?? 0;

            // Phân bố đánh giá theo số sao
            $ratingDistribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $count = Review::where('user_id', $user->id)->where('rating', $i)->count();
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $reviewsCount > 0 ? round(($count / $reviewsCount) * 100, 1) : 0
                ];
            }

            // Thợ cắt tóc được đánh giá cao nhất bởi khách hàng này
            $topBarbers = Barber::with('user')
                ->whereHas('reviews', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->withCount(['reviews' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->withAvg(['reviews' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }], 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->take(5)
                ->get();

            return view('admin.users.show', compact(
                'user',
                'recentAppointments',
                'reviews',
                'reviewsCount',
                'averageRating',
                'ratingDistribution',
                'topBarbers'
            ));
        }

        return view('admin.users.show', compact('user', 'recentAppointments'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,barber,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
        ]);

        $oldRole = $user->role;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->password = Hash::make($request->password);
            $user->save();
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }

        // Xử lý khi thay đổi vai trò
        if ($oldRole !== $request->role) {
            // Nếu chuyển thành thợ cắt tóc, tạo hồ sơ thợ cắt tóc nếu chưa có
            if ($request->role === 'barber' && !$user->barber) {
                $user->barber()->create([
                    'bio' => $request->bio ?? '',
                    'experience' => $request->experience ?? 0,
                    'specialties' => $request->specialties ?? '',
                ]);
            }
            // Nếu từ thợ cắt tóc chuyển sang vai trò khác, xử lý các ràng buộc liên quan
            else if ($oldRole === 'barber' && $request->role !== 'barber') {
                // Xử lý lịch hẹn hiện tại của thợ cắt tóc này
                // Có thể thêm logic xử lý ở đây
            }
        }

        // Cập nhật thông tin thợ cắt tóc nếu có
        if ($request->role === 'barber' && $user->barber && ($request->filled('bio') || $request->filled('experience') || $request->filled('specialties'))) {
            $user->barber()->update([
                'bio' => $request->bio ?? $user->barber->bio,
                'experience' => $request->experience ?? $user->barber->experience,
                'specialties' => $request->specialties ?? $user->barber->specialties,
            ]);
        }

        return redirect()->route('admin.users.index', ['role' => $request->role])
            ->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    public function destroy(User $user)
    {
        // Xóa avatar nếu có
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Xóa thông tin liên quan
        if ($user->role === 'barber') {
            $user->barber()->delete();
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'Người dùng đã được xóa thành công.');
    }
}