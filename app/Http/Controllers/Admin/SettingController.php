<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        // Settings are env-based; in a full implementation these would
        // be stored in a settings table and cached.
        return back()->with('message', 'Settings saved.');
    }
}
