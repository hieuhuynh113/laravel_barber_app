<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $user->load(['barber', 'appointments.services', 'news']);
        return view('admin.users.show', compact('user'));
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