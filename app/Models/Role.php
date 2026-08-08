<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by'];

    // ✅ ADD THIS RELATIONSHIP
    public function creator()
    {
        // Assuming 'created_by' column stores the ID of an Admin user
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
