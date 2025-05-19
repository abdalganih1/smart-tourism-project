<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'author_user_id',
        'title',
        'content',
        'excerpt',
        'main_image_url',
        'video_url',
        'tags',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime', // Or date if only date
    ];


    public function author()
    {
        // Article belongs to a User (author). Articles table has author_user_id FK.
        return $this->belongsTo(User::class, 'author_user_id');
    }

    // Polymorphic relationships where this Article is the target
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