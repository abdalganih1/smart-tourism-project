<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\TouristSiteResource; // For site
// use App\Http\Resources\UserResource; // For organizer


class TouristActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying TouristActivity model instance
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'site_id' => $this->site_id, // Include FK or the nested resource
            'location_text' => $this->location_text,
            'start_datetime' => $this->start_datetime, // Will be cast/formatted
            'duration_minutes' => $this->duration_minutes,
            'organizer_user_id' => $this->organizer_user_id, // Include FK or the nested resource
            'price' => number_format($this->price, 2), // Format currency
            'max_participants' => $this->max_participants,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'site' => new TouristSiteResource($this->whenLoaded('site')), // Include site details
            'organizer' => new UserResource($this->whenLoaded('organizer')), // Include organizer user
        ];
    }
}