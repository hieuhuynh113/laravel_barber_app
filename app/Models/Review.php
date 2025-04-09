<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'barber_id',
        'service_id',
        'rating',
        'comment',
        'status',
        'images'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
