<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImportLaravelDocs extends Command
{
    protected $signature   = 'docs:import-laravel {--branch=12.x : The docs branch to download}';
    protected $description = 'Download Laravel documentation markdown files from GitHub';

    public function handle(): int
    {
        $branch = $this->option('branch');
        $dest   = storage_path("app/private/documents/laravel-docs/{$branch}");
        $zipUrl = "https://github.com/laravel/docs/archive/refs/heads/{$branch}.zip";

        $this->info("Downloading Laravel {$branch} docs from GitHub…");

        $response = Http::timeout(120)
            ->withoutVerifying()
            ->withHeaders(['User-Agent' => 'DocManager/1.0'])
            ->get($zipUrl);

        if (! $response->ok()) {
            $this->error("Download failed (HTTP {$response->status()}): {$zipUrl}");
            return self::FAILURE;
        }

        $tmpZip = sys_get_temp_dir() . "/laravel-docs-{$branch}.zip";
        file_put_contents($tmpZip, $response->body());
        $this->line('  Downloaded ' . number_format(strlen($response->body()) / 1024, 1) . ' KB');

        $zip = new ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            $this->error('Failed to open the downloaded ZIP archive.');
            @unlink($tmpZip);
            return self::FAILURE;
        }

        @mkdir($dest, 0755, true);

        // GitHub ZIP puts files under "docs-{branch}/" — handle both "docs-12.x/" and "docs-main/" etc.
        $prefix = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '/') && substr_count($name, '/') === 1) {
                $prefix = $name;
                break;
            }
        }

        if (! $prefix) {
            $this->error('Could not determine ZIP folder prefix.');
            $zip->close();
            @unlink($tmpZip);
            return self::FAILURE;
        }

        $count = 0;
        $bar   = $this->output->createProgressBar($zip->numFiles);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $bar->advance();

            if (! str_starts_with($name, $prefix)) {
                continue;
            }

            $relative = substr($name, strlen($prefix));
            if ($relative === '' || str_ends_with($relative, '/')) {
                continue;
            }

            if (! str_ends_with($relative, '.md') && $relative !== 'docs.json') {
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

        $this->info("✓ Imported {$count} file(s) to storage/app/private/documents/laravel-docs/{$branch}");
        $this->line('');
        $this->line('  Next: <info>php artisan docs:build-laravel-offline --branch=' . $branch . '</info>');

        return self::SUCCESS;
    }
}
