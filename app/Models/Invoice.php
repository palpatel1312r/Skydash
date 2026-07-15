<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  use HasFactory;

  protected $fillable = [
    'invoice_number',
    'invoice_date',
    'customer_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'customer_address',
    'products',
    'subtotal',
    'tax_rate',
    'tax_amount',
    'total_amount',
    'status',
  ];

  protected $casts = [
    'products' => 'array',
    'invoice_date' => 'date',
  ];

  /**
   * Get the customer that owns the invoice.
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class, 'customer_id');
  }
}
