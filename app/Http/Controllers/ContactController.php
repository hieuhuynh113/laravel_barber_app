<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewContactNotification;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($request->all());

        // Gửi thông báo cho admin về tin nhắn liên hệ mới
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewContactNotification($contact));
            \Log::info("Thông báo liên hệ mới đã được gửi đến admin từ {$contact->email}");
        } catch (\Exception $e) {
            \Log::error("Không thể gửi thông báo liên hệ mới: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn của bạn và sẽ liên hệ lại sớm.');
    }
}
