<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->loadMissing(['sender', 'replyTo.sender']);
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversation.' . $this->message->conversation_id)];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'                   => $this->message->id,
            'conversation_id'      => $this->message->conversation_id,
            'sender_id'            => $this->message->sender_id,
            'sender_name'          => $this->message->sender->name,
            'sender_initial'       => strtoupper(substr($this->message->sender->name, 0, 1)),
            'type'                 => $this->message->type,
            'body'                 => $this->message->body,
            'metadata'             => $this->message->metadata
                ? array_merge($this->message->metadata, ['url' => asset('storage/' . ($this->message->metadata['path'] ?? ''))])
                : null,
            'reply_to_id'          => $this->message->reply_to_id,
            'reply_to'             => $this->message->reply_to_id && $this->message->replyTo ? [
                'id'          => $this->message->replyTo->id,
                'type'        => $this->message->replyTo->type,
                'body'        => $this->message->replyTo->deleted_at ? null : $this->message->replyTo->body,
                'metadata'    => (!$this->message->replyTo->deleted_at && $this->message->replyTo->metadata)
                    ? array_merge($this->message->replyTo->metadata, ['url' => asset('storage/' . ($this->message->replyTo->metadata['path'] ?? ''))])
                    : null,
                'sender_name' => $this->message->replyTo->sender?->name ?? 'Unknown',
                'deleted'     => (bool) $this->message->replyTo->deleted_at,
            ] : null,
            'created_at'           => $this->message->created_at->toISOString(),
        ];
    }
}
