<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barber_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'booking_code',
        'customer_name',
        'email',
        'phone',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_services')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function appointmentServices()
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->appointmentServices->sum('price');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
