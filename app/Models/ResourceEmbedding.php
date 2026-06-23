<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceEmbedding extends Model
{
    protected $fillable = ['resource_id', 'chunk_index', 'chunk_text', 'embedding', 'model'];

    protected function casts(): array
    {
        return ['embedding' => 'array'];
    }

    public function resource() { return $this->belongsTo(Resource::class); }
}
