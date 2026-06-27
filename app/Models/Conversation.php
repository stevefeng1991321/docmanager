<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
        'type',
        'name',
        'created_by',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // ── helpers ──────────────────────────────────────────────────────────────

    // Treat null type as 'direct' so pre-migration rows still work
    public function isDirect(): bool { return $this->type === 'direct' || ($this->type === null && $this->user_one_id !== null); }
    public function isGroup(): bool  { return $this->type === 'group'; }

    public function involves(int $userId): bool
    {
        if ($this->isDirect()) {
            return $this->user_one_id === $userId || $this->user_two_id === $userId;
        }

        if ($this->relationLoaded('participants')) {
            return $this->participants->contains('id', $userId);
        }

        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function otherUser(int $currentUserId): User
    {
        return $this->user_one_id === $currentUserId ? $this->userTwo : $this->userOne;
    }

    public function lastReadAtFor(int $userId): ?\Illuminate\Support\Carbon
    {
        return $this->reads->firstWhere('user_id', $userId)?->last_read_at;
    }

    public function unreadCountFor(int $userId): int
    {
        $lastReadAt = $this->lastReadAtFor($userId);

        $query = $this->messages()->where('sender_id', '!=', $userId);

        if ($lastReadAt) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }

    public function toListItem(int $forUserId): array
    {
        $lastMessage = $this->messages()->with('sender')->latest()->first();

        $item = [
            'id'              => $this->id,
            'type'            => $this->isDirect() ? 'direct' : 'group',
            'last_message'    => $lastMessage ? [
                'body'        => $lastMessage->body,
                'sender_id'   => $lastMessage->sender_id,
                'sender_name' => $lastMessage->sender?->name ?? '[Deleted]',
                'created_at'  => $lastMessage->created_at->toIso8601String(),
            ] : null,
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'unread_count'    => $this->unreadCountFor($forUserId),
        ];

        if ($this->isDirect()) {
            $other        = $this->otherUser($forUserId);
            $item['name'] = $other->name;
            $item['other_user'] = [
                'id'       => $other->id,
                'name'     => $other->name,
                'username' => $other->username,
                'role'     => $other->role,
            ];
        } else {
            $item['name']         = $this->name ?? 'Group Chat';
            $item['member_count'] = $this->participants->count();
            $item['participants'] = $this->participants->map(fn ($u) => [
                'id'   => $u->id,
                'name' => $u->name,
                'role' => $u->pivot->role,
            ]);
        }

        return $item;
    }

    // ── factories ─────────────────────────────────────────────────────────────

    public static function between(User $a, User $b): self
    {
        [$lowId, $highId] = $a->id < $b->id ? [$a->id, $b->id] : [$b->id, $a->id];

        return static::firstOrCreate(
            ['user_one_id' => $lowId, 'user_two_id' => $highId],
            ['type' => 'direct']
        );
    }

    public static function createGroup(string $name, User $creator, array $memberIds): self
    {
        $group = static::create([
            'type'       => 'group',
            'name'       => $name,
            'created_by' => $creator->id,
        ]);

        $attach = [$creator->id => ['role' => 'admin', 'joined_at' => now()]];

        foreach ($memberIds as $id) {
            if ((int) $id !== $creator->id) {
                $attach[(int) $id] = ['role' => 'member', 'joined_at' => now()];
            }
        }

        $group->participants()->attach($attach);

        return $group;
    }

    // ── relationships ─────────────────────────────────────────────────────────

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ConversationRead::class);
    }
}
