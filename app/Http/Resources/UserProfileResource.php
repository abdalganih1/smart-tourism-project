<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'resource' property is the underlying UserProfile model instance
        return [
            // Note: 'user_id' is the FK, but you might include it if useful
            // 'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'passport_image_url' => $this->passport_image_url,
            'bio' => $this->bio,
            'profile_picture_url' => $this->profile_picture_url,
            'updated_at' => $this->updated_at, // Profile has its own updated_at
        ];
    }
}