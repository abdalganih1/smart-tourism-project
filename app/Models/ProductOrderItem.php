<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderItem extends Model
{
    use HasFactory;

    // Primary key is 'id' by default
    public $timestamps = false; // No timestamps in schema

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
    ];

     protected $casts = [
        'price_at_purchase' => 'decimal:2',
        'quantity' => 'integer',
    ];


    public function order()
    {
        // ProductOrderItem belongs to a ProductOrder. ProductOrderItems table has order_id FK.
        return $this->belongsTo(ProductOrder::class);
    }

    public function product()
    {
        // ProductOrderItem belongs to a Product. ProductOrderItems table has product_id FK.
        return $this->belongsTo(Product::class);
    }
}