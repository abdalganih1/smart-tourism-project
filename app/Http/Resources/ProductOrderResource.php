<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For user
// use App\Http\Resources\ProductOrderItemResource; // For items


class ProductOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying ProductOrder model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, // Include FK or the nested resource
            'order_date' => $this->order_date, // Will be cast/formatted by Laravel's default casts or Accessors
            'total_amount' => number_format($this->total_amount, 2), // Format currency
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'payment_transaction_id' => $this->payment_transaction_id,
            'shipping_address_line1' => $this->shipping_address_line1,
            'shipping_address_line2' => $this->shipping_address_line2,
            'shipping_city' => $this->shipping_city,
            'shipping_postal_code' => $this->shipping_postal_code,
            'shipping_country' => $this->shipping_country,
            'created_at' => $this->created_at, // These might be redundant if order_date is used
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'user' => new UserResource($this->whenLoaded('user')), // Include user who placed the order
            'items' => ProductOrderItemResource::collection($this->whenLoaded('items')), // Include order items
        ];
    }
}