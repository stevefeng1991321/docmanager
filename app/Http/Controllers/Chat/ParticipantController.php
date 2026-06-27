<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->isGroup(), 400);

        $conversation->load('participants');
        abort_unless($conversation->involves($request->user()->id), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $target = User::where('id', $validated['user_id'])->where('status', 'active')->firstOrFail();

        if (! $conversation->participants->contains('id', $target->id)) {
            $conversation->participants()->attach($target->id, [
                'role'      => 'member',
                'joined_at' => now(),
            ]);
        }

        return response()->json([
            'ok'           => true,
            'member_count' => $conversation->participants()->count(),
        ]);
    }

    public function destroy(Request $request, Conversation $conversation, User $user)
    {
        abort_unless($conversation->isGroup(), 400);

        $conversation->load('participants');

        $currentUser = $request->user();
        $isSelf      = $user->id === $currentUser->id;
        $isAdmin     = $conversation->participants->firstWhere('id', $currentUser->id)?->pivot->role === 'admin';

        abort_unless($isSelf || $isAdmin, 403);
        abort_unless($conversation->involves($user->id), 404);

        $conversation->participants()->detach($user->id);

        return response()->json(['ok' => true]);
    }
}
