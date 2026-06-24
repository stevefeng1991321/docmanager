<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)
            ->whereIn('status', ['active'])
            ->first();

        // Always show success to prevent username enumeration
        if (!$user) {
            return back()->with('message', 'If that username exists and is active, your request has been submitted. Please contact your administrator to obtain the reset link.');
        }

        // Only allow one pending password reset request per user
        $existing = AccountRequest::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('message', 'A password reset request is already pending for this account. Please contact your administrator.');
        }

        AccountRequest::create([
            'user_id' => $user->id,
            'type'    => 'password_reset',
            'status'  => 'pending',
        ]);

        return back()->with('message', 'Your request has been submitted. Please contact your administrator to obtain the reset link.');
    }
}
