<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestInviteController extends Controller
{
    public function store(Request $request, Test $test)
    {
        $validated = $request->validate([
            'candidate_name'  => ['required', 'string', 'max:255'],
            'candidate_email' => ['nullable', 'email', 'max:255'],
        ]);

        do {
            $token = Str::random(48);
        } while (TestInvite::where('token', $token)->exists());

        $test->invites()->create([
            'candidate_name'  => $validated['candidate_name'],
            'candidate_email' => $validated['candidate_email'] ?? null,
            'token'           => $token,
            'status'          => 'pending',
            'created_by'      => $request->user()->id,
        ]);

        return redirect()->route('admin.tests.show', $test)
            ->with('message', 'Invite created. Copy the link below and share it with the candidate.');
    }

    public function destroy(Test $test, TestInvite $invite)
    {
        abort_unless($invite->test_id === $test->id, 404);

        $invite->delete();

        return redirect()->route('admin.tests.show', $test)
            ->with('message', 'Invite revoked.');
    }

    public function grade(TestInvite $invite)
    {
        $invite->load(['test.problems', 'answers']);

        $answersByProblem = $invite->answers->keyBy('problem_id');

        return view('admin.tests.grade', compact('invite', 'answersByProblem'));
    }

    public function storeGrade(Request $request, TestInvite $invite)
    {
        $invite->load('test.problems');

        $validated = $request->validate([
            'scores'   => ['required', 'array'],
            'scores.*' => ['nullable', 'integer', 'min:0'],
            'feedback' => ['nullable', 'array'],
            'feedback.*' => ['nullable', 'string'],
        ]);

        $totalScore = 0;
        $maxScore   = 0;

        foreach ($invite->test->problems as $problem) {
            $points = (int) $problem->pivot->points;
            $maxScore += $points;

            $score = (int) ($validated['scores'][$problem->id] ?? 0);
            $score = max(0, min($score, $points));
            $totalScore += $score;

            $invite->answers()->updateOrCreate(
                ['problem_id' => $problem->id],
                [
                    'score'    => $score,
                    'feedback' => $validated['feedback'][$problem->id] ?? null,
                ]
            );
        }

        $invite->update([
            'total_score' => $totalScore,
            'max_score'   => $maxScore,
            'status'      => 'graded',
            'graded_at'   => now(),
            'graded_by'   => $request->user()->id,
        ]);

        return redirect()->route('admin.tests.show', $invite->test)
            ->with('message', 'Grading saved.');
    }
}
