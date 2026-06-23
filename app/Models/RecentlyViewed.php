<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyViewed extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'resource_id', 'viewed_at'];

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function resource() { return $this->belongsTo(Resource::class); }
}
