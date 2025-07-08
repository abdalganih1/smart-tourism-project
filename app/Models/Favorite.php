<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // ** أضف هذا السطر ** - هذا يخبر Laravel ألا يبحث عن created_at/updated_at

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        // Note: added_at should be handled by the database with a default value
        // If it's not, you must add 'added_at' to fillable and pass it in create()
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
     protected $casts = [
        'added_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->morphTo();
    }
}