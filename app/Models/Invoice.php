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
        'email_sent',
        'email_sent_at',
    ];

    /**
     * Accessor để lấy giá trị tax_rate
     */
    public function getTaxRateAttribute()
    {
        // Nếu subtotal = 0, trả về 0 để tránh chia cho 0
        if ($this->subtotal == 0) return 0;

        // Tính toán tax_rate dựa trên tax và subtotal
        return ($this->tax / $this->subtotal) * 100;
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
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
     * Mối quan hệ với Product (nhiều-nhiều)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product')
                    ->withPivot('quantity', 'price', 'discount', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Accessor để lấy các items của hóa đơn (dịch vụ và sản phẩm)
     * Được sử dụng trong view edit.blade.php
     */
    public function getItemsAttribute()
    {
        $items = collect([]);

        // Hàm chuyển đổi model thành item
        $mapToItem = function ($model, $type) {
            return (object) [
                'id' => $model->pivot->id,
                'type' => $type,
                'item_id' => $model->id,
                'name' => $model->name,
                'price' => $model->pivot->price,
                'quantity' => $model->pivot->quantity,
                'discount' => $model->pivot->discount,
                'subtotal' => $model->pivot->subtotal,
            ];
        };

        // Thêm các dịch vụ
        $serviceItems = $this->services->map(function ($service) use ($mapToItem) {
            return $mapToItem($service, 'service');
        });
        $items = $items->concat($serviceItems);

        // Thêm các sản phẩm
        $productItems = $this->products->map(function ($product) use ($mapToItem) {
            return $mapToItem($product, 'product');
        });
        $items = $items->concat($productItems);

        return $items;
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function isPaymentPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Kiểm tra trạng thái hóa đơn
     */
    public function isStatusCompleted()
    {
        return $this->status === 'completed';
    }

    public function isStatusPending()
    {
        return $this->status === 'pending';
    }

    public function isStatusCanceled()
    {
        return $this->status === 'canceled';
    }

    /**
     * Các phương thức tương thích ngược (giữ lại để tránh lỗi với code hiện tại)
     */
    public function isPaid()
    {
        return $this->isPaymentPaid();
    }

    public function isPending()
    {
        return $this->isPaymentPending();
    }

    public function isCompleted()
    {
        return $this->isStatusCompleted();
    }

    public function isPendingStatus()
    {
        return $this->isStatusPending();
    }

    public function isCanceled()
    {
        return $this->isStatusCanceled();
    }
}
