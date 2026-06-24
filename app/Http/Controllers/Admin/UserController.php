<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, fn($q, $s) => $q->where('username', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"))
            ->when($request->role,   fn($q, $r) => $q->where('role', $r))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function pending()
    {
        $users = User::where('status', 'pending')->orderBy('created_at')->paginate(20);
        return view('admin.users.pending', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {

        $user = User::create([
            'username' => $request->username,
            'name'     => $request->name,
            'password' => $request->password,
            'role'     => $request->role,
            'status'   => 'active',
        ]);

        AuditLog::record('user.created', null, ['username' => $user->username, 'role' => $user->role]);

        return redirect()->route('admin.users.index')->with('message', "Account '{$user->username}' created.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        $user->update($request->only('username', 'name', 'role', 'status', 'storage_quota_mb'));
        AuditLog::record('user.updated', null, ['user_id' => $user->id]);

        return redirect()->route('admin.users.index')->with('message', 'User updated.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        Notification::send($user->id, 'account_activated', 'Account Activated', 'Your account has been activated. You can now sign in.');
        AuditLog::record('user.activated', null, ['user_id' => $user->id, 'username' => $user->username]);

        return back()->with('message', "Account '{$user->username}' activated.");
    }

    public function deactivate(User $user)
    {
        $user->update(['status' => 'inactive']);
        AuditLog::record('user.deactivated', null, ['user_id' => $user->id]);

        return back()->with('message', "Account '{$user->username}' deactivated.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => ['required', 'string', 'min:8']]);
        $user->update(['password' => Hash::make($request->password)]);
        AuditLog::record('user.password_reset', null, ['user_id' => $user->id]);

        return back()->with('message', "Password reset for '{$user->username}'.");
    }

    public function bulkActivate(Request $request)
    {
        $request->validate(['ids' => ['required', 'array']]);
        $users = User::whereIn('id', $request->ids)->where('status', 'pending')->get();

        foreach ($users as $user) {
            $user->update(['status' => 'active']);
            Notification::send($user->id, 'account_activated', 'Account Activated', 'Your account has been activated. You can now sign in.');
        }

        AuditLog::record('user.bulk_activated', null, ['count' => $users->count()]);

        return back()->with('message', "{$users->count()} account(s) activated.");
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids'    => ['required', 'array'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $users = User::whereIn('id', $request->ids)->where('status', 'pending')->get();
        $reason = $request->reason ?? 'Your registration request was not approved.';

        foreach ($users as $user) {
            Notification::send($user->id, 'account_rejected', 'Registration Not Approved', $reason);
            $user->delete();
        }

        AuditLog::record('user.bulk_rejected', null, ['count' => $users->count(), 'reason' => $reason]);

        return back()->with('message', "{$users->count()} account(s) rejected and removed.");
    }
}
