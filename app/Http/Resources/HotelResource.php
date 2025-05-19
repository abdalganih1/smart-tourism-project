<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For managedBy
// use App\Http\Resources\HotelRoomResource; // For rooms


class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying Hotel model instance
        return [
            'id' => $this->id,
            'name' => $this->name,
            'star_rating' => $this->star_rating,
            'description' => $this->description,
            'address_line1' => $this->address_line1,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'main_image_url' => $this->main_image_url ? asset($this->main_image_url) : null, // Use asset()
            'managed_by_user_id' => $this->managed_by_user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'managed_by' => new UserResource($this->whenLoaded('managedBy')), // Include manager user
            'rooms' => \App\Http\Resources\HotelRoomResource::collection($this->whenLoaded('rooms')), // Include rooms collection

            // Optional: Include counts or summaries of polymorphic relations
            // 'comments_count' => $this->whenLoaded('comments', fn() => $this->comments->count()),
            // 'ratings_count' => $this->whenLoaded('ratings', fn() => $this->ratings->count()),
            // 'average_rating' => $this->whenLoaded('ratings', fn() => round($this->ratings->avg('rating_value') ?? 0, 1)),
            // 'is_favorited_by_user' => $this->whenNotNull(Auth::id(), fn() => Auth::user()->favorites()->where(...)->exists()), // Contextual check if authenticated
        ];
    }
}