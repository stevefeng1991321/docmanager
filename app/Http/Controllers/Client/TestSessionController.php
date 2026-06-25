<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TestInvite;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestSessionController extends Controller
{
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
            $invite->answers()->updateOrCreate(
                ['problem_id' => $problem->id],
                ['code' => $answers[$problem->id] ?? '']
            );
        }

        $invite->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);
    }
}
