<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $fillable = [
        'order_index',
        'title',
        'description',
        'difficulty',
        'category',
        'solution_code',
        'function_name',
        'test_cases',
    ];

    protected $casts = [
        'test_cases' => 'array',
    ];
}
