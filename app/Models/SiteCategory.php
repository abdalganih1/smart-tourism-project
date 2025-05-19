<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteCategory extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    public $timestamps = false; // No timestamps in schema

    protected $fillable = [
        'name',
        'description',
    ];

    public function touristSites()
    {
        // SiteCategory has many TouristSites. TouristSites table has category_id FK.
        return $this->hasMany(TouristSite::class, 'category_id');
    }
}