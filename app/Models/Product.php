<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'product';
    protected $fillable = [
        'title',
        'image',
        'description',
        'price',
        'quantity',
        'category',
        'type'
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    use HasFactory;
}
