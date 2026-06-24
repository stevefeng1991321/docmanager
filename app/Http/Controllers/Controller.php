<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

abstract class Controller
{
    protected function clearDocumentCaches(): void
    {
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.upload_trend');
        Cache::forget('dashboard.download_trend');
        Cache::forget('home.categories.tree');
    }
}
