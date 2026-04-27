<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'email',
        'bill',
        'phone',
        'address',
        'customer_status',
        'status'
    ];

    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with products through order items
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
}
