<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'reply',
        'replied_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function markAsRead()
    {
        $this->update(['status' => 1]);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 0);
    }
}
