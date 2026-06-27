<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImportFlowbiteDocs extends Command
{
    protected $signature   = 'docs:import-flowbite {--branch=main : GitHub branch to download}';
    protected $description = 'Download Flowbite documentation markdown files from GitHub';

    public function handle(): int
    {
        $branch = $this->option('branch');
        $dest   = storage_path("app/private/documents/flowbite-docs/{$branch}");
        $zipUrl = "https://github.com/themesberg/flowbite/archive/refs/heads/{$branch}.zip";
        $tmpZip = sys_get_temp_dir() . "/flowbite-docs-{$branch}.zip";

        $this->info("Downloading Flowbite repo ({$branch}) from GitHub…");

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

        // Detect root prefix e.g. "flowbite-main/"
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

        // Docs live under content/{section}/*.md
        $contentPrefix = $prefix . 'content/';
        $count         = 0;
        $bar           = $this->output->createProgressBar($zip->numFiles);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $bar->advance();

            if (str_ends_with($name, '/') || ! str_ends_with($name, '.md')) {
                continue;
            }

            if (! str_starts_with($name, $contentPrefix)) {
                continue;
            }

            $relative = substr($name, strlen($contentPrefix));
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
            $this->warn('No markdown files found in content/.');
            return self::FAILURE;
        }

        $this->info("✓ Imported {$count} file(s) to storage/app/private/documents/flowbite-docs/{$branch}");
        $this->line('');
        $this->line('  Next: <info>php artisan docs:build-flowbite-offline --branch=' . $branch . '</info>');

        return self::SUCCESS;
    }
}
