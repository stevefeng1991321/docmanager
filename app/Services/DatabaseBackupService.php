<?php

namespace App\Services;

use RuntimeException;
use InvalidArgumentException;

class DatabaseBackupService
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function create(): string
    {
        $cfg = $this->mysqlConfig();

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $bin = $this->findExecutable('mysqldump');
        $cmd = $this->buildCmd($bin, $cfg, ['--single-transaction', '--routines', '--triggers', $cfg['database']]);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $filepath, 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException('Could not launch mysqldump process.');
        }

        fclose($pipes[0]);
        $stderr   = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
            @unlink($filepath);
            throw new RuntimeException('mysqldump failed' . ($stderr ? ': ' . trim($stderr) : '.'));
        }

        return $filename;
    }

    public function list(): array
    {
        $files   = glob($this->backupDir . DIRECTORY_SEPARATOR . 'backup_*.sql') ?: [];
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename'   => basename($file),
                'size'       => filesize($file),
                'created_at' => filemtime($file),
            ];
        }
        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);
        return $backups;
    }

    public function safePath(string $filename): string
    {
        if (!preg_match('/^backup_[\d_-]+\.sql$/', $filename)) {
            throw new InvalidArgumentException('Invalid backup filename.');
        }
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            throw new InvalidArgumentException('Backup file not found.');
        }
        return $path;
    }

    public function delete(string $filename): void
    {
        $path = $this->safePath($filename);
        unlink($path);
    }

    public function restore(string $filepath): void
    {
        $cfg = $this->mysqlConfig();
        $bin = $this->findExecutable('mysql');
        $cmd = $this->buildCmd($bin, $cfg, [$cfg['database']]);

        $descriptors = [
            0 => ['file', $filepath, 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new RuntimeException('Could not launch mysql process.');
        }

        fclose($pipes[1]);
        $stderr   = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException('Restore failed' . ($stderr ? ': ' . trim($stderr) : '.'));
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildCmd(string $bin, array $cfg, array $extra): string
    {
        $parts   = [escapeshellarg($bin)];
        $parts[] = '--host=' . escapeshellarg($cfg['host']);
        $parts[] = '--port=' . escapeshellarg($cfg['port']);
        $parts[] = '--user=' . escapeshellarg($cfg['username']);
        if ($cfg['password'] !== '') {
            $parts[] = '--password=' . escapeshellarg($cfg['password']);
        }
        foreach ($extra as $arg) {
            $parts[] = escapeshellarg($arg);
        }
        return implode(' ', $parts);
    }

    private function findExecutable(string $name): string
    {
        // Check PATH first using where.exe (Windows) or which (Unix)
        $finder = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        exec("{$finder} {$name} 2>&1", $out, $code);
        if ($code === 0 && !empty($out[0]) && trim($out[0]) !== '') {
            return trim($out[0]);
        }

        // Windows fallback: scan common MySQL / MariaDB install locations
        if (PHP_OS_FAMILY === 'Windows') {
            $patterns = [
                'C:\\Program Files\\MySQL\\MySQL Server *\\bin\\' . $name . '.exe',
                'C:\\Program Files (x86)\\MySQL\\MySQL Server *\\bin\\' . $name . '.exe',
                'C:\\Program Files\\MariaDB *\\bin\\' . $name . '.exe',
                'C:\\xampp\\mysql\\bin\\' . $name . '.exe',
                'C:\\wamp64\\bin\\mysql\\mysql*\\bin\\' . $name . '.exe',
                'C:\\wamp\\bin\\mysql\\mysql*\\bin\\' . $name . '.exe',
            ];
            foreach ($patterns as $pattern) {
                $matches = glob($pattern);
                if (!empty($matches)) {
                    return $matches[0];
                }
                // non-glob exact path
                if (!str_contains($pattern, '*') && is_file($pattern)) {
                    return $pattern;
                }
            }
        }

        throw new RuntimeException(
            "Could not find \"{$name}\". Add MySQL's bin directory to the system PATH " .
            "(e.g. C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin)."
        );
    }

    private function mysqlConfig(): array
    {
        $conn = config('database.default');
        $cfg  = config("database.connections.{$conn}");

        if (($cfg['driver'] ?? '') !== 'mysql') {
            throw new RuntimeException('Database backup only supports MySQL/MariaDB connections.');
        }

        return [
            'host'     => $cfg['host']     ?? '127.0.0.1',
            'port'     => (string)($cfg['port'] ?? 3306),
            'database' => $cfg['database'] ?? '',
            'username' => $cfg['username'] ?? 'root',
            'password' => $cfg['password'] ?? '',
        ];
    }
}
