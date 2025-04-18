<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointmentServices()
    {
        return $this->hasMany(AppointmentService::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_services')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Mối quan hệ với Invoice (nhiều-nhiều)
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_service')
                    ->withPivot('quantity', 'price', 'discount', 'subtotal')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
