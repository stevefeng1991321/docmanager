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

    // Notification type → UserPreference column. Types absent here are always sent.
    private static array $prefMap = [
        'doc_approved'      => 'notify_doc_approved',
        'doc_rejected'      => 'notify_doc_approved',
        'file_uploaded'     => 'notify_file_uploaded',
        'version_updated'   => 'notify_version_updated',
        'access_denied'     => 'notify_access_denied',
        'account_activated' => 'notify_account_activated',
    ];

    public static function send(int $userId, string $type, string $title, string $message = '', ?int $resourceId = null): void
    {
        $prefKey = self::$prefMap[$type] ?? null;

        if ($prefKey !== null) {
            $prefs = UserPreference::where('user_id', $userId)->first();
            if ($prefs && $prefs->$prefKey === false) {
                return;
            }
        }

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
