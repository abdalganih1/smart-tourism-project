<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'seller_user_id',
        'name',
        'description',
        'color',
        'stock_quantity',
        'price',
        'main_image_url',
        'category_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function seller()
    {
        // Product belongs to a User (seller). Products table has seller_user_id FK.
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function category()
    {
        // Product belongs to a ProductCategory. Products table has category_id FK.
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Polymorphic relationships where this Product is the target (One-to-Many polymorphic)
    // e.g., Product has many Ratings (where Rating's target_type is 'product' and target_id is this product's id)
    public function ratings()
    {
        // 'target' is the morph name defined in the Rating model's morphTo relation
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

    // Product can appear in many cart items and order items (One-to-Many)
    public function shoppingCartItems()
    {
        return $this->hasMany(ShoppingCartItem::class);
    }

     public function orderItems()
    {
        return $this->hasMany(ProductOrderItem::class);
    }
}