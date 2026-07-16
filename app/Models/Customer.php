<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'customer';

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'role_id',   // ✅ ADD THIS
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ ADD THIS RELATIONSHIP
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
