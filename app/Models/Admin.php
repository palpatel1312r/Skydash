<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'phone',
        'address',
        'role_id',   // ✅ Changed from 'role' to 'role_id'
        'status',
    ];

    // Add the relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
