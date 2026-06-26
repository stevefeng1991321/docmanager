<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrendMedia;

class BasicKnowledgeTrend extends Model
{
    protected $fillable = ['title', 'content', 'summary', 'category_id', 'tags', 'status'];

    protected $casts = ['tags' => 'array'];

    public function media()
    {
        return $this->morphMany(TrendMedia::class, 'mediable')->orderBy('sort_order')->orderBy('id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
