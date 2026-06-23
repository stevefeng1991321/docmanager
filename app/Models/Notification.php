<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'type', 'title', 'message', 'resource_id', 'is_read', 'created_at'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean', 'created_at' => 'datetime'];
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function resource() { return $this->belongsTo(Resource::class); }

    public static function send(int $userId, string $type, string $title, string $message = '', ?int $resourceId = null): void
    {
        static::create([
            'user_id'     => $userId,
            'type'        => $type,
            'title'       => $title,
            'message'     => $message,
            'resource_id' => $resourceId,
            'created_at'  => now(),
        ]);
    }
}
