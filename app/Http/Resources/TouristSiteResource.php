<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\SiteCategoryResource; // For category
// use App\Http\Resources\UserResource; // For addedBy
// use App\Http\Resources\CommentResource; // For comments (if embedded)
// use App\Http\Resources\RatingResource; // For ratings (if embedded)
// use App\Http\Resources\SiteExperienceResource; // For experiences (if embedded)


class TouristSiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying TouristSite model instance
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location_text' => $this->location_text,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'country' => $this->country,
            'category_id' => $this->category_id, // Include FK or the nested resource
            'main_image_url' => $this->main_image_url ? asset($this->main_image_url) : null, // Use asset()
            'video_url' => $this->video_url,
            'added_by_user_id' => $this->added_by_user_id, // Include FK or the nested resource
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'category' => new SiteCategoryResource($this->whenLoaded('category')), // Include category
            'added_by' => new UserResource($this->whenLoaded('addedBy')), // Include user who added site

            // Optional: Include summaries/lists of polymorphic relations if loaded
            // 'comments' => CommentResource::collection($this->whenLoaded('comments')),
            // 'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
             // 'experiences' => SiteExperienceResource::collection($this->whenLoaded('experiences')),

            // Optional: Include counts or summaries directly if fetched or added to model
            // 'comments_count' => $this->whenNotNull($this->comments_count),
            // 'ratings_count' => $this->whenNotNull($this->ratings_count),
            // 'average_rating' => $this->whenNotNull($this->average_rating),
            // 'experiences_count' => $this->whenNotNull($this->experiences_count),
            // 'is_favorited_by_user' => $this->whenNotNull($this->is_favorited_by_user), // if added as accessor
        ];
    }
}