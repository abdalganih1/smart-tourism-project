<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'num_adults',
        'num_children',
        'total_amount',
        'booking_status',
        'payment_status',
        'payment_transaction_id',
        'special_requests',
        // 'booked_at' is often handled by timestamps or default in DB
    ];

     protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'num_adults' => 'integer',
        'num_children' => 'integer',
        'total_amount' => 'decimal:2',
        'booked_at' => 'datetime',
    ];

    public function user()
    {
        // HotelBooking belongs to a User. HotelBookings table has user_id FK.
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        // HotelBooking belongs to a HotelRoom. HotelBookings table has room_id FK.
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }
}