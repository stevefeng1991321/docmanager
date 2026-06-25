<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
