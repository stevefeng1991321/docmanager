<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConversationController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();

        $conversations = Conversation::forUser($userId)
            ->with(['participants.user', 'latestMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $participant = $conversation->participants->firstWhere('user_id', $userId);

                $unreadCount = $conversation->messages()
                    ->when($participant?->last_read_at, fn($q, $dt) => $q->where('created_at', '>', $dt))
                    ->count();

                $others = $conversation->participants
                    ->where('user_id', '!=', $userId)
                    ->values();

                return [
                    'id'              => $conversation->id,
                    'type'            => $conversation->type,
                    'name'            => $conversation->type === 'group'
                        ? $conversation->name
                        : ($others->first()?->user->name ?? 'Unknown'),
                    'initial'         => strtoupper(substr(
                        $conversation->type === 'group'
                            ? ($conversation->name ?? 'G')
                            : ($others->first()?->user->name ?? 'U'),
                        0, 1
                    )),
                    'last_message'    => $conversation->latestMessage?->body,
                    'last_message_at' => $conversation->last_message_at?->toISOString(),
                    'unread_count'    => $unreadCount,
                    'my_role'         => $participant?->role,
                ];
            });

        return response()->json($conversations);
    }

    public function store(Request $request): JsonResponse
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

            // Re-use existing private conversation if one already exists
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

            $userIds = array_unique(array_merge([$myId], $request->user_ids));

            foreach ($userIds as $uid) {
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
}
