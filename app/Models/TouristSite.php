<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristSite extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'name',
        'description',
        'location_text',
        'latitude',
        'longitude',
        'city',
        'country',
        'category_id',
        'main_image_url',
        'video_url',
        'added_by_user_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8', // Or 11 depending on migration column definition
    ];


    public function category()
    {
        // TouristSite belongs to a SiteCategory. TouristSites table has category_id FK.
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }

    public function addedBy()
    {
        // TouristSite belongs to a User (the one who added it). TouristSites table has added_by_user_id FK.
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function activities()
    {
        // TouristSite has many TouristActivities at this site. TouristActivities table has site_id FK.
        return $this->hasMany(TouristActivity::class);
    }

    public function experiences()
    {
        // Tell Laravel that the foreign key on the 'site_experiences' table is 'site_id'
        return $this->hasMany(SiteExperience::class, 'site_id', 'id');
    }

    // Polymorphic relationships where this TouristSite is the target
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