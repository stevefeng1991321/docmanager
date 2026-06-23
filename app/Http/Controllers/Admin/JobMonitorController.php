<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobMonitorController extends Controller
{
    public function index()
    {
        $pending = DB::table('jobs')->count();
        $failed  = DB::table('failed_jobs')->latest('failed_at')->paginate(30);
        return view('admin.jobs.index', compact('pending', 'failed'));
    }

    public function retry($id)
    {
        \Artisan::call('queue:retry', ['id' => [$id]]);
        return back()->with('message', 'Job queued for retry.');
    }

    public function retryAll()
    {
        \Artisan::call('queue:retry', ['id' => ['all']]);
        return back()->with('message', 'All failed jobs queued for retry.');
    }
}
