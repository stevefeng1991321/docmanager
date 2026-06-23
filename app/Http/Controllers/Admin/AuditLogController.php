<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user', 'resource')
            ->when($request->action, fn($q, $a) => $q->where('action', 'like', "%{$a}%"))
            ->when($request->user,   fn($q, $u) => $q->whereHas('user', fn($q) => $q->where('username', 'like', "%{$u}%")))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }
}
