<?php

namespace App\Http\Controllers\Client;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\NewMessageNotification;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = $this->userConversations();
        return view('chat.index', compact('conversations'));
    }

    private function userConversations(): \Illuminate\Support\Collection
    {
        return Conversation::forUser(auth()->id())
            ->with(['participants.user', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conversation) {
                $participant = $conversation->participants
                    ->firstWhere('user_id', auth()->id());

                $conversation->unread_count = $conversation->messages()
                    ->when($participant?->last_read_at, fn($q, $dt) => $q->where('created_at', '>', $dt))
                    ->count();

                return $conversation;
            });
    }

    public function show(Conversation $conversation)
    {
        $isParticipant = $conversation->participants()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->exists();

        abort_unless($isParticipant, 403);

        $conversation->load(['participants.user']);

        $conversations = $this->userConversations();

        return view('chat.show', [
            'conversation'  => $conversation,
            'conversations' => $conversations,
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'type'       => 'required|in:private,group',
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => ['exists:users,id', Rule::notIn([auth()->id()])],
            'name'       => 'required_if:type,group|nullable|string|max:100',
        ]);

        $myId = auth()->id();

        if ($request->type === 'private') {
            $otherId = $request->user_ids[0];
            $existing = Conversation::where('type', 'private')
                ->whereHas('participants', fn($q) => $q->where('user_id', $myId))
                ->whereHas('participants', fn($q) => $q->where('user_id', $otherId))
                ->first();

            if ($existing) {
                return response()->json(['id' => $existing->id, 'existing' => true]);
            }
        }

        $conversation = DB::transaction(function () use ($request, $myId) {
            $conv = Conversation::create([
                'type'       => $request->type,
                'name'       => $request->name,
                'created_by' => $myId,
            ]);

            foreach (array_unique(array_merge([$myId], $request->user_ids)) as $uid) {
                $conv->participants()->create([
                    'user_id' => $uid,
                    'role'    => $uid === $myId ? 'owner' : 'member',
                ]);
            }

            return $conv;
        });

        return response()->json(['id' => $conversation->id, 'existing' => false], 201);
    }

    public function users(): JsonResponse
    {
        $users = User::where('id', '!=', auth()->id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($users);
    }

    public function apiMessages(Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->withTrashed()
            ->cursorPaginate(50);

        $data = $messages->map(fn($msg) => [
            'id'             => $msg->id,
            'sender_id'      => $msg->sender_id,
            'sender_name'    => $msg->sender->name,
            'sender_initial' => strtoupper(substr($msg->sender->name, 0, 1)),
            'body'           => $msg->deleted_at ? null : $msg->body,
            'deleted'        => (bool) $msg->deleted_at,
            'type'           => $msg->type,
            'reply_to_id'    => $msg->reply_to_id,
            'created_at'     => $msg->created_at->toISOString(),
        ]);

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

    public function apiSend(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $request->validate(['body' => 'required|string|max:5000']);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'type'      => 'text',
            'body'      => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        MessageSent::dispatch($message);

        // Notify every other participant on their personal channel (works even when off the chat page)
        $conversation->participants()
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id')
            ->each(fn($uid) => NewMessageNotification::dispatch($message, $uid));

        return response()->json([
            'id'         => $message->id,
            'sender_id'  => $message->sender_id,
            'body'       => $message->body,
            'created_at' => $message->created_at->toISOString(),
        ], 201);
    }

    public function apiRead(Conversation $conversation): JsonResponse
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
        abort_unless(
            $conversation->participants()
                ->where('user_id', auth()->id())
                ->whereNull('left_at')
                ->exists(),
            403
        );
    }
}
