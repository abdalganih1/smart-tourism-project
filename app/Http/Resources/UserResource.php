<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'resource' property is the underlying User model instance
        return [
            'id' => $this->id, // Include the user's primary key (id)
            'username' => $this->username,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Include the loaded profile relationship if it exists
            'profile' => new UserProfileResource($this->whenLoaded('profile')),
            // 'whenLoaded' ensures profile is only included if the relationship
            // was loaded via ->load('profile') or eager loading ->with('profile')
        ];
    }
}