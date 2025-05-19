<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying SiteCategory model instance
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            // 'created_at' => $this->created_at, // No timestamps in schema V2.1
            // 'updated_at' => $this->updated_at, // No timestamps in schema V2.1

            // Optional: Include count of associated tourist sites
            // 'tourist_sites_count' => $this->whenCounted('touristSites'), // if using ->withCount('touristSites')
        ];
    }
}