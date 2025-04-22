<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function scopeService($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeProduct($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
