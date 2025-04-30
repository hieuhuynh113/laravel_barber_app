<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function index()
    {
        $user = Auth::user();
        return view('barber.profile.index', compact('user'));
    }
    
    /**
     * Hiển thị form chỉnh sửa thông tin cá nhân
     */
    public function edit()
    {
        $user = Auth::user();
        return view('barber.profile.edit', compact('user'));
    }
    
    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'experience' => 'nullable|integer',
            'specialties' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }
        
        $user->barber()->update([
            'bio' => $request->bio,
            'experience' => $request->experience,
            'specialties' => $request->specialties,
        ]);
        
        return redirect()->route('barber.profile.index')
            ->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }
    
    /**
     * Hiển thị form đổi mật khẩu
     */
    public function changePasswordForm()
    {
        return view('barber.profile.change-password');
    }
    
    /**
     * Cập nhật mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->route('barber.profile.index')
            ->with('success', 'Mật khẩu đã được cập nhật thành công.');
    }
}
