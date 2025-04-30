<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_day_off',
        'reason',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'is_day_off' => 'boolean',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Chủ nhật',
            1 => 'Thứ hai',
            2 => 'Thứ ba',
            3 => 'Thứ tư',
            4 => 'Thứ năm',
            5 => 'Thứ sáu',
            6 => 'Thứ bảy',
        ];

        return $days[$this->day_of_week] ?? '';
    }
}
