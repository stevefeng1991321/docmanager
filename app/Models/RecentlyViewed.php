<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyViewed extends Model
{
    protected $table = 'recently_viewed';
    public $timestamps = false;
    protected $fillable = ['user_id', 'viewable_type', 'viewable_id', 'viewed_at'];

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }

    public function user()     { return $this->belongsTo(User::class); }
    public function viewable() { return $this->morphTo(); }

    public static function record(int $userId, string $type, int $itemId, int $cap = 10): void
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'viewable_type' => $type, 'viewable_id' => $itemId],
            ['viewed_at' => now()]
        );

        // Trim to cap: keep the most-recent $cap rows, delete anything older
        $keep = static::where('user_id', $userId)
            ->orderByDesc('viewed_at')
            ->limit($cap)
            ->pluck('id');

        if ($keep->isNotEmpty()) {
            static::where('user_id', $userId)
                ->whereNotIn('id', $keep)
                ->delete();
        }
    }
}
