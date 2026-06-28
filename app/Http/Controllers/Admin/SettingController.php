<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\UserPreference;
use App\Support\PasswordPolicy;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    private array $defaults = [
        'max_upload_mb'            => 50,
        'share_link_expiry_hours'  => 24,
        'lockout_attempts'         => 5,
        'lockout_minutes'          => 15,
        'trash_retention_days'     => 30,
        'document_stale_months'    => 6,
        'password_complexity'      => 'standard',
    ];

    public function index()
    {
        $saved    = Setting::allKeyed();
        $settings = array_merge($this->defaults, $saved);

        // Load this admin's personal theme (fall back to global setting, then 'default')
        $userTheme = Auth::user()->preferences?->admin_theme;
        $settings['admin_theme'] = $userTheme ?? Setting::get('admin_theme', 'default');

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
            'document_stale_months'   => ['required', 'integer', 'min:1', 'max:60'],
            'password_complexity'     => ['required', 'in:' . implode(',', array_keys(PasswordPolicy::LEVELS))],
            'admin_theme'             => ['required', 'in:' . implode(',', array_keys(config('admin_themes')))],
        ]);

        // Save theme to this admin's user_preferences (per-user)
        UserPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            ['admin_theme' => $validated['admin_theme']]
        );

        // Save remaining global settings
        unset($validated['admin_theme']);
        Setting::setMany($validated);

        return back()->with('message', 'Settings saved.');
    }

    public function generateQr(Request $request)
    {
        $request->validate([
            'text' => ['required', 'string', 'max:2000'],
            'size' => ['nullable', 'integer', 'min:100', 'max:600'],
        ]);

        $size     = (int) $request->input('size', 300);
        $renderer = new ImageRenderer(new RendererStyle($size), new SvgImageBackEnd());
        $svg      = (new Writer($renderer))->writeString($request->input('text'));

        return response()->json(['svg' => $svg]);
    }
}
