<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\TestInvite;
use App\Services\JavaScriptGraderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestSessionController extends Controller
{
    public function __construct(private JavaScriptGraderService $grader) {}

    public function show(string $token): View
    {
        $invite = TestInvite::with('test.problems')->where('token', $token)->firstOrFail();

        if ($invite->status === 'pending' && !$invite->test->isActive()) {
            return view('test.show', ['state' => 'unavailable', 'invite' => $invite]);
        }

        if ($invite->status === 'started' && $invite->isExpired()) {
            $this->finalizeSubmission($invite, []);
        }

        $state = match ($invite->status) {
            'pending'             => 'start',
            'started'             => 'take',
            'submitted', 'graded' => 'submitted',
        };

        $remainingSeconds = $state === 'take'
            ? max(0, now()->diffInSeconds($invite->expires_at, false))
            : null;

        $existingAnswers = $state === 'take'
            ? $invite->answers()->pluck('code', 'problem_id')->all()
            : [];

        return view('test.show', [
            'state'            => $state,
            'invite'           => $invite,
            'remainingSeconds' => $remainingSeconds,
            'existingAnswers'  => $existingAnswers,
        ]);
    }

    public function start(string $token): RedirectResponse
    {
        $invite = TestInvite::with('test')->where('token', $token)->firstOrFail();

        if ($invite->status === 'pending' && $invite->test->isActive()) {
            $invite->update([
                'status'     => 'started',
                'started_at' => now(),
                'expires_at' => now()->addMinutes($invite->test->time_limit_minutes),
            ]);
        }

        return redirect()->route('test.show', $token);
    }

    public function submit(Request $request, string $token): RedirectResponse
    {
        $invite = TestInvite::with('test.problems')->where('token', $token)->firstOrFail();

        if ($invite->status === 'started') {
            $this->finalizeSubmission($invite, $request->input('answers', []));
        }

        return redirect()->route('test.show', $token);
    }

    private function finalizeSubmission(TestInvite $invite, array $answers): void
    {
        foreach ($invite->test->problems as $problem) {
            $code = $answers[$problem->id] ?? '';

            [$score, $feedback] = $code !== ''
                ? $this->autoGrade($problem, $code)
                : [null, null];

            $invite->answers()->updateOrCreate(
                ['problem_id' => $problem->id],
                ['code' => $code, 'score' => $score, 'feedback' => $feedback]
            );
        }

        $invite->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Auto-grade against the problem's stored test cases, if any. Returns
     * [null, null] when the problem isn't auto-gradable or grading fails —
     * the admin grade screen falls back to fully manual scoring in that case.
     */
    private function autoGrade(Problem $problem, string $code): array
    {
        try {
            $result = $this->grader->grade($problem, $code);
        } catch (\Throwable $e) {
            report($e);
            return [null, null];
        }

        if (!$result || !empty($result['error'])) {
            return [null, null];
        }

        $points = (int) $problem->pivot->points;
        $total = $result['total'];
        $passed = $result['passed'];

        $score = $total > 0 ? (int) round($points * $passed / $total) : null;

        $feedback = "{$passed}/{$total} test cases passed.";
        foreach ($result['results'] as $i => $case) {
            if (!$case['pass']) {
                $feedback .= "\nTest " . ($i + 1) . ': expected ' . json_encode($case['expected'])
                    . ', got ' . json_encode($case['actual'])
                    . (!empty($case['error']) ? " ({$case['error']})" : '');
            }
        }

        return [$score, $feedback];
    }
}
