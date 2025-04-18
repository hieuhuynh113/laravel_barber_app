<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_id',
        'file_path',
        'notes',
        'status',
        'admin_notes',
    ];

    /**
     * Get the appointment that owns the receipt.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
