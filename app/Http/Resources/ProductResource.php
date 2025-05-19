<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For seller
// use App\Http\Resources\ProductCategoryResource; // For category
// use App\Http\Resources\CommentResource; // For comments (if embedded)
// use App\Http\Resources\RatingResource; // For ratings (if embedded)

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying Product model instance
        return [
            'id' => $this->id,
            'seller_user_id' => $this->seller_user_id, // Include FK or the nested resource
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'stock_quantity' => $this->stock_quantity,
            'price' => number_format($this->price, 2), // Format currency
            'main_image_url' => $this->main_image_url ? asset($this->main_image_url) : null, // Use asset()
            'category_id' => $this->category_id, // Include FK or the nested resource
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'seller' => new UserResource($this->whenLoaded('seller')), // Include seller user
            'category' => new ProductCategoryResource($this->whenLoaded('category')), // Include category

            // Optional: Include summaries/lists of polymorphic relations if loaded
            // 'comments' => CommentResource::collection($this->whenLoaded('comments')),
            // 'ratings' => RatingResource::collection($this->whenLoaded('ratings')),

            // Optional: Include counts or summaries directly if fetched or added to model
            // 'comments_count' => $this->whenNotNull($this->comments_count), // if using ->withCount('comments')
            // 'ratings_count' => $this->whenNotNull($this->ratings_count), // if using ->withCount('ratings')
            // 'average_rating' => $this->whenNotNull($this->average_rating), // if added as accessor or fetched
            // 'is_favorited_by_user' => $this->whenNotNull($this->is_favorited_by_user), // if added as accessor
        ];
    }
}