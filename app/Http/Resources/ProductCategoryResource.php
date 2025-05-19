<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\ProductCategoryResource; // For parent relationship


class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying ProductCategory model instance
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent_category_id' => $this->parent_category_id, // Include FK
            // 'created_at' => $this->created_at, // No timestamps in schema V2.1
            // 'updated_at' => $this->updated_at, // No timestamps in schema V2.1

            // Include relationships if they were loaded
            'parent' => new ProductCategoryResource($this->whenLoaded('parent')), // Include parent category (Recursive)
            // 'children' => ProductCategoryResource::collection($this->whenLoaded('children')), // Include children categories (Recursive)
            // 'products_count' => $this->whenLoaded('products', fn() => $this->products->count()), // Count of products in this category
        ];
    }
}