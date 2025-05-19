<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For user
// use App\Http\Resources\TouristSiteResource; // For site

class SiteExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying SiteExperience model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, // Include FK or the nested resource
            'site_id' => $this->site_id, // Include FK or the nested resource
            'title' => $this->title,
            'content' => $this->content,
            'visit_date' => $this->visit_date ? $this->visit_date->format('Y-m-d') : null, // Format date
            'photo_url' => $this->photo_url ? asset($this->photo_url) : null, // Use asset()
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'user' => new UserResource($this->whenLoaded('user')), // Include user who wrote the experience
            'site' => new TouristSiteResource($this->whenLoaded('site')), // Include site details

            // Optional: Include counts or summaries of polymorphic relations (Comments, Ratings, Favorites targeting this experience)
            // 'comments_count' => $this->whenLoaded('comments', fn() => $this->comments->count()),
            // 'ratings_count' => $this->whenLoaded('ratings', fn() => $this->ratings->count()),
            // 'average_rating' => $this->whenLoaded('ratings', fn() => round($this->ratings->avg('rating_value') ?? 0, 1)),
            // 'is_favorited_by_user' => $this->whenNotNull(Auth::id(), fn() => Auth::user()->favorites()->where(...)->exists()), // Contextual check if authenticated
        ];
    }
}