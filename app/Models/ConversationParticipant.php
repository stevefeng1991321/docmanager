<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    protected $fillable = [
        'conversation_id', 'user_id', 'role', 'last_read_at', 'notifications_muted', 'left_at',
    ];

    protected $casts = [
        'last_read_at'         => 'datetime',
        'left_at'              => 'datetime',
        'notifications_muted'  => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
