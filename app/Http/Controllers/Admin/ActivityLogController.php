<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->event, fn($q, $e) => $q->where('event', 'like', "%{$e}%"))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.activity-logs.index', compact('logs'));
    }
}
