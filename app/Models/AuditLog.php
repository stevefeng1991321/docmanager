<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'action', 'resource_id', 'details', 'ip_address', 'created_at'];

    protected function casts(): array
    {
        return ['details' => 'array', 'created_at' => 'datetime'];
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function resource() { return $this->belongsTo(Resource::class); }

    public static function record(string $action, ?int $resourceId = null, array $details = []): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'resource_id' => $resourceId,
            'details'     => $details,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
        ]);
    }
}
