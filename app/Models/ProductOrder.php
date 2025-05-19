<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;

    // Primary key is 'id' by default

    protected $fillable = [
        'user_id',
        'total_amount',
        'order_status',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'payment_transaction_id',
        // 'order_date' is often handled by timestamps or default in DB
    ];

    protected $casts = [
        'order_date' => 'datetime', // Or date if only date
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        // ProductOrder belongs to a User. ProductOrders table has user_id FK.
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        // ProductOrder has many ProductOrderItems. ProductOrderItems table has order_id FK.
        return $this->hasMany(ProductOrderItem::class);
    }
}