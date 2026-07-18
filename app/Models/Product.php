<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'title',
        'image',
        'description',
        'price',
        'quantity',
        'category',
        'type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function hasStock($requestedQuantity)
    {
        return $this->quantity >= $requestedQuantity;
    }

    public function decreaseStock($quantity)
    {
        if ($this->quantity < $quantity) {
            throw new \Exception('Not enough stock!');
        }
        $this->quantity -= $quantity;
        $this->save();
    }
    // Add this to your Product model
    public function increaseStock($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }
}
