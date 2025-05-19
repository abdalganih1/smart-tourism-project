<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    // If you used a composite PK in the migration, uncomment and adjust below:
    // protected $primaryKey = ['user_id', 'target_type', 'target_id']; // Example for composite
    // public $incrementing = false; // Composite PK is not auto-incrementing

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        // 'added_at' is usually handled by default in DB
    ];

     protected $casts = [
        'added_at' => 'datetime',
    ];


    public function user()
    {
        // Favorite belongs to a User. Favorites table has user_id FK.
        return $this->belongsTo(User::class);
    }

    // Polymorphic relationship to the favorited item (Site, Product, Article, Hotel)
    // Laravel expects target_type and target_id columns by default
    public function target()
    {
        return $this->morphTo(); // Searches for target_type and target_id columns
    }
}