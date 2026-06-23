<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRating extends Model
{
    protected $fillable = ['user_id', 'resource_id', 'rating', 'review'];

    public function user()     { return $this->belongsTo(User::class); }
    public function resource() { return $this->belongsTo(Resource::class); }
}
