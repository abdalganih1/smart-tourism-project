<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristActivity extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'name',
        'description',
        'site_id',
        'location_text',
        'start_datetime',
        'duration_minutes',
        'organizer_user_id',
        'price',
        'max_participants',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
    ];

    public function site()
    {
        // TouristActivity belongs to a TouristSite. TouristActivities table has site_id FK.
        return $this->belongsTo(TouristSite::class, 'site_id');
    }

    public function organizer()
    {
        // TouristActivity belongs to a User (organizer). TouristActivities table has organizer_user_id FK.
        return $this->belongsTo(User::class, 'organizer_user_id');
    }
}