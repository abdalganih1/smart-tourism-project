<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPhoneNumber extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'phone_number',
        'is_primary',
        'description',
    ];

    public function user()
    {
        // UserPhoneNumber belongs to a User. UserPhoneNumbers table has user_id FK.
        return $this->belongsTo(User::class);
    }
}