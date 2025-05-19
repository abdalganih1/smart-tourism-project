<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying Comment model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'parent_comment_id' => $this->parent_comment_id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'user' => new UserResource($this->whenLoaded('user')), // Include user who wrote the comment
            'parent' => new CommentResource($this->whenLoaded('parent')), // Include parent comment if it's a reply (Recursive)
            'replies' => CommentResource::collection($this->whenLoaded('replies')), // Include replies (Recursive)
        ];
    }
}