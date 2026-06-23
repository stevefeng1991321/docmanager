<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use Illuminate\Http\Request;

class AccountRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = AccountRequest::with('user')
            ->when($request->type,   fn($q, $t) => $q->where('type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.account-requests.index', compact('requests'));
    }

    public function approve(Request $request, AccountRequest $accountRequest)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:500']]);

        if ($accountRequest->type === 'username_change') {
            $user = $accountRequest->user;

            if (!$user) {
                $accountRequest->update(['status' => 'rejected', 'admin_note' => 'User no longer exists.']);
                return back()->with('message', 'User no longer exists — request rejected.');
            }

            $user->update(['username' => $accountRequest->new_username]);
            $accountRequest->update(['status' => 'approved', 'admin_note' => $request->admin_note]);

            Notification::send($user->id, 'account_activated',
                'Username Changed',
                "Your username has been updated to \"{$accountRequest->new_username}\".");

            AuditLog::record('account_request.username_changed', null, [
                'request_id'   => $accountRequest->id,
                'user_id'      => $user->id,
                'new_username' => $accountRequest->new_username,
            ]);

            return back()->with('message', "Username changed to \"{$accountRequest->new_username}\".");
        }

        if ($accountRequest->type === 'account_deletion') {
            $user = $accountRequest->user;

            $accountRequest->update(['status' => 'approved', 'admin_note' => $request->admin_note]);

            AuditLog::record('account_request.deletion_approved', null, [
                'request_id' => $accountRequest->id,
                'user_id'    => $user?->id,
                'username'   => $user?->username,
            ]);

            $user?->delete();

            return back()->with('message', 'Account deletion approved and account removed.');
        }

        return back()->with('error', 'Unknown request type.');
    }

    public function reject(Request $request, AccountRequest $accountRequest)
    {
        $request->validate(['admin_note' => ['nullable', 'string', 'max:500']]);

        $accountRequest->update(['status' => 'rejected', 'admin_note' => $request->admin_note]);

        if ($accountRequest->user) {
            $message = $accountRequest->type === 'username_change'
                ? 'Your username change request was not approved.'
                : 'Your account deletion request was not approved.';

            Notification::send($accountRequest->user->id, 'account_activated',
                'Request Not Approved',
                $request->admin_note ?? $message);
        }

        AuditLog::record('account_request.rejected', null, [
            'request_id' => $accountRequest->id,
            'type'       => $accountRequest->type,
        ]);

        return back()->with('message', 'Request rejected.');
    }
}
