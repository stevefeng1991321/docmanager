<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['resource_id', 'version_id', 'user_id', 'action', 'ip_address'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function resource() { return $this->belongsTo(Resource::class); }
    public function version()  { return $this->belongsTo(DocumentVersion::class, 'version_id'); }
    public function user()     { return $this->belongsTo(User::class); }
}
