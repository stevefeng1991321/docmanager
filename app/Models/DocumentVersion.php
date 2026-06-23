<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'resource_id', 'version_number', 'file_path',
        'stored_filename', 'file_size', 'file_hash', 'change_note', 'uploaded_by', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function resource()  { return $this->belongsTo(Resource::class); }
    public function uploader()  { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function accessLogs(){ return $this->hasMany(DocumentAccessLog::class, 'version_id'); }
}
