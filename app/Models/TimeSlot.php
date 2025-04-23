<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'date',
        'time_slot',
        'booked_count',
        'max_bookings',
    ];

    protected $casts = [
        'date' => 'date',
        'booked_count' => 'integer',
        'max_bookings' => 'integer',
    ];

    /**
     * Kiểm tra xem mốc thời gian này còn chỗ trống không
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->booked_count < $this->max_bookings;
    }

    /**
     * Kiểm tra xem mốc thời gian này còn bao nhiêu chỗ trống
     *
     * @return int
     */
    public function availableSpots()
    {
        return max(0, $this->max_bookings - $this->booked_count);
    }

    /**
     * Tăng số lượng đặt chỗ
     *
     * @return bool
     */
    public function incrementBookedCount()
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $this->booked_count += 1;
        return $this->save();
    }

    /**
     * Giảm số lượng đặt chỗ
     *
     * @return bool
     */
    public function decrementBookedCount()
    {
        if ($this->booked_count > 0) {
            $this->booked_count -= 1;
            return $this->save();
        }

        return false;
    }

    /**
     * Mối quan hệ với Barber
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Mối quan hệ với Appointment
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'time_slot', 'time_slot')
            ->where('barber_id', $this->barber_id)
            ->where('appointment_date', $this->date);
    }
}
