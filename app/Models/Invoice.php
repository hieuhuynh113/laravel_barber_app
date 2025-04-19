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

    /**
     * Accessor để lấy giá trị discount_amount
     */
    public function getDiscountAmountAttribute()
    {
        return $this->discount;
    }

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

    /**
     * Accessor để lấy giá trị tax_amount
     */
    public function getTaxAmountAttribute()
    {
        return $this->tax;
    }

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

        // Lấy dịch vụ từ quan hệ services và chuyển đổi thành collection các item
        $services = $this->services;

        // Chuyển đổi dịch vụ thành các item
        if (!$services->isEmpty()) {
            $serviceItems = $services->map(function ($service) {
                $item = new \stdClass();
                $item->id = $service->pivot->id;
                $item->type = 'service';
                $item->item_id = $service->id;
                $item->name = $service->name;
                $item->price = $service->pivot->price;
                $item->quantity = $service->pivot->quantity;
                $item->discount = $service->pivot->discount;
                $item->subtotal = $service->pivot->subtotal;
                return $item;
            });

            $items = $items->concat($serviceItems);
        }

        // Lấy sản phẩm từ quan hệ products và chuyển đổi thành collection các item
        $products = $this->products;

        // Chuyển đổi sản phẩm thành các item
        if (!$products->isEmpty()) {
            $productItems = $products->map(function ($product) {
                $item = new \stdClass();
                $item->id = $product->pivot->id;
                $item->type = 'product';
                $item->item_id = $product->id;
                $item->name = $product->name;
                $item->price = $product->pivot->price;
                $item->quantity = $product->pivot->quantity;
                $item->discount = $product->pivot->discount;
                $item->subtotal = $product->pivot->subtotal;
                return $item;
            });

            $items = $items->concat($productItems);
        }

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
