<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImportAlpineDocs extends Command
{
    protected $signature   = 'docs:import-alpine {--branch=main : GitHub branch to download}';
    protected $description = 'Download Alpine.js documentation markdown files from GitHub';

    public function handle(): int
    {
        $branch  = $this->option('branch');
        $dest    = storage_path("app/private/documents/alpine-docs/{$branch}");
        $zipUrl  = "https://github.com/alpinejs/alpine/archive/refs/heads/{$branch}.zip";
        $tmpZip  = sys_get_temp_dir() . "/alpine-docs-{$branch}.zip";

        // Docs live at: packages/docs/src/en/  (flat .md + subdirs)
        $docsPrefix = 'packages/docs/src/en/';

        $this->info("Downloading Alpine.js repo ({$branch}) from GitHub…");

        Http::timeout(180)
            ->withoutVerifying()
            ->withHeaders(['User-Agent' => 'DocManager/1.0'])
            ->withOptions(['sink' => $tmpZip])
            ->get($zipUrl);

        if (! file_exists($tmpZip) || filesize($tmpZip) === 0) {
            $this->error('Download failed or produced an empty file.');
            return self::FAILURE;
        }

        $this->line('  Downloaded ' . number_format(filesize($tmpZip) / 1024 / 1024, 1) . ' MB');

        $zip = new ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            $this->error('Failed to open the downloaded ZIP archive.');
            @unlink($tmpZip);
            return self::FAILURE;
        }

        // Detect root prefix: "alpine-main/"
        $prefix = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '/') && substr_count($name, '/') === 1) {
                $prefix = $name;
                break;
            }
        }

        if (! $prefix) {
            $this->error('Could not determine ZIP root folder.');
            $zip->close();
            @unlink($tmpZip);
            return self::FAILURE;
        }

        $fullDocsPrefix = $prefix . $docsPrefix;
        $count          = 0;
        $bar            = $this->output->createProgressBar($zip->numFiles);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $bar->advance();

            if (str_ends_with($name, '/') || ! str_ends_with($name, '.md')) {
                continue;
            }

            if (! str_starts_with($name, $fullDocsPrefix)) {
                continue;
            }

            // relative path keeps the sub-directory: "essentials/installation.md"
            $relative = substr($name, strlen($fullDocsPrefix));
            if ($relative === '') {
                continue;
            }

            $target = "{$dest}/{$relative}";
            @mkdir(dirname($target), 0755, true);
            file_put_contents($target, $zip->getFromIndex($i));
            $count++;
        }

        $zip->close();
        @unlink($tmpZip);
        $bar->finish();
        $this->newLine();

        if ($count === 0) {
            $this->warn('No markdown files found at the expected path.');
            return self::FAILURE;
        }

        $this->info("✓ Imported {$count} file(s) to storage/app/private/documents/alpine-docs/{$branch}");
        $this->line('');
        $this->line('  Next: <info>php artisan docs:build-alpine-offline --branch=' . $branch . '</info>');

        return self::SUCCESS;
    }
}
