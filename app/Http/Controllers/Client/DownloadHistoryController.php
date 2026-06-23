<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;

class DownloadHistoryController extends Controller
{
    public function index()
    {
        $downloads = DownloadLog::with(['resource.category'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('downloads.index', compact('downloads'));
    }
}
