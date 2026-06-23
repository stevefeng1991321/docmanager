<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingListItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['reading_list_id', 'resource_id', 'sort_order', 'added_at'];

    public function readingList() { return $this->belongsTo(ReadingList::class); }
    public function resource()   { return $this->belongsTo(Resource::class); }
}
