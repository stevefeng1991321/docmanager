<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicKnowledgeTrend extends Model
{
    protected $fillable = ['title', 'content', 'summary', 'category_id', 'tags', 'status'];

    protected $casts = ['tags' => 'array'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
