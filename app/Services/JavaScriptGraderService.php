<?php

namespace App\Services;

use App\Models\Problem;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class JavaScriptGraderService
{
    /**
     * Run a candidate's JS submission against a problem's stored test cases
     * in a sandboxed Node subprocess. Returns null when the problem has no
     * test cases defined (not auto-gradable — falls back to manual grading).
     */
    public function grade(Problem $problem, string $code): ?array
    {
        $cases = $problem->test_cases ?? [];

        if (empty($cases) || !$problem->function_name) {
            return null;
        }

        $dir = storage_path('app/sandbox/' . Str::random(20));
        File::ensureDirectoryExists($dir);
        $payloadPath = $dir . '/payload.json';

        try {
            file_put_contents($payloadPath, json_encode([
                'code'         => $code,
                'functionName' => $problem->function_name,
                'cases'        => $cases,
            ]));

            $result = Process::timeout(10)
                ->env($this->subprocessEnv())
                ->run($this->buildCommand($payloadPath));

            $output = json_decode($result->output(), true);

            if (!is_array($output)) {
                return [
                    'error'   => 'Sandbox execution failed to produce a result.',
                    'passed'  => 0,
                    'total'   => count($cases),
                    'results' => [],
                ];
            }

            if (!empty($output['fatal'])) {
                $output['error'] = $output['fatal'];
            }

            return $output;
        } catch (ProcessTimedOutException) {
            return [
                'error'   => 'Execution timed out.',
                'passed'  => 0,
                'total'   => count($cases),
                'results' => [],
            ];
        } finally {
            File::deleteDirectory($dir);
        }
    }

    /**
     * Node's crypto init needs a few core OS env vars (SystemRoot on Windows,
     * PATH everywhere) that Symfony's Process strips when PHP runs under a
     * non-CLI SAPI such as `php artisan serve`'s built-in dev server.
     */
    private function subprocessEnv(): array
    {
        $env = [];

        foreach (['PATH', 'SystemRoot', 'windir', 'TEMP', 'TMP'] as $key) {
            $value = getenv($key);
            if ($value !== false) {
                $env[$key] = $value;
            }
        }

        return $env;
    }

    private function buildCommand(string $payloadPath): array|string
    {
        $runner = base_path('sandbox/js-runner.cjs');

        if (PHP_OS_FAMILY === 'Windows') {
            return ['node', $runner, $payloadPath];
        }

        // Linux/production: cap CPU time and virtual memory at the OS level
        // in addition to the wall-clock Process::timeout() above.
        return [
            'bash',
            '-c',
            'ulimit -t 10 -v 262144; exec node ' . escapeshellarg($runner) . ' ' . escapeshellarg($payloadPath),
        ];
    }
}
