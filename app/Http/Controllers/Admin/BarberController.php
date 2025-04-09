<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\User;
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
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|integer',
            'specialties' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean',
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
        return view('admin.barbers.show', compact('barber'));
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