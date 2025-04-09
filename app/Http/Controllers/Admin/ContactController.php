<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        
        $query = Contact::query();
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        $contacts = $query->latest()->paginate(10);
        
        return view('admin.contacts.index', compact('contacts', 'status'));
    }
    
    public function show(Contact $contact)
    {
        // Đánh dấu là đã đọc nếu chưa đọc
        if (!$contact->status) {
            $contact->update(['status' => true]);
        }
        
        return view('admin.contacts.show', compact('contact'));
    }
    
    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);
        
        $contact->update([
            'reply' => $request->reply,
            'replied_at' => now(),
        ]);
        
        // Ở đây bạn có thể thêm logic để gửi email phản hồi đến người liên hệ
        
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Phản hồi đã được gửi thành công.');
    }
    
    public function markAsRead(Contact $contact)
    {
        $contact->update(['status' => true]);
        
        return redirect()->back()
            ->with('success', 'Tin nhắn đã được đánh dấu là đã đọc.');
    }
    
    public function markAsUnread(Contact $contact)
    {
        $contact->update(['status' => false]);
        
        return redirect()->back()
            ->with('success', 'Tin nhắn đã được đánh dấu là chưa đọc.');
    }
    
    public function destroy(Contact $contact)
    {
        $contact->delete();
        
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Tin nhắn liên hệ đã được xóa thành công.');
    }
    
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,mark_unread,delete',
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);
        
        $contactIds = $request->contact_ids;
        $action = $request->action;
        
        switch ($action) {
            case 'mark_read':
                Contact::whereIn('id', $contactIds)->update(['status' => true]);
                $message = 'Các tin nhắn đã được đánh dấu là đã đọc.';
                break;
            case 'mark_unread':
                Contact::whereIn('id', $contactIds)->update(['status' => false]);
                $message = 'Các tin nhắn đã được đánh dấu là chưa đọc.';
                break;
            case 'delete':
                Contact::whereIn('id', $contactIds)->delete();
                $message = 'Các tin nhắn đã được xóa thành công.';
                break;
        }
        
        return redirect()->route('admin.contacts.index')
            ->with('success', $message);
    }
} 