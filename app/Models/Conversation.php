<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ConversationRead::class);
    }

    public function involves(int $userId): bool
    {
        return $this->user_one_id === $userId || $this->user_two_id === $userId;
    }

    public function otherUser(int $currentUserId): User
    {
        return $this->user_one_id === $currentUserId ? $this->userTwo : $this->userOne;
    }

    public static function between(User $a, User $b): self
    {
        [$lowId, $highId] = $a->id < $b->id ? [$a->id, $b->id] : [$b->id, $a->id];

        return static::firstOrCreate([
            'user_one_id' => $lowId,
            'user_two_id' => $highId,
        ]);
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
        $other = $this->otherUser($forUserId);
        $lastMessage = $this->messages()->latest()->first();

        return [
            'id'              => $this->id,
            'other_user'      => [
                'id'       => $other->id,
                'name'     => $other->name,
                'username' => $other->username,
                'role'     => $other->role,
            ],
            'last_message'    => $lastMessage ? [
                'body'       => $lastMessage->body,
                'sender_id'  => $lastMessage->sender_id,
                'created_at' => $lastMessage->created_at->toIso8601String(),
            ] : null,
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'unread_count'    => $this->unreadCountFor($forUserId),
        ];
    }
}
