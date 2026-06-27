<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->withTrashed()
            ->cursorPaginate(50);

        $data = $messages->map(fn($msg) => [
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'sender_name' => $msg->sender->name,
            'sender_initial' => strtoupper(substr($msg->sender->name, 0, 1)),
            'body'        => $msg->deleted_at ? null : $msg->body,
            'deleted'     => (bool) $msg->deleted_at,
            'type'        => $msg->type,
            'reply_to_id' => $msg->reply_to_id,
            'created_at'  => $msg->created_at->toISOString(),
        ]);

        // Include each participant's last_read_at so the client can seed read-receipt state on load
        $readStatus = $conversation->participants()
            ->where('user_id', '!=', auth()->id())
            ->whereNotNull('last_read_at')
            ->pluck('last_read_at', 'user_id')
            ->map(fn($dt) => $dt->toISOString());

        return response()->json([
            'data'        => $data,
            'next_cursor' => $messages->nextCursor()?->encode(),
            'read_status' => $readStatus,
        ]);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $request->validate([
            'body'        => 'required|string|max:5000',
            'reply_to_id' => 'nullable|integer|exists:messages,id',
        ]);

        $message = $conversation->messages()->create([
            'sender_id'   => auth()->id(),
            'type'        => 'text',
            'body'        => $request->body,
            'reply_to_id' => $request->reply_to_id,
        ]);

        $conversation->update(['last_message_at' => now()]);

        MessageSent::dispatch($message);

        return response()->json([
            'id'          => $message->id,
            'sender_id'   => $message->sender_id,
            'body'        => $message->body,
            'created_at'  => $message->created_at->toISOString(),
        ], 201);
    }

    public function read(Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $readAt = now();

        $conversation->participants()
            ->where('user_id', auth()->id())
            ->update(['last_read_at' => $readAt]);

        MessageRead::dispatch($conversation->id, auth()->id(), $readAt->toISOString());

        return response()->json(['ok' => true]);
    }

    private function authorizeParticipant(Conversation $conversation): void
    {
        $isParticipant = $conversation->participants()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->exists();

        abort_unless($isParticipant, 403);
    }
}
