<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    public $timestamps = false;
    protected $fillable = ['resource_id', 'created_by', 'token', 'expires_at', 'revoked_at'];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'revoked_at'  => 'datetime',
            'created_at'  => 'datetime',
        ];
    }

    public function isValid(): bool
    {
        return $this->revoked_at === null && $this->expires_at !== null && $this->expires_at->isFuture();
    }

    public function resource() { return $this->belongsTo(Resource::class); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
}
