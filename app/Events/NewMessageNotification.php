<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class NewMessageNotification implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Message $message,
        public int $recipientId,
    ) {
        $this->message->loadMissing('sender');
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->recipientId)];
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id'      => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_name'     => $this->message->sender->name,
            'body'            => match($this->message->type) {
                'image' => '📷 Image',
                'file'  => '📎 ' . ($this->message->metadata['filename'] ?? 'File'),
                default => Str::limit($this->message->body ?? '', 80),
            },
        ];
    }
}
