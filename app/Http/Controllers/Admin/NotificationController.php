<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index');
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $users = User::where('status', UserStatus::Active)->pluck('id');
        foreach ($users as $userId) {
            Notification::send($userId, 'system_broadcast', $request->title, $request->message);
        }

        return back()->with('message', "Notification sent to {$users->count()} users.");
    }
}
