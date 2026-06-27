<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $defaults = [
        'max_upload_mb'           => 50,
        'share_link_expiry_hours' => 24,
        'lockout_attempts'        => 5,
        'lockout_minutes'         => 15,
        'trash_retention_days'    => 30,
        'password_complexity'     => 'standard',
        'admin_theme'             => 'default',
    ];

    public function index()
    {
        $saved    = Setting::allKeyed();
        $settings = array_merge($this->defaults, $saved);
        $passwordLevels = PasswordPolicy::LEVELS;
        $themes = config('admin_themes');

        return view('admin.settings.index', compact('settings', 'passwordLevels', 'themes'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_upload_mb'           => ['required', 'integer', 'min:1', 'max:500'],
            'share_link_expiry_hours' => ['required', 'integer', 'min:1'],
            'lockout_attempts'        => ['required', 'integer', 'min:3', 'max:20'],
            'lockout_minutes'         => ['required', 'integer', 'min:1'],
            'trash_retention_days'    => ['required', 'integer', 'min:1'],
            'password_complexity'     => ['required', 'in:' . implode(',', array_keys(PasswordPolicy::LEVELS))],
            'admin_theme'             => ['required', 'in:' . implode(',', array_keys(config('admin_themes')))],
        ]);

        Setting::setMany($validated);

        return back()->with('message', 'Settings saved.');
    }
}
