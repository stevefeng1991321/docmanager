<?php

namespace App\Http\Controllers\Client;

use App\Events\GroupUpdated;
use App\Events\MessageDeleted;
use App\Events\MessageEdited;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\NewMessageNotification;
use App\Events\UserTyping;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // ─── Views ───────────────────────────────────────────────────────────────

    public function index()
    {
        $conversations = $this->userConversations();
        return view('chat.index', compact('conversations'));
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

        $myParticipant = $conversation->participants
            ->firstWhere('user_id', auth()->id());

        return view('chat.show', [
            'conversation'  => $conversation,
            'conversations' => $conversations,
            'isMuted'       => (bool) $myParticipant?->notifications_muted,
            'myRole'        => $myParticipant?->role ?? 'member',
        ]);
    }

    // ─── Messages ─────────────────────────────────────────────────────────────

    public function apiMessages(Conversation $conversation, Request $request): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $messages = $conversation->messages()
            ->with(['sender:id,name', 'replyTo.sender:id,name'])
            ->withTrashed()
            ->when($request->cursor, fn($q) => $q->where('id', '<', $request->cursor))
            ->reorder('id', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $nextCursor = $messages->isNotEmpty() && $conversation->messages()->withTrashed()->where('id', '<', $messages->first()->id)->exists()
            ? $messages->first()->id
            : null;

        $data = $messages->map(fn($msg) => $this->formatMessage($msg));

        $readStatus = $conversation->participants()
            ->where('user_id', '!=', auth()->id())
            ->whereNotNull('last_read_at')
            ->pluck('last_read_at', 'user_id')
            ->map(fn($dt) => $dt->toISOString());

        return response()->json([
            'data'        => $data,
            'next_cursor' => $nextCursor,
            'read_status' => $readStatus,
        ]);
    }

    public function apiSend(Request $request, Conversation $conversation): JsonResponse
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

        $message->loadMissing(['sender', 'replyTo.sender']);
        $conversation->update(['last_message_at' => now()]);
        MessageSent::dispatch($message);

        $conversation->participants()
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id')
            ->each(fn($uid) => NewMessageNotification::dispatch($message, $uid));

        return response()->json($this->formatMessage($message), 201);
    }

    public function apiDeleteMessage(Conversation $conversation, Message $message): JsonResponse
    {
        $this->authorizeParticipant($conversation);
        abort_unless($message->conversation_id === $conversation->id, 404);
        abort_unless($message->sender_id === auth()->id(), 403);

        $message->delete();
        MessageDeleted::dispatch($message);

        return response()->json(['ok' => true]);
    }

    public function apiEditMessage(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $this->authorizeParticipant($conversation);
        abort_unless($message->conversation_id === $conversation->id, 404);
        abort_unless($message->sender_id === auth()->id(), 403);
        abort_if((bool) $message->deleted_at, 403);

        $request->validate(['body' => 'required|string|max:5000']);

        $message->update([
            'body'      => $request->body,
            'edited_at' => now(),
        ]);

        MessageEdited::dispatch($message);

        return response()->json([
            'id'        => $message->id,
            'body'      => $message->body,
            'edited_at' => $message->edited_at->toISOString(),
        ]);
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

    public function apiTyping(Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);
        UserTyping::dispatch($conversation->id, auth()->id(), auth()->user()->name);
        return response()->json(['ok' => true]);
    }

    // ─── Conversation management ───────────────────────────────────────────────

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
            $otherId  = $request->user_ids[0];
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

    public function leaveGroup(Conversation $conversation): JsonResponse
    {
        abort_unless($conversation->type === 'group', 403);
        $this->authorizeParticipant($conversation);

        $conversation->participants()
            ->where('user_id', auth()->id())
            ->update(['left_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function toggleMute(Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);

        $participant = $conversation->participants()
            ->where('user_id', auth()->id())
            ->first();

        $muted = !$participant->notifications_muted;
        $participant->update(['notifications_muted' => $muted]);

        return response()->json(['muted' => $muted]);
    }

    public function addMembers(Request $request, Conversation $conversation): JsonResponse
    {
        abort_unless($conversation->type === 'group', 403);
        $this->authorizeGroupAdmin($conversation);

        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $addedUsers = [];
        foreach ($request->user_ids as $uid) {
            $conversation->participants()->updateOrCreate(
                ['user_id' => $uid],
                ['role' => 'member', 'left_at' => null]
            );
            $user = User::find($uid);
            if ($user) $addedUsers[] = ['user_id' => $uid, 'name' => $user->name, 'role' => 'member'];
        }

        GroupUpdated::dispatch($conversation, 'members_added', ['users' => $addedUsers]);

        return response()->json(['ok' => true]);
    }

    public function removeMember(Conversation $conversation, User $user): JsonResponse
    {
        abort_unless($conversation->type === 'group', 403);
        $this->authorizeGroupAdmin($conversation);

        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['left_at' => now()]);

        GroupUpdated::dispatch($conversation, 'member_removed', ['user_id' => $user->id]);

        return response()->json(['ok' => true]);
    }

    public function renameGroup(Request $request, Conversation $conversation): JsonResponse
    {
        abort_unless($conversation->type === 'group', 403);
        $this->authorizeGroupAdmin($conversation);

        $request->validate(['name' => 'required|string|max:100']);
        $conversation->update(['name' => $request->name]);

        GroupUpdated::dispatch($conversation, 'renamed', ['name' => $conversation->name]);

        return response()->json(['ok' => true, 'name' => $conversation->name]);
    }

    public function apiSearch(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);
        $q = trim($request->string('q'));
        if (strlen($q) < 2) return response()->json([]);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->whereNull('deleted_at')
            ->where('body', 'like', '%' . $q . '%')
            ->reorder('id', 'desc')
            ->limit(20)
            ->get()
            ->map(fn($m) => $this->formatMessage($m));

        return response()->json($messages);
    }

    public function apiUpload(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($conversation);
        $request->validate(['file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip']);

        $file    = $request->file('file');
        $mime    = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');
        $path    = $file->store('chat', 'public');

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'type'      => $isImage ? 'image' : 'file',
            'body'      => null,
            'metadata'  => [
                'path'     => $path,
                'filename' => $file->getClientOriginalName(),
                'size'     => $file->getSize(),
                'mime'     => $mime,
            ],
        ]);

        $message->loadMissing(['sender', 'replyTo.sender']);
        $conversation->update(['last_message_at' => now()]);
        MessageSent::dispatch($message);

        $conversation->participants()
            ->where('user_id', '!=', auth()->id())
            ->pluck('user_id')
            ->each(fn($uid) => NewMessageNotification::dispatch($message, $uid));

        return response()->json($this->formatMessage($message), 201);
    }

    public function users(): JsonResponse
    {
        $users = User::where('id', '!=', auth()->id())
            ->where('status', UserStatus::Active)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($users);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

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

    private function formatMessage(Message $msg): array
    {
        return [
            'id'             => $msg->id,
            'sender_id'      => $msg->sender_id,
            'sender_name'    => $msg->sender->name,
            'sender_initial' => strtoupper(substr($msg->sender->name, 0, 1)),
            'body'           => $msg->deleted_at ? null : $msg->body,
            'deleted'        => (bool) $msg->deleted_at,
            'edited_at'      => $msg->edited_at?->toISOString(),
            'type'           => $msg->type,
            'reply_to_id'    => $msg->reply_to_id,
            'reply_to'       => $msg->reply_to_id && $msg->replyTo ? [
                'id'          => $msg->replyTo->id,
                'type'        => $msg->replyTo->type,
                'body'        => $msg->replyTo->deleted_at ? null : $msg->replyTo->body,
                'metadata'    => (!$msg->replyTo->deleted_at && $msg->replyTo->metadata)
                    ? array_merge($msg->replyTo->metadata, ['url' => asset('storage/' . ($msg->replyTo->metadata['path'] ?? ''))])
                    : null,
                'sender_name' => $msg->replyTo->sender?->name ?? 'Unknown',
                'deleted'     => (bool) $msg->replyTo->deleted_at,
            ] : null,
            'metadata'       => $msg->metadata
                ? array_merge($msg->metadata, ['url' => asset('storage/' . ($msg->metadata['path'] ?? ''))])
                : null,
            'created_at'     => $msg->created_at->toISOString(),
        ];
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

    private function authorizeGroupAdmin(Conversation $conversation): void
    {
        abort_unless(
            $conversation->participants()
                ->where('user_id', auth()->id())
                ->whereIn('role', ['owner', 'admin'])
                ->whereNull('left_at')
                ->exists(),
            403
        );
    }
}
