<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    /** @param int[] $recipientIds */
    public function __construct(public Message $message, public array $recipientIds)
    {
    }

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('conversation.' . $this->message->conversation_id)];

        foreach ($this->recipientIds as $id) {
            $channels[] = new PrivateChannel('App.Models.User.' . $id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender_name'     => $this->message->sender->name,
            'body'            => $this->message->body,
            'created_at'      => $this->message->created_at->toIso8601String(),
            'delivered_at'    => $this->message->delivered_at?->toIso8601String(),
        ];
    }
}
