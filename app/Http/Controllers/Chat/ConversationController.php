<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessagesDelivered;
use App\Events\MessagesRead;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = trim((string) $request->input('q', ''));

        $conversations = $user->conversations()
            ->with(['reads', 'userOne', 'userTwo', 'participants'])
            ->get();

        if ($query !== '') {
            $needle = mb_strtolower($query);

            $conversations = $conversations->filter(function (Conversation $c) use ($user, $needle, $query) {
                if ($c->isDirect()) {
                    $other = $c->otherUser($user->id);
                    if (str_contains(mb_strtolower($other->name), $needle) ||
                        str_contains(mb_strtolower($other->username), $needle)) {
                        return true;
                    }
                } else {
                    if (str_contains(mb_strtolower($c->name ?? ''), $needle)) {
                        return true;
                    }
                    foreach ($c->participants as $participant) {
                        if (str_contains(mb_strtolower($participant->name), $needle)) {
                            return true;
                        }
                    }
                }

                return $c->messages()->where('body', 'like', "%{$query}%")->exists();
            });
        }

        $list = $conversations
            ->sortByDesc(fn (Conversation $c) => $c->last_message_at ?? $c->created_at)
            ->map(fn (Conversation $c) => $c->toListItem($user->id))
            ->values();

        return response()->json(['conversations' => $list]);
    }

    public function store(Request $request)
    {
        // Group creation
        if ($request->has('name') || $request->has('member_ids')) {
            $validated = $request->validate([
                'name'         => ['required', 'string', 'max:100'],
                'member_ids'   => ['required', 'array', 'min:1', 'max:49'],
                'member_ids.*' => ['integer', 'exists:users,id'],
            ]);

            $group = Conversation::createGroup(
                $validated['name'],
                $request->user(),
                $validated['member_ids']
            );

            $group->load('participants');
            return response()->json($group->toListItem($request->user()->id));
        }

        // Direct conversation
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'different:' . $request->user()->id],
        ]);

        $target       = User::where('id', $validated['user_id'])->where('status', 'active')->firstOrFail();
        $conversation = Conversation::between($request->user(), $target);

        return response()->json($conversation->fresh(['reads', 'userOne', 'userTwo'])->toListItem($request->user()->id));
    }

    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->isGroup()) {
            $conversation->load('participants');
        } else {
            $conversation->load(['userOne', 'userTwo']);
        }

        abort_unless($conversation->involves($user->id), 403);

        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        $now = now();

        $deliveredCount = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => $now]);

        if ($deliveredCount > 0) {
            $this->broadcastSafely(new MessagesDelivered($conversation->id, $user->id, $now));
        }

        $conversation->reads()->updateOrCreate(['user_id' => $user->id], ['last_read_at' => $now]);
        $this->broadcastSafely(new MessagesRead($conversation->id, $user->id, $now));

        // Read receipt comparison only applies to direct conversations
        $otherLastReadAt = null;
        if ($conversation->isDirect()) {
            $otherUserId     = $conversation->otherUser($user->id)->id;
            $otherLastReadAt = $conversation->reads->firstWhere('user_id', $otherUserId)?->last_read_at;
        }

        return response()->json([
            'conversation' => $conversation->toListItem($user->id),
            'messages'     => $messages->map(fn ($m) => $this->serializeMessage($m, $user->id, $otherLastReadAt)),
        ]);
    }

    public function markDelivered(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        abort_unless($conversation->involves($user->id), 403);

        $now   = now();
        $count = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => $now]);

        if ($count > 0) {
            $this->broadcastSafely(new MessagesDelivered($conversation->id, $user->id, $now));
        }

        return response()->json(['ok' => true]);
    }

    public function markRead(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        abort_unless($conversation->involves($user->id), 403);

        $now = now();
        $conversation->reads()->updateOrCreate(['user_id' => $user->id], ['last_read_at' => $now]);
        $this->broadcastSafely(new MessagesRead($conversation->id, $user->id, $now));

        return response()->json(['ok' => true]);
    }

    public function users(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $users = User::where('status', 'active')
            ->where('id', '!=', $request->user()->id)
            ->when($query !== '', function ($q) use ($query) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%"));
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'username', 'role']);

        return response()->json(['users' => $users]);
    }

    private function broadcastSafely(object $event): void
    {
        try {
            event($event);
        } catch (Throwable $e) {
            Log::warning('Chat broadcast failed for ' . get_class($event) . ': ' . $e->getMessage());
        }
    }

    private function serializeMessage($message, int $viewerId, ?\Illuminate\Support\Carbon $otherLastReadAt): array
    {
        $isMine = $message->sender_id === $viewerId;
        $status = 'sent';

        if ($isMine && $otherLastReadAt !== null) {
            if ($otherLastReadAt && $message->created_at <= $otherLastReadAt) {
                $status = 'read';
            } elseif ($message->delivered_at) {
                $status = 'delivered';
            }
        }

        return [
            'id'          => $message->id,
            'sender_id'   => $message->sender_id,
            'sender_name' => $message->sender?->name ?? '[Deleted]',
            'body'        => $message->body,
            'is_mine'     => $isMine,
            'status'      => $isMine ? $status : null,
            'created_at'  => $message->created_at->toIso8601String(),
        ];
    }
}
