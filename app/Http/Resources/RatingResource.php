<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For user
// Import all potential target resources (Optional, if you create specific resources for each)
// use App\Http\Resources\TouristSiteResource;
// use App\Http\Resources\ProductResource;
// use App\Http\Resources\ArticleResource;
// use App\Http\Resources\HotelResource;
// use App\Http\Resources\SiteExperienceResource;


class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying Rating model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, // Include FK or the nested resource
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'rating_value' => $this->rating_value,
            'review_title' => $this->review_title,
            'review_text' => $this->review_text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'user' => new UserResource($this->whenLoaded('user')), // Include user who wrote the rating

            // Include the loaded 'target' polymorphic relationship
            // Requires loading the relationship with ->with('target') in the controller.
            // Map to specific resources if you have them
            'target' => $this->whenLoaded('target', function () {
                 switch ($this->target_type) {
                     case 'TouristSite':
                         return new TouristSiteResource($this->target);
                     case 'Product':
                         return new ProductResource($this->target);
                     case 'Article':
                         return new ArticleResource($this->target);
                     case 'Hotel':
                         return new HotelResource($this->target);
                     case 'SiteExperience':
                         return new SiteExperienceResource($this->target);
                     default:
                         return JsonResource::make($this->target);
                 }
             }),
             // If you don't have specific resources for each target type yet:
             // 'target' => JsonResource::make($this->whenLoaded('target')), // Simple generic resource
        ];
    }
}