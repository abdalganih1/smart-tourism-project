<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'name',
        'star_rating',
        'description',
        'address_line1',
        'city',
        'country',
        'latitude',
        'longitude',
        'contact_phone',
        'contact_email',
        'main_image_url',
        'managed_by_user_id',
    ];

    protected $casts = [
        'star_rating' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8', // Or 11
    ];

    public function managedBy()
    {
        // Hotel belongs to a User (manager). Hotels table has managed_by_user_id FK.
        return $this->belongsTo(User::class, 'managed_by_user_id');
    }

    public function rooms()
    {
        // Hotel has many HotelRooms. HotelRooms table has hotel_id FK.
        return $this->hasMany(HotelRoom::class);
    }

    // Polymorphic relationships where this Hotel is the target
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'target');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'target');
    }

     public function favorites()
    {
        return $this->morphMany(Favorite::class, 'target');
    }
}