<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImportViteDocs extends Command
{
    protected $signature   = 'docs:import-vite {--branch=main : GitHub branch to download}';
    protected $description = 'Download Vite documentation markdown files from GitHub';

    public function handle(): int
    {
        $branch = $this->option('branch');
        $dest   = storage_path("app/private/documents/vite-docs/{$branch}");
        $zipUrl = "https://github.com/vitejs/vite/archive/refs/heads/{$branch}.zip";
        $tmpZip = sys_get_temp_dir() . "/vite-docs-{$branch}.zip";

        $this->info("Downloading Vite repo ({$branch}) from GitHub…");

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

        // Detect root prefix e.g. "vite-main/"
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

        // Docs live under docs/ — skip .vitepress, images, public, _data, blog subdir
        $docsPrefix = $prefix . 'docs/';
        $skip       = ['.vitepress/', 'images/', 'public/', '_data/', 'blog/'];
        $count      = 0;
        $bar        = $this->output->createProgressBar($zip->numFiles);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $bar->advance();

            if (str_ends_with($name, '/') || ! str_ends_with($name, '.md')) {
                continue;
            }

            if (! str_starts_with($name, $docsPrefix)) {
                continue;
            }

            $relative = substr($name, strlen($docsPrefix));
            if ($relative === '') {
                continue;
            }

            // Skip non-doc subdirectories
            $skipThis = false;
            foreach ($skip as $s) {
                if (str_starts_with($relative, $s)) {
                    $skipThis = true;
                    break;
                }
            }
            if ($skipThis) {
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
            $this->warn('No markdown files found in docs/.');
            return self::FAILURE;
        }

        $this->info("✓ Imported {$count} file(s) to storage/app/private/documents/vite-docs/{$branch}");
        $this->line('');
        $this->line('  Next: <info>php artisan docs:build-vite-offline --branch=' . $branch . '</info>');

        return self::SUCCESS;
    }
}
