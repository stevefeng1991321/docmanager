<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Save notification preferences
        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'notify_file_uploaded'     => $request->boolean('notify_file_uploaded'),
                'notify_version_updated'   => $request->boolean('notify_version_updated'),
                'notify_access_denied'     => $request->boolean('notify_access_denied'),
                'notify_doc_approved'      => $request->boolean('notify_doc_approved'),
                'notify_account_activated' => $request->boolean('notify_account_activated'),
                'view_mode'                => $request->input('view_mode', 'grid'),
            ]
        );

        AuditLog::record('profile.updated', null, ['user_id' => $user->id]);

        return back()->with('message', 'Profile updated.');
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'Account deleted.');
    }
}
