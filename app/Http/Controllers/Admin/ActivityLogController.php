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

    public function export(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->event, fn($q, $e) => $q->where('event', 'like', "%{$e}%"))
            ->latest('created_at')
            ->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="activity-logs.csv"'];

        $callback = function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Time', 'User', 'Event', 'IP Address', 'User Agent', 'Details']);
            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $log->user?->username ?? '—',
                    $log->event,
                    $log->ip_address ?? '',
                    $log->user_agent ?? '',
                    json_encode($log->details),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
