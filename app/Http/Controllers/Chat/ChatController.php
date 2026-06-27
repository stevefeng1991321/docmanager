<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['reads', 'userOne', 'userTwo', 'participants'])
            ->get()
            ->sortByDesc(fn (Conversation $c) => $c->last_message_at ?? $c->created_at)
            ->map(fn (Conversation $c) => $c->toListItem($user->id))
            ->values();

        $openWithUser = null;
        if ($request->filled('user')) {
            $target = User::where('id', $request->integer('user'))
                ->where('status', 'active')
                ->where('id', '!=', $user->id)
                ->first();

            if ($target) {
                $conversation = Conversation::between($user, $target);
                $openWithUser = $conversation->id;
            }
        }

        return view('chat.index', [
            'currentUser'    => ['id' => $user->id, 'name' => $user->name, 'username' => $user->username],
            'conversations'  => $conversations,
            'openConversationId' => $openWithUser,
        ]);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();

        $count = $user->conversations()
            ->with('reads')
            ->get()
            ->sum(fn (Conversation $c) => $c->unreadCountFor($user->id));

        return response()->json(['unread_count' => $count]);
    }
}
