@extends('layouts.admin')
@section('title', $test->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                    {{ match($test->status) { 'active' => 'bg-green-100 text-green-700', 'draft' => 'bg-gray-100 text-gray-600', 'archived' => 'bg-yellow-100 text-yellow-700' } }}">
                    {{ $test->status }}
                </span>
                <span class="text-xs text-gray-400">{{ $test->time_limit_minutes }} min · {{ $test->problems->count() }} problems</span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">{{ $test->title }}</h1>
            @if($test->description)
                <p class="text-sm text-gray-500 mt-1 max-w-2xl">{{ $test->description }}</p>
            @endif
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.tests.edit', $test) }}"
               class="text-sm px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Edit</a>
            <a href="{{ route('admin.tests.index') }}"
               class="text-sm px-3 py-1.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">← Tests</a>
        </div>
    </div>

    {{-- Problems --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 text-sm font-semibold text-gray-700">Problems</div>
        <ol class="divide-y divide-gray-50">
            @foreach($test->problems as $problem)
            <li class="px-5 py-2.5 flex items-center gap-3 text-sm">
                <span class="text-xs text-gray-400 font-mono w-6">{{ $problem->pivot->order_index }}</span>
                <span class="flex-1 text-gray-800">{{ $problem->title }}</span>
                <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                    {{ match($problem->difficulty) { 'easy' => 'bg-green-100 text-green-700', 'medium' => 'bg-yellow-100 text-yellow-700', 'hard' => 'bg-red-100 text-red-700' } }}">
                    {{ $problem->difficulty }}
                </span>
                <span class="text-xs text-gray-400 w-20 text-right">{{ $problem->pivot->points }} pts</span>
            </li>
            @endforeach
        </ol>
    </div>

    {{-- Invite candidate --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="text-sm font-semibold text-gray-700 mb-3">Invite a Candidate</div>
        @if($test->status !== 'active')
            <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                This test is {{ $test->status }}. Set it to <strong>active</strong> before sending invites.
            </p>
        @else
            <form method="POST" action="{{ route('admin.tests.invites.store', $test) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Candidate Name</label>
                    <input type="text" name="candidate_name" value="{{ old('candidate_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('candidate_name') border-red-400 @enderror">
                    @error('candidate_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Candidate Email <span class="text-gray-400">(optional)</span></label>
                    <input type="email" name="candidate_email" value="{{ old('candidate_email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('candidate_email') border-red-400 @enderror">
                    @error('candidate_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Create Invite Link
                </button>
            </form>
        @endif
    </div>

    {{-- Invites --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ copied: null }">
        <div class="px-5 py-3 border-b border-gray-100 text-sm font-semibold text-gray-700">Candidates ({{ $test->invites->count() }})</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Candidate</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Score</th>
                    <th class="px-4 py-2 text-left">Link</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($test->invites as $invite)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5">
                        <div class="text-gray-800 font-medium">{{ $invite->candidate_name }}</div>
                        @if($invite->candidate_email)
                            <div class="text-xs text-gray-400">{{ $invite->candidate_email }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($invite->status) {
                                'pending'   => 'bg-gray-100 text-gray-600',
                                'started'   => 'bg-blue-100 text-blue-700',
                                'submitted' => 'bg-amber-100 text-amber-700',
                                'graded'    => 'bg-green-100 text-green-700',
                            } }}">
                            {{ $invite->isExpired() ? 'expired (no submission)' : $invite->status }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-gray-600">
                        @if(!is_null($invite->total_score))
                            {{ $invite->total_score }} / {{ $invite->max_score }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ route('test.show', $invite->token) }}'); copied = {{ $invite->id }}; setTimeout(() => copied = null, 1500)"
                                class="text-xs text-blue-600 hover:underline">
                            <span x-show="copied !== {{ $invite->id }}">Copy Link</span>
                            <span x-show="copied === {{ $invite->id }}" x-cloak>Copied!</span>
                        </button>
                    </td>
                    <td class="px-4 py-2.5 text-right">
                        @if(in_array($invite->status, ['submitted', 'graded']))
                            <a href="{{ route('admin.test-invites.grade', $invite) }}" class="text-xs text-gray-600 hover:text-blue-600 mr-3">
                                {{ $invite->status === 'graded' ? 'Review' : 'Grade' }}
                            </a>
                        @endif
                        <form action="{{ route('admin.tests.invites.destroy', [$test, $invite]) }}" method="POST" class="inline"
                              onsubmit="return confirm('Revoke invite for {{ $invite->candidate_name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Revoke</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No candidates invited yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
