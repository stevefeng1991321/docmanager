<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\AuditLog;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user  = auth()->user();
        $prefs = $user->preferences ?? new UserPreference();
        return view('profile.edit', compact('user', 'prefs'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['nullable', Password::defaults(), 'confirmed'],
            'avatar'   => ['nullable', 'image', 'max:5120'],
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $prefsData = [
            'notify_file_uploaded'     => $request->boolean('notify_file_uploaded'),
            'notify_version_updated'   => $request->boolean('notify_version_updated'),
            'notify_access_denied'     => $request->boolean('notify_access_denied'),
            'notify_doc_approved'      => $request->boolean('notify_doc_approved'),
            'notify_account_activated' => $request->boolean('notify_account_activated'),
            'view_mode'                => $request->input('view_mode', 'grid'),
        ];

        if ($request->hasFile('avatar')) {
            $prefs = $user->preferences;
            if ($prefs && $prefs->avatar) {
                Storage::disk('public')->delete($prefs->avatar);
            }
            $prefsData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        UserPreference::updateOrCreate(['user_id' => $user->id], $prefsData);

        AuditLog::record('profile.updated', null, ['user_id' => $user->id]);

        return back()->with('message', 'Profile updated.');
    }

    public function requestUsernameChange(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'new_username' => [
                'required', 'string', 'min:3', 'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/',
                'unique:users,username',
                'unique:account_requests,new_username,NULL,id,status,pending',
            ],
        ]);

        AccountRequest::create([
            'user_id'      => $user->id,
            'type'         => 'username_change',
            'new_username' => $request->new_username,
        ]);

        AuditLog::record('profile.username_change_requested', null, [
            'user_id'      => $user->id,
            'new_username' => $request->new_username,
        ]);

        return back()->with('message', 'Your username change request has been submitted for admin review.');
    }

    public function requestDeletion(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $existing = AccountRequest::where('user_id', $user->id)
            ->where('type', 'account_deletion')
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('message', 'You already have a pending account deletion request.');
        }

        AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'account_deletion',
            'reason'  => $request->reason,
        ]);

        AuditLog::record('profile.deletion_requested', null, ['user_id' => $user->id]);

        return back()->with('message', 'Your account deletion request has been submitted for admin review.');
    }

    public function destroy(Request $request)
    {
        // Hard delete — admin uses AccountRequestController for the review workflow.
        // This action is kept for direct self-service deletion without a pending request.
        $user = auth()->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'Account deleted.');
    }
}
