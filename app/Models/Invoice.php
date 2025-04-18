<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'appointment_id',
        'user_id',
        'barber_id',
        'invoice_number',
        'subtotal',
        'discount',
        'tax',
        'total',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Mối quan hệ với Appointment
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Mối quan hệ với User (khách hàng)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với Barber
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Mối quan hệ với Service (nhiều-nhiều)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'invoice_service')
                    ->withPivot('quantity', 'price', 'discount', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Accessor để lấy các items của hóa đơn (dịch vụ và sản phẩm)
     * Được sử dụng trong view edit.blade.php
     */
    public function getItemsAttribute()
    {
        // Lấy dịch vụ từ quan hệ services và chuyển đổi thành collection các item
        $services = $this->services;

        // Nếu không có dịch vụ nào, trả về một collection rỗng
        if ($services->isEmpty()) {
            return collect([]);
        }

        // Chuyển đổi dịch vụ thành các item
        $items = $services->map(function ($service) {
            $item = new \stdClass();
            $item->id = $service->pivot->id;
            $item->type = 'service';
            $item->item_id = $service->id;
            $item->price = $service->pivot->price;
            $item->quantity = $service->pivot->quantity;
            $item->discount = $service->pivot->discount;
            $item->subtotal = $service->pivot->subtotal;
            return $item;
        });

        // Hiện tại chưa có mối quan hệ với sản phẩm, nhưng nếu sau này có thêm,
        // có thể mở rộng đoạn code này để thêm các sản phẩm vào collection items

        return $items;
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Kiểm tra trạng thái hóa đơn
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPendingStatus()
    {
        return $this->status === 'pending';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }
}
