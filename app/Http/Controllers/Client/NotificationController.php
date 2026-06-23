<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->delete();
        return back();
    }
}
