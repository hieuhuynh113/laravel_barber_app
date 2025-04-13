<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReply;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        // Gửi email phản hồi đến người liên hệ
        try {
            Mail::to($contact->email)->send(new ContactReply($contact));
            Log::info("Email phản hồi đã được gửi đến {$contact->email} cho liên hệ #{$contact->id}");
            return redirect()->route('admin.contacts.index')
                ->with('success', 'Phản hồi đã được gửi thành công và email đã được gửi đến người liên hệ.');
        } catch (\Exception $e) {
            Log::error("Không thể gửi email phản hồi: " . $e->getMessage());
            return redirect()->route('admin.contacts.index')
                ->with('warning', 'Phản hồi đã được lưu nhưng không thể gửi email. Vui lòng kiểm tra cấu hình email.');
        }
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