<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'resource_id' => $this->resource_id,
            'page_number' => $this->page_number,
            'label'       => $this->label,
            'resource'    => new DocumentResource($this->whenLoaded('resource')),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
