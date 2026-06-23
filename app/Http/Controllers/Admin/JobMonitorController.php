<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobMonitorController extends Controller
{
    public function index()
    {
        $pending    = DB::table('jobs')->count();
        $failed     = DB::table('failed_jobs')->latest('failed_at')->paginate(30);
        return view('admin.jobs.index', compact('pending', 'failed'));
    }
}
