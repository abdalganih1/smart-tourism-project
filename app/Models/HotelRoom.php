<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'price_per_night',
        'area_sqm',
        'max_occupancy',
        'description',
        'is_available_for_booking',
    ];

     protected $casts = [
        'price_per_night' => 'decimal:2',
        'area_sqm' => 'decimal:2',
        'max_occupancy' => 'integer',
        'is_available_for_booking' => 'boolean',
    ];

    public function hotel()
    {
        // HotelRoom belongs to a Hotel. HotelRooms table has hotel_id FK.
        return $this->belongsTo(Hotel::class);
    }

    public function type()
    {
        // HotelRoom belongs to a HotelRoomType. HotelRooms table has room_type_id FK.
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

    public function bookings()
    {
        // HotelRoom has many HotelBookings. HotelBookings table has room_id FK.
        return $this->hasMany(HotelBooking::class, 'room_id');
    }
}