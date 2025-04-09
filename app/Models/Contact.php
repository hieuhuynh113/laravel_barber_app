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
    ];

    protected $casts = [
        'status' => 'boolean',
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
