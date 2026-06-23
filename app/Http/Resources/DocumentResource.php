<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'original_filename'=> $this->original_filename,
            'file_type'        => $this->file_type,
            'file_size'        => $this->file_size,
            'file_size_human'  => $this->humanFileSize(),
            'status'           => $this->status,
            'download_count'   => $this->download_count,
            'current_version'  => $this->current_version,
            'average_rating'   => $this->averageRating(),
            'category'         => new CategoryResource($this->whenLoaded('category')),
            'tags'             => TagResource::collection($this->whenLoaded('tags')),
            'uploaded_by'      => $this->whenLoaded('uploader', fn() => [
                'id'   => $this->uploader->id,
                'name' => $this->uploader->name,
            ]),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            'download_url'     => route('api.resources.download', $this->id),
        ];
    }

    private function humanFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1_048_576) return number_format($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)     return number_format($bytes / 1_024, 1) . ' KB';
        return $bytes . ' B';
    }
}
