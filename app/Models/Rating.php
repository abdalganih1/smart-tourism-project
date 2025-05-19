<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'rating_value',
        'review_title',
        'review_text',
    ];

    protected $casts = [
        'rating_value' => 'integer',
    ];

    public function user()
    {
        // Rating belongs to a User. Ratings table has user_id FK.
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship to the rated item
    public function target()
    {
        return $this->morphTo();
    }
}