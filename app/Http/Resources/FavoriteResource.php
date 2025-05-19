<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import all potential target resources (Optional, if you create specific resources for each)
// use App\Http\Resources\TouristSiteResource;
// use App\Http\Resources\ProductResource;
// use App\Http\Resources\ArticleResource;
// use App\Http\Resources\HotelResource;
// use App\Http\Resources\SiteExperienceResource;


class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying Favorite model instance
        return [
            'id' => $this->id, // Optional, might not be needed for API client
            'user_id' => $this->user_id, // Optional, implicit if fetching user's favorites
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'added_at' => $this->added_at,

            // Include the loaded 'target' relationship
            // This requires loading the relationship with ->with('target') in the controller.
            // We can use conditional resources based on target_type if needed,
            // but a generic JsonResource for the target itself is simpler.
            // If you have specific resources for each target type (e.g., TouristSiteResource),
            // you would map them here:
            'target' => $this->whenLoaded('target', function () {
                // This is a common pattern for polymorphic resources
                switch ($this->target_type) {
                    case 'TouristSite':
                        return new TouristSiteResource($this->target); // You need TouristSiteResource
                    case 'Product':
                        return new ProductResource($this->target); // You need ProductResource
                    case 'Article':
                        return new ArticleResource($this->target); // You need ArticleResource
                    case 'Hotel':
                        return new HotelResource($this->target); // You need HotelResource
                    case 'SiteExperience':
                        return new SiteExperienceResource($this->target); // You need SiteExperienceResource
                    default:
                        return JsonResource::make($this->target); // Fallback generic resource
                }
            }),
             // If you don't have specific resources for each target type yet:
             // 'target' => JsonResource::make($this->whenLoaded('target')), // Simple generic resource

        ];
    }
}