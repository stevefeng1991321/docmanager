<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasicKnowledgeTrend;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Resource;
use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard.stats.v2', 300, function () {
            return [
                'total_documents'    => Resource::count(),
                'uploads_this_week'  => Resource::whereNull('deleted_at')
                                            ->where('created_at', '>=', now()->startOfWeek())
                                            ->count(),
                'published'          => Resource::where('status', 'published')->count(),
                'pending_review'     => Resource::where('status', 'pending_review')->count(),
                'total_users'        => User::where('status', 'active')->count(),
                'pending_users'      => User::where('status', 'pending')->count(),
                'storage_bytes'      => Resource::sum('file_size'),
                'downloads_today'    => DB::table('download_logs')
                                            ->whereDate('created_at', today())
                                            ->count(),
                'failed_jobs'        => DB::table('failed_jobs')->count(),
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

        $employeeStats = Cache::remember('dashboard.employee_stats', 300, function () {
            return [
                'total'         => Employee::count(),
                'active'        => Employee::where('employment_status', 'active')->count(),
                'inactive'      => Employee::where('employment_status', 'inactive')->count(),
                'recent_hires'  => Employee::where('date_of_joining', '>=', now()->subDays(30))->count(),
                'by_department' => Department::withCount('employees')->orderByDesc('employees_count')->limit(5)->get(),
            ];
        });

        $knowledgeStats = Cache::remember('dashboard.knowledge_stats', 300, function () {
            return [
                'total'       => BasicKnowledgeTrend::count(),
                'published'   => BasicKnowledgeTrend::where('status', 'published')->count(),
                'draft'       => BasicKnowledgeTrend::where('status', 'draft')->count(),
                'by_category' => DB::table('basic_knowledge_trends')
                    ->join('categories', 'basic_knowledge_trends.category_id', '=', 'categories.id')
                    ->selectRaw('categories.name, COUNT(*) as count')
                    ->where('basic_knowledge_trends.status', 'published')
                    ->groupBy('categories.id', 'categories.name')
                    ->orderByDesc('count')
                    ->limit(6)
                    ->get(),
                'recent' => BasicKnowledgeTrend::with('category')
                    ->where('status', 'published')
                    ->latest()
                    ->take(4)
                    ->get(),
            ];
        });

        $workReportStats = Cache::remember('dashboard.work_report_stats', 300, function () {
            return [
                'total'            => WorkReport::count(),
                'submitted_today'  => WorkReport::whereDate('submitted_at', today())->count(),
                'pending_review'   => WorkReport::whereIn('status', ['submitted', 'under_review'])->count(),
                'approved'         => WorkReport::where('status', 'approved')->count(),
                'rejected'         => WorkReport::where('status', 'rejected')->count(),
                'draft'            => WorkReport::where('status', 'draft')->count(),
                'recent'           => WorkReport::with('employee')->latest('submitted_at')->whereNotNull('submitted_at')->take(5)->get(),
            ];
        });

        return view('admin.dashboard.index', compact(
            'stats', 'recentDocuments', 'topDownloaded', 'uploadTrend', 'topSearches', 'downloadTrend',
            'employeeStats', 'workReportStats', 'knowledgeStats'
        ));
    }
}
