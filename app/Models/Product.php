<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // ✅ Specify the correct table name
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
}
