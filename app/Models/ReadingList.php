<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingList extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_private'];

    protected function casts(): array
    {
        return ['is_private' => 'boolean'];
    }

    public function user()      { return $this->belongsTo(User::class); }
    public function resources() { return $this->belongsToMany(Resource::class, 'reading_list_items')->withPivot('sort_order', 'added_at')->orderByPivot('sort_order'); }
}
