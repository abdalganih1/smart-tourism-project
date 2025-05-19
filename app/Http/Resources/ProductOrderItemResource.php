<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\ProductResource; // For product


class ProductOrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying ProductOrderItem model instance
        return [
            'id' => $this->id, // Optional, might not be needed
            'order_id' => $this->order_id, // Optional, implicit if nested in order
            'product_id' => $this->product_id, // Include FK or the nested resource
            'quantity' => $this->quantity,
            'price_at_purchase' => number_format($this->price_at_purchase, 2), // Format currency
            // 'created_at' => $this->created_at, // No timestamps in schema
            // 'updated_at' => $this->updated_at, // No timestamps in schema

            // Include relationships if they were loaded
            'product' => new ProductResource($this->whenLoaded('product')), // Include product details
        ];
    }
}