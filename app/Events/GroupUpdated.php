<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class GroupUpdated implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public Conversation $conversation,
        public string $type,
        public array $payload = [],
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversation.' . $this->conversation->id)];
    }

    public function broadcastAs(): string { return 'group.updated'; }

    public function broadcastWith(): array
    {
        return array_merge(['type' => $this->type], $this->payload);
    }
}
