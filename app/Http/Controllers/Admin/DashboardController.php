<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard.stats', 300, function () {
            return [
                'total_documents' => Resource::count(),
                'published'       => Resource::where('status', 'published')->count(),
                'pending_review'  => Resource::where('status', 'pending_review')->count(),
                'total_users'     => User::where('status', 'active')->count(),
                'pending_users'   => User::where('status', 'pending')->count(),
                'storage_bytes'   => Resource::sum('file_size'),
                'downloads_today' => DB::table('download_logs')
                                        ->whereDate('created_at', today())
                                        ->count(),
                'failed_jobs'     => DB::table('failed_jobs')->count(),
            ];
        });

        $uploadTrend = Cache::remember('dashboard.upload_trend', 600, function () {
            return DB::table('resources')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereNull('deleted_at')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        $downloadTrend = Cache::remember('dashboard.download_trend', 600, function () {
            return DB::table('download_logs')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });

        $topSearches = Cache::remember('dashboard.top_searches', 900, function () {
            return DB::table('search_logs')
                ->selectRaw('query, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(10)
                ->get();
        });

        $recentDocuments = Resource::with('uploader', 'category')
            ->latest()
            ->take(10)
            ->get();

        $topDownloaded = Resource::where('download_count', '>', 0)
            ->orderByDesc('download_count')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 'recentDocuments', 'topDownloaded', 'uploadTrend', 'topSearches', 'downloadTrend'
        ));
    }
}
