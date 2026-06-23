<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Permanently delete trashed documents older than trash_retention_days (default 30)
Schedule::command('dms:prune-trash')->daily()->at('02:00');

// Delete share links expired more than 7 days ago
Schedule::command('dms:prune-shares')->daily()->at('02:15');

// Delete expired Sanctum API tokens
Schedule::command('dms:prune-tokens')->daily()->at('02:30');

// Auto-archive documents whose expires_at has passed
Schedule::command('dms:archive-expired')->hourly();

// Prune old job batches
Schedule::command('queue:prune-batches --hours=48')->weekly();
