<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Client;
use Illuminate\Support\Facades\Route;

// ─── Client Web App ──────────────────────────────────────────────────────────

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/', [Client\HomeController::class, 'index'])->name('home');

    // Documents
    Route::get('/documents/{resource}',         [Client\DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{resource}/preview', [Client\DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{resource}/download',[Client\DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{resource}/share',  [Client\DocumentController::class, 'share'])->name('documents.share');

    // Browse
    Route::get('/categories/{category:slug}', [Client\CategoryController::class, 'show'])->name('categories.show');
    Route::get('/tags/{tag:slug}',            [Client\TagController::class, 'show'])->name('tags.show');

    // Search
    Route::get('/search', [Client\SearchController::class, 'index'])->name('search');

    // Personal library
    Route::get('/favorites',     [Client\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{resource}',   [Client\FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{resource}', [Client\FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/history',    [Client\RecentlyViewedController::class, 'index'])->name('history.index');
    Route::get('/downloads',  [Client\DownloadHistoryController::class, 'index'])->name('downloads.index');

    Route::resource('/saved-searches',  Client\SavedSearchController::class)->except(['show']);
    Route::resource('/reading-lists',   Client\ReadingListController::class);
    Route::post('/reading-lists/{readingList}/items/{resource}',   [Client\ReadingListController::class, 'addItem'])->name('reading-lists.items.add');
    Route::delete('/reading-lists/{readingList}/items/{resource}', [Client\ReadingListController::class, 'removeItem'])->name('reading-lists.items.remove');

    Route::post('/bookmarks',           [Client\BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::put('/bookmarks/{bookmark}', [Client\BookmarkController::class, 'update'])->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', [Client\BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    Route::post('/ratings/{resource}',  [Client\RatingController::class, 'store'])->name('ratings.store');

    Route::get('/notifications',        [Client\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [Client\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}',     [Client\NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/profile',    [Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [Client\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [Client\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public share link (no auth required)
Route::get('/share/{token}', [Client\ShareController::class, 'show'])->name('share.show');

// ─── Admin Panel ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'active', 'role:admin,editor'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Documents — custom routes before resource() to avoid wildcard conflict
    Route::get('documents/trash',              [Admin\DocumentController::class, 'trash'])->name('documents.trash');
    Route::resource('documents', Admin\DocumentController::class);
    Route::patch('documents/{resource}/restore',[Admin\DocumentController::class, 'restore'])->name('documents.restore');
    Route::delete('documents/{resource}/force', [Admin\DocumentController::class, 'forceDelete'])->name('documents.force-delete');
    Route::patch('documents/{resource}/lock',   [Admin\DocumentController::class, 'lock'])->name('documents.lock');
    Route::patch('documents/{resource}/unlock', [Admin\DocumentController::class, 'unlock'])->name('documents.unlock');
    Route::patch('documents/{resource}/approve',[Admin\DocumentController::class, 'approve'])->name('documents.approve');
    Route::patch('documents/{resource}/reject', [Admin\DocumentController::class, 'reject'])->name('documents.reject');

    // Versions
    Route::post('documents/{resource}/versions',              [Admin\VersionController::class, 'store'])->name('versions.store');
    Route::patch('documents/{resource}/versions/{version}/restore', [Admin\VersionController::class, 'restore'])->name('versions.restore');

    // Categories & Tags
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('tags', Admin\TagController::class);

    // Users (admin only) — static routes before resource() to avoid wildcard conflict
    Route::middleware('role:admin')->group(function () {
        Route::get('users/pending',          [Admin\UserController::class, 'pending'])->name('users.pending');
        Route::post('users/bulk-activate',   [Admin\UserController::class, 'bulkActivate'])->name('users.bulk-activate');
        Route::post('users/bulk-reject',     [Admin\UserController::class, 'bulkReject'])->name('users.bulk-reject');
        Route::resource('users', Admin\UserController::class);
        Route::patch('users/{user}/activate',       [Admin\UserController::class, 'activate'])->name('users.activate');
        Route::patch('users/{user}/deactivate',     [Admin\UserController::class, 'deactivate'])->name('users.deactivate');
        Route::patch('users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Logs & Analytics
    Route::get('audit-logs',    [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('jobs',          [Admin\JobMonitorController::class, 'index'])->name('jobs.index');
    Route::get('storage',       [Admin\StorageController::class, 'index'])->name('storage.index');
    Route::get('search',        [Admin\SearchIndexController::class, 'index'])->name('search.index');
    Route::post('search/reindex',[Admin\SearchIndexController::class, 'reindex'])->name('search.reindex');

    // Job retry
    Route::post('jobs/{id}/retry',  [Admin\JobMonitorController::class, 'retry'])->name('jobs.retry');
    Route::post('jobs/retry-all',   [Admin\JobMonitorController::class, 'retryAll'])->name('jobs.retry-all');

    // Notifications broadcast
    Route::post('notifications/broadcast', [Admin\NotificationController::class, 'broadcast'])->name('notifications.broadcast');

    // Settings (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('settings',  [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings',  [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    });
});

require __DIR__.'/auth.php';
