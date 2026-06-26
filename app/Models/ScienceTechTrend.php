<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrendMedia;

class ScienceTechTrend extends Model
{
    protected $table = 'science_tech_trends';

    protected $fillable = [
        'title',
        'content',
        'summary',
        'status',
        'year',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function media()
    {
        return $this->morphMany(TrendMedia::class, 'mediable')->orderBy('sort_order')->orderBy('id');
    }
}
