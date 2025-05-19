<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    // Assuming migration uses $table->id() for PK

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        // 'added_at' is usually handled by timestamps or default in DB
    ];

    protected $casts = [
        'added_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function user()
    {
        // ShoppingCartItem belongs to a User. ShoppingCartItems table has user_id FK.
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        // ShoppingCartItem belongs to a Product. ShoppingCartItems table has product_id FK.
        return $this->belongsTo(Product::class);
    }
}