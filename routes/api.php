<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('login',  [Api\AuthController::class, 'login'])->middleware('throttle:api-login');
});

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware(['auth:sanctum', 'active'])->group(function () {

    // Auth
    Route::post('auth/logout', [Api\AuthController::class, 'logout']);
    Route::get('auth/me',      [Api\AuthController::class, 'me']);

    // Documents
    Route::get('resources',                          [Api\ResourceController::class, 'index'])->name('api.resources.index');
    Route::get('resources/{resource}',               [Api\ResourceController::class, 'show'])->name('api.resources.show');
    Route::post('resources',                         [Api\ResourceController::class, 'store'])->name('api.resources.store');
    Route::put('resources/{resource}',               [Api\ResourceController::class, 'update'])->name('api.resources.update');
    Route::delete('resources/{resource}',            [Api\ResourceController::class, 'destroy'])->name('api.resources.destroy');
    Route::get('resources/{resource}/download',      [Api\ResourceController::class, 'download'])->name('api.resources.download')->middleware('throttle:api-download');
    Route::post('resources/{resource}/share',        [Api\ResourceController::class, 'share'])->name('api.resources.share');

    // Search
    Route::get('search', Api\SearchController::class)->name('api.search');
});
