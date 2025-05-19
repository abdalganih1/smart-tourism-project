<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    // Primary key is 'id' by default, no need to specify $primaryKey
    // Auto-incrementing is true by default
    // Key type is int (unsignedBigInteger) by default

    // Assuming migration now includes $table->id() and $table->foreignId('user_id')...
    // So, 'id' is the PK for UserProfile, and 'user_id' is a regular FK.

    protected $fillable = [
        'user_id', // Now user_id is fillable as it's an FK, not the PK
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'passport_image_url',
        'bio',
        'profile_picture_url',
    ];

    // Define relationship back to User (Many-to-One)
    public function user()
    {
        // UserProfile belongs to a User. UserProfile table has user_id FK.
        return $this->belongsTo(User::class); // Laravel infers FK name 'user_id'
    }
}