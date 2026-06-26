<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanComment;
use App\Models\Notification;
use Illuminate\Http\Request;

class PlanCommentController extends Controller
{
    public function store(Request $request, Plan $plan)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $plan->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        // Notify plan owner if commenter is someone else
        if ($plan->owner_id !== auth()->id()) {
            Notification::send(
                $plan->owner_id,
                'plan_comment',
                'New Comment on Plan',
                auth()->user()->name . ' commented on: ' . $plan->title,
                $plan->id
            );
        }

        return back()->with('message', 'Comment added.');
    }

    public function destroy(Plan $plan, PlanComment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('message', 'Comment deleted.');
    }
}
