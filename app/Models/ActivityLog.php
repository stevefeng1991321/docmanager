<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'event', 'ip_address', 'user_agent', 'details', 'created_at'];

    protected function casts(): array
    {
        return ['details' => 'array', 'created_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }

    public static function record(string $event, array $details = []): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'event'      => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details'    => $details,
            'created_at' => now(),
        ]);
    }
}
