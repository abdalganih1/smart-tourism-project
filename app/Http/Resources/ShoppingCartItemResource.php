<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\ProductResource; // For product

class ShoppingCartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying ShoppingCartItem model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, // Optional, implicit if fetching user's cart
            'product_id' => $this->product_id, // Include FK or the nested resource
            'quantity' => $this->quantity,
            'added_at' => $this->added_at, // Will be cast/formatted

            // Include relationships if they were loaded
            'product' => new ProductResource($this->whenLoaded('product')), // Include product details
        ];
    }
}