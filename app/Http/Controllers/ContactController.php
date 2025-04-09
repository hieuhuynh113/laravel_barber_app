<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

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
        
        Contact::create($request->all());
        
        return redirect()->back()->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận được tin nhắn của bạn và sẽ liên hệ lại sớm.');
    }
}
