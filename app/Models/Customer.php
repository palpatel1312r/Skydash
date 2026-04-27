<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'role',
        'status',
    ];
}
