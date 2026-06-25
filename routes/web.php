<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Client;
use Illuminate\Support\Facades\Route;

// ─── Client Web App ──────────────────────────────────────────────────────────

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/', [Client\HomeController::class, 'index'])->name('home');
    Route::get('/home/browse', [Client\HomeController::class, 'browse'])->name('home.browse');

    // Documents
    Route::get('/documents/{resource}',         [Client\DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{resource}/preview', [Client\DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{resource}/stream',  [Client\DocumentController::class, 'stream'])->name('documents.stream');
    Route::get('/documents/{resource}/download',[Client\DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{resource}/share',  [Client\DocumentController::class, 'share'])->name('documents.share');

    // Browse
    Route::get('/categories/{category:slug}', [Client\CategoryController::class, 'show'])->name('categories.show');
    Route::get('/tags/{tag:slug}',            [Client\TagController::class, 'show'])->name('tags.show');

    // Search
    Route::get('/search', [Client\SearchController::class, 'index'])->name('search');
    Route::get('/search/suggest', [Client\SearchController::class, 'suggest'])->name('search.suggest');

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

    Route::get('/notifications',                      [Client\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all',           [Client\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read',[Client\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}',    [Client\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Science & Technology
    Route::get('/science-tech',        [Client\ScienceTechController::class, 'index'])->name('science-tech.index');
    Route::get('/science-tech/{trend}', [Client\ScienceTechController::class, 'show'])->name('science-tech.show');

    // Basic Knowledge
    Route::get('/basic-knowledge',         [Client\BasicKnowledgeController::class, 'index'])->name('basic-knowledge.index');
    Route::get('/basic-knowledge/{trend}', [Client\BasicKnowledgeController::class, 'show'])->name('basic-knowledge.show');

    Route::get('/profile',                   [Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',                 [Client\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',                [Client\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/request-username', [Client\ProfileController::class, 'requestUsernameChange'])->name('profile.request-username-change');
    Route::post('/profile/request-deletion', [Client\ProfileController::class, 'requestDeletion'])->name('profile.request-deletion');
});

// Public share link (no auth required)
Route::get('/share/{token}', [Client\ShareController::class, 'show'])->name('share.show');

// ─── Admin Panel ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'active', 'role:admin,editor'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Documents — custom routes before resource() to avoid wildcard conflict
    Route::get('documents/trash',              [Admin\DocumentController::class, 'trash'])->name('documents.trash');
    Route::resource('documents', Admin\DocumentController::class)->except(['show']);
    Route::get('documents/{document}/access-log', [Admin\DocumentController::class, 'accessLog'])->name('documents.access-log');
    Route::patch('documents/{document}/restore',[Admin\DocumentController::class, 'restore'])->name('documents.restore')->withTrashed();
    Route::delete('documents/{document}/force', [Admin\DocumentController::class, 'forceDelete'])->name('documents.force-delete')->withTrashed();
    Route::patch('documents/{document}/lock',   [Admin\DocumentController::class, 'lock'])->name('documents.lock');
    Route::patch('documents/{document}/unlock', [Admin\DocumentController::class, 'unlock'])->name('documents.unlock');
    Route::patch('documents/{document}/approve',  [Admin\DocumentController::class, 'approve'])->name('documents.approve');
    Route::patch('documents/{document}/reject',   [Admin\DocumentController::class, 'reject'])->name('documents.reject');
    Route::patch('documents/{document}/archive',  [Admin\DocumentController::class, 'archive'])->name('documents.archive');
    Route::patch('documents/{document}/unarchive',[Admin\DocumentController::class, 'unarchive'])->name('documents.unarchive');

    // Bulk actions
    Route::post('documents/bulk/approve',          [Admin\DocumentController::class, 'bulkApprove'])->name('documents.bulk-approve');
    Route::post('documents/bulk/trash',            [Admin\DocumentController::class, 'bulkTrash'])->name('documents.bulk-trash');
    Route::post('documents/bulk/reject',           [Admin\DocumentController::class, 'bulkReject'])->name('documents.bulk-reject');
    Route::post('documents/bulk/assign-category',  [Admin\DocumentController::class, 'bulkAssignCategory'])->name('documents.bulk-assign-category');
    Route::post('documents/bulk/download',         [Admin\DocumentController::class, 'bulkDownload'])->name('documents.bulk-download');

    // Chunked upload
    Route::post('documents/upload/chunk',    [Admin\ChunkedUploadController::class, 'chunk'])->name('documents.upload.chunk');
    Route::post('documents/upload/assemble', [Admin\ChunkedUploadController::class, 'assemble'])->name('documents.upload.assemble');
    Route::post('documents/{resource}/upload/version', [Admin\ChunkedUploadController::class, 'assembleVersion'])->name('documents.upload.version');

    // Versions
    Route::post('documents/{resource}/versions',              [Admin\VersionController::class, 'store'])->name('versions.store');
    Route::patch('documents/{resource}/versions/{version}/restore', [Admin\VersionController::class, 'restore'])->name('versions.restore');

    // Categories & Tags
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('tags', Admin\TagController::class);
    Route::post('tags/merge',  [Admin\TagController::class, 'merge'])->name('tags.merge');

    // Science & Technology Trends
    Route::get('science-tech',                      [Admin\ScienceTechTrendController::class, 'index'])->name('science-tech.index');
    Route::get('science-tech/create',               [Admin\ScienceTechTrendController::class, 'create'])->name('science-tech.create');
    Route::post('science-tech',                     [Admin\ScienceTechTrendController::class, 'store'])->name('science-tech.store');
    Route::get('science-tech/{trend}/edit',         [Admin\ScienceTechTrendController::class, 'edit'])->name('science-tech.edit');
    Route::put('science-tech/{trend}',              [Admin\ScienceTechTrendController::class, 'update'])->name('science-tech.update');
    Route::delete('science-tech/{trend}',           [Admin\ScienceTechTrendController::class, 'destroy'])->name('science-tech.destroy');
    Route::get('science-tech/{trend}',              [Admin\ScienceTechTrendController::class, 'show'])->name('science-tech.show');

    // Basic Knowledge
    Route::get('basic-knowledge',                      [Admin\BasicKnowledgeTrendController::class, 'index'])->name('basic-knowledge.index');
    Route::get('basic-knowledge/create',               [Admin\BasicKnowledgeTrendController::class, 'create'])->name('basic-knowledge.create');
    Route::post('basic-knowledge',                     [Admin\BasicKnowledgeTrendController::class, 'store'])->name('basic-knowledge.store');
    Route::get('basic-knowledge/{trend}/edit',         [Admin\BasicKnowledgeTrendController::class, 'edit'])->name('basic-knowledge.edit');
    Route::put('basic-knowledge/{trend}',              [Admin\BasicKnowledgeTrendController::class, 'update'])->name('basic-knowledge.update');
    Route::delete('basic-knowledge/{trend}',           [Admin\BasicKnowledgeTrendController::class, 'destroy'])->name('basic-knowledge.destroy');
    Route::get('basic-knowledge/{trend}',              [Admin\BasicKnowledgeTrendController::class, 'show'])->name('basic-knowledge.show');

    // Problems & Reference Solutions — static routes before {problem} wildcard
    Route::get('problems',                    [Admin\ProblemController::class, 'index'])->name('problems.index');
    Route::get('problems/create',             [Admin\ProblemController::class, 'create'])->name('problems.create');
    Route::post('problems',                   [Admin\ProblemController::class, 'store'])->name('problems.store');
    Route::get('problems/{problem}/edit',     [Admin\ProblemController::class, 'edit'])->name('problems.edit');
    Route::put('problems/{problem}',          [Admin\ProblemController::class, 'update'])->name('problems.update');
    Route::delete('problems/{problem}',       [Admin\ProblemController::class, 'destroy'])->name('problems.destroy');
    Route::get('problems/{problem}',          [Admin\ProblemController::class, 'show'])->name('problems.show');

    // Roles (read-only permission matrix)
    Route::get('roles', fn() => view('admin.roles.index'))->name('roles.index');

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

    // Document comparison
    Route::get('compare',          [Admin\CompareController::class, 'index'])->name('compare.index');
    Route::get('compare/{a}/{b}',  [Admin\CompareController::class, 'show'])->name('compare.show');

    // Logs & Analytics
    Route::get('audit-logs',        [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [Admin\AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('activity-logs',        [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/export', [Admin\ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::get('jobs',          [Admin\JobMonitorController::class, 'index'])->name('jobs.index');
    Route::get('storage',       [Admin\StorageController::class, 'index'])->name('storage.index');
    Route::get('search',              [Admin\SearchIndexController::class, 'index'])->name('search.index');
    Route::post('search/reindex',     [Admin\SearchIndexController::class, 'reindex'])->name('search.reindex');
    Route::post('search/build-tfidf', [Admin\SearchIndexController::class, 'buildTfidf'])->name('search.build-tfidf');

    // Job retry
    Route::post('jobs/{id}/retry',  [Admin\JobMonitorController::class, 'retry'])->name('jobs.retry');
    Route::post('jobs/retry-all',   [Admin\JobMonitorController::class, 'retryAll'])->name('jobs.retry-all');

    // Notifications (index accessible by editors too)
    Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/broadcast', [Admin\NotificationController::class, 'broadcast'])->name('notifications.broadcast');

    // Account requests (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('account-requests',                                     [Admin\AccountRequestController::class, 'index'])->name('account-requests.index');
        Route::patch('account-requests/{accountRequest}/approve',          [Admin\AccountRequestController::class, 'approve'])->name('account-requests.approve');
        Route::patch('account-requests/{accountRequest}/reject',           [Admin\AccountRequestController::class, 'reject'])->name('account-requests.reject');
        Route::post('account-requests/{accountRequest}/generate-reset-link', [Admin\AccountRequestController::class, 'generateResetLink'])->name('account-requests.generate-reset-link');
    });

    // Settings (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('settings',  [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings',  [Admin\SettingController::class, 'update'])->name('settings.update');
    });

    // Documentation
    Route::get('help/{path?}', function ($path = 'index.html') {
        $docRoot = realpath(base_path('documentation'));
        $file    = realpath(base_path('documentation/' . ltrim($path, '/')));
        if (!$file || !str_starts_with($file, $docRoot)) {
            abort(404);
        }
        return response()->file($file);
    })->where('path', '.*')->name('help');
});

require __DIR__.'/auth.php';
