<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  use HasFactory;

  protected $fillable = [
    'profileable_type',
    'profileable_id',
    'phone',
    'address',
    'profile_image',
  ];

  // ✅ The inverse relationship
  public function profileable()
  {
    return $this->morphTo();
  }
}
