<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StorageController extends Controller
{
    public function index()
    {
        $byUser = User::withSum('resources', 'file_size')
            ->having('resources_sum_file_size', '>', 0)
            ->orderByDesc('resources_sum_file_size')
            ->paginate(20);

        $byType = Resource::selectRaw('file_type, COUNT(*) as count, SUM(file_size) as total_size')
            ->groupBy('file_type')
            ->orderByDesc('total_size')
            ->get();

        $totalBytes = Resource::sum('file_size');

        return view('admin.storage.index', compact('byUser', 'byType', 'totalBytes'));
    }
}
