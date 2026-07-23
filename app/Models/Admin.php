<?php

namespace App\Models;

use App\Models\Profile;
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
        'phone',
        'address',
        'role_id',
        'status',
    ];

    // Add the relationship
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function profile()
    {
        return $this->morphOne(Profile::class, 'profileable');
    }
    // ✅ ADD THIS INSIDE YOUR Admin MODEL
    protected static function booted()
    {
        static::creating(function ($admin) {
            // If role_id is empty, automatically set it to 2 (Admin)
            if (empty($admin->role_id)) {
                $admin->role_id = 2;
            }
        });
    }
}
