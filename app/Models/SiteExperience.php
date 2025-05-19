<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteExperience extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'site_id',
        'title',
        'content',
        'photo_url',
        'visit_date',
    ];

     protected $casts = [
        'visit_date' => 'date',
    ];

    public function user()
    {
        // SiteExperience belongs to a User. SiteExperiences table has user_id FK.
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        // SiteExperience belongs to a TouristSite. SiteExperiences table has site_id FK.
        return $this->belongsTo(TouristSite::class, 'site_id');
    }

    // Polymorphic relationships where this SiteExperience is the target
    // Note: Specify morph name if model name doesn't match snake_case singular of column (site_experience)
    // Eloquent should handle 'site_experience' automatically if target_type stores 'site_experience'
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