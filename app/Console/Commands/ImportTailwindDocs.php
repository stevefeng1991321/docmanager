<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImportTailwindDocs extends Command
{
    protected $signature   = 'docs:import-tailwind {--branch=main : GitHub branch (main = v4)}';
    protected $description = 'Download Tailwind CSS documentation MDX files from GitHub';

    public function handle(): int
    {
        $branch = $this->option('branch');
        $dest   = storage_path("app/private/documents/tailwind-docs/{$branch}");
        $zipUrl = "https://github.com/tailwindlabs/tailwindcss.com/archive/refs/heads/{$branch}.zip";

        $this->info("Downloading Tailwind CSS repo ({$branch}) from GitHub…");
        $this->line("  Streaming to disk — the full repo ZIP may be 100+ MB, please wait.");

        $tmpZip = sys_get_temp_dir() . "/tailwind-docs-{$branch}.zip";

        // Stream response directly to disk to avoid exhausting PHP memory
        $response = Http::timeout(300)
            ->withoutVerifying()
            ->withHeaders(['User-Agent' => 'DocManager/1.0'])
            ->withOptions(['sink' => $tmpZip])
            ->get($zipUrl);

        if (! $response->ok() || ! file_exists($tmpZip) || filesize($tmpZip) === 0) {
            $this->error("Download failed (HTTP {$response->status()}): {$zipUrl}");
            @unlink($tmpZip);
            return self::FAILURE;
        }

        $sizeMb = number_format(filesize($tmpZip) / 1024 / 1024, 1);
        $this->line("  Downloaded {$sizeMb} MB");

        $zip = new ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            $this->error('Failed to open the downloaded ZIP archive.');
            @unlink($tmpZip);
            return self::FAILURE;
        }

        @mkdir($dest, 0755, true);

        // GitHub ZIP uses "tailwindcss.com-{branch}/" as the root folder
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

        // Try v4 (src/docs/) then v3 (src/pages/docs/)
        $docsPaths = [
            $prefix . 'src/docs/',
            $prefix . 'src/pages/docs/',
        ];

        $count = 0;
        $bar   = $this->output->createProgressBar($zip->numFiles);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $bar->advance();

            if (str_ends_with($name, '/') || ! str_ends_with($name, '.mdx')) {
                continue;
            }

            $matched = null;
            foreach ($docsPaths as $path) {
                if (str_starts_with($name, $path)) {
                    $matched = $path;
                    break;
                }
            }

            if (! $matched) {
                continue;
            }

            $relative = substr($name, strlen($matched));

            // Flat files only — skip sub-directories
            if ($relative === '' || str_contains($relative, '/')) {
                continue;
            }

            file_put_contents("{$dest}/{$relative}", $zip->getFromIndex($i));
            $count++;
        }

        $zip->close();
        @unlink($tmpZip);
        $bar->finish();
        $this->newLine();

        if ($count === 0) {
            $this->warn('No MDX files found in the expected docs paths.');
            $this->line('  Tried: ' . implode(', ', array_map(fn($p) => ltrim(substr($p, strlen($prefix)), '/'), $docsPaths)));
            return self::FAILURE;
        }

        $this->info("✓ Imported {$count} file(s) to storage/app/private/documents/tailwind-docs/{$branch}");
        $this->line('');
        $this->line('  Next: <info>php artisan docs:build-tailwind-offline --branch=' . $branch . '</info>');

        return self::SUCCESS;
    }
}
