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

    public function export(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->action, fn($q, $a) => $q->where('action', 'like', "%{$a}%"))
            ->when($request->user,   fn($q, $u) => $q->whereHas('user', fn($q) => $q->where('username', 'like', "%{$u}%")))
            ->latest('created_at')
            ->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="audit-logs.csv"'];

        $callback = function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Time', 'User', 'Action', 'Resource ID', 'IP Address', 'Details']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user?->username ?? '—',
                    $log->action,
                    $log->resource_id ?? '',
                    $log->ip_address ?? '',
                    json_encode($log->details),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
