<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Review;

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
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('images/avatars'), $avatarName);
            $user->avatar = 'images/avatars/' . $avatarName;
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
        
        $review->delete();
        
        return redirect()->route('profile.reviews')->with('success', 'Đánh giá đã được xóa thành công!');
    }
    
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
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
}
