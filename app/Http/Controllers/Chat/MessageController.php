<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->isGroup()) {
            $conversation->load('participants');
        }

        abort_unless($conversation->involves($user->id), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body'      => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        $recipientIds = $conversation->isDirect()
            ? [$conversation->otherUser($user->id)->id]
            : $conversation->participants->where('id', '!=', $user->id)->pluck('id')->all();

        try {
            event(new MessageSent($message->load('sender'), $recipientIds));
        } catch (Throwable $e) {
            Log::warning('Chat broadcast failed for MessageSent: ' . $e->getMessage());
        }

        return response()->json([
            'id'          => $message->id,
            'sender_id'   => $message->sender_id,
            'sender_name' => $user->name,
            'body'        => $message->body,
            'is_mine'     => true,
            'status'      => 'sent',
            'created_at'  => $message->created_at->toIso8601String(),
        ]);
    }
}
