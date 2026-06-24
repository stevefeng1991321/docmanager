<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'resource_id' => $this->resource_id,
            'rating'      => $this->rating,
            'review'      => $this->review,
            'user'        => new UserResource($this->whenLoaded('user')),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
