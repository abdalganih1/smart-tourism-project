<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    public $timestamps = false; // No timestamps in schema

    protected $fillable = [
        'name',
        'description',
    ];

    public function rooms()
    {
        // HotelRoomType has many HotelRooms. HotelRooms table has room_type_id FK.
        return $this->hasMany(HotelRoom::class, 'room_type_id');
    }
}