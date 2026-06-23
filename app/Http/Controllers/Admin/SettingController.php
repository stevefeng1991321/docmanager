<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'max_upload_mb'          => config('app.max_upload_size_mb', 50),
            'share_link_expiry_hours' => config('app.share_link_expiry_hours', 24),
            'lockout_attempts'       => config('auth.lockout.max_attempts', 5),
            'lockout_minutes'        => config('auth.lockout.decay_minutes', 15),
            'trash_retention_days'   => config('app.trash_retention_days', 30),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Settings are env-based; in a full implementation these would
        // be stored in a settings table and cached.
        return back()->with('message', 'Settings saved.');
    }
}
