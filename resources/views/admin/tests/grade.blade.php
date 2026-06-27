@extends('layouts.admin')
@section('title', 'Grade Submission')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">{{ $invite->test->title }}</p>
            <h1 class="text-xl font-bold text-gray-800">{{ $invite->candidate_name }}</h1>
            @if($invite->candidate_email)
                <p class="text-sm text-gray-500">{{ $invite->candidate_email }}</p>
            @endif
        </div>
        <div class="text-right text-sm text-gray-500">
            @if($invite->submitted_at)
                <div>Submitted {{ $invite->submitted_at->format('M j, Y g:ia') }}</div>
            @endif
            @if($invite->status === 'graded')
                <div class="mt-1 font-semibold text-green-700">{{ $invite->total_score }} / {{ $invite->max_score }} pts</div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.test-invites.grade.store', $invite) }}" class="space-y-5">
        @csrf @method('PUT')

        @foreach($invite->test->problems as $problem)
            @php $answer = $answersByProblem->get($problem->id); @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-400 font-mono mr-1">#{{ $problem->pivot->order_index }}</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $problem->title }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize ml-2
                            {{ match($problem->difficulty) { 'easy' => 'bg-green-100 text-green-700', 'medium' => 'bg-yellow-100 text-yellow-700', 'hard' => 'bg-red-100 text-red-700' } }}">
                            {{ $problem->difficulty }}
                        </span>
                    </div>
                </div>
                <div class="px-5 py-3 text-sm text-gray-600 border-b border-gray-50">{{ $problem->description }}</div>

                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                    <div class="p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1.5">Candidate's Answer</div>
                        <pre class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs font-mono overflow-x-auto whitespace-pre-wrap" style="max-height: 320px; overflow-y: auto;">{{ $answer && $answer->code !== '' ? $answer->code : '(no answer submitted)' }}</pre>
                    </div>
                    <div class="p-4">
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1.5">Reference Solution</div>
                        <pre class="bg-gray-900 text-gray-100 rounded-lg p-3 text-xs font-mono overflow-x-auto whitespace-pre-wrap" style="max-height: 320px; overflow-y: auto;">{{ $problem->solution_code }}</pre>
                    </div>
                </div>

                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap items-start gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Score (max {{ $problem->pivot->points }})</label>
                        <input type="number" name="scores[{{ $problem->id }}]"
                               value="{{ old('scores.'.$problem->id, $answer->score ?? 0) }}"
                               min="0" max="{{ $problem->pivot->points }}"
                               class="w-28 border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                    </div>
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Feedback</label>
                        <textarea name="feedback[{{ $problem->id }}]" rows="2"
                               placeholder="Optional comments for this answer, or auto-grading results"
                               class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-mono resize-y">{{ old('feedback.'.$problem->id, $answer->feedback ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Save Grading
            </button>
            <a href="{{ route('admin.tests.show', $invite->test) }}" class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
