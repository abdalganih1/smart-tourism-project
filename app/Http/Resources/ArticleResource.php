<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'resource' property is the underlying Article model instance
        return [
            'id' => $this->id,
            'author_user_id' => $this->author_user_id,
            'title' => $this->title,
            'content' => $this->content, // Include full content in show, maybe summary in index
            'excerpt' => $this->excerpt, // Always include excerpt if available
            'main_image_url' => $this->main_image_url ? asset($this->main_image_url) : null, // Use asset() to get full URL
            'video_url' => $this->video_url,
            'tags' => $this->tags ? explode(',', $this->tags) : [], // Split tags into an array
            'status' => $this->status,
            'published_at' => $this->published_at, // Returns Carbon instance, will be cast to string/format in JSON
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'author' => new UserResource($this->whenLoaded('author')),

            // Optional: Include counts or summaries of polymorphic relations
            // You might add 'comments_count', 'ratings_count', 'average_rating' directly to the model
            // or fetch them here if relations are loaded.
            // 'comments_count' => $this->whenLoaded('comments', fn() => $this->comments->count()),
            // 'ratings_count' => $this->whenLoaded('ratings', fn() => $this->ratings->count()),
            // 'average_rating' => $this->whenLoaded('ratings', fn() => round($this->ratings->avg('rating_value') ?? 0, 1)),
            // 'is_favorited_by_user' => $this->whenNotNull(Auth::id(), fn() => Auth::user()->favorites()->where(...)->exists()), // Contextual check if authenticated
        ];
    }

     /**
      * Customize the pagination response.
      * (Optional - only needed if you want custom pagination format beyond default Laravel)
      */
     // public function paginationInformation($request, $paginated)
     // {
     //     return parent::paginationInformation($request, $paginated);
     // }
}