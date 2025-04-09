<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isBarber()
    {
        return $this->role === 'barber';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
