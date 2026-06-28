<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $invite->test->title }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">

@if($state === 'unavailable')
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md text-center bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('tests.empty') }}</h1>
            <p class="text-sm text-gray-500">{{ __('tests.empty_sub') }}</p>
        </div>
    </div>

@elseif($state === 'start')
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8 space-y-5">
            <div>
                <p class="text-sm text-gray-500">Hi {{ $invite->candidate_name }}, welcome to</p>
                <h1 class="text-2xl font-bold text-gray-900">{{ $invite->test->title }}</h1>
            </div>

            @if($invite->test->description)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $invite->test->description }}</p>
            @endif

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-gray-400 text-xs">Problems</div>
                    <div class="font-semibold text-gray-800">{{ $invite->test->problems->count() }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-gray-400 text-xs">{{ __('tests.time_limit') }}</div>
                    <div class="font-semibold text-gray-800">{{ $invite->test->time_limit_minutes }} minutes</div>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-lg p-3">
                Once you click Start, the timer begins immediately and cannot be paused. Make sure you're ready before continuing.
            </div>

            <form method="POST" action="{{ route('test.start', $invite->token) }}">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    {{ __('tests.start_test') }}
                </button>
            </form>
        </div>
    </div>

@elseif($state === 'take')
    <div x-data="testTimer({{ $remainingSeconds }})" x-init="start()" class="min-h-screen">
        {{-- Sticky timer header --}}
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-base font-semibold text-gray-800">{{ $invite->test->title }}</h1>
                <p class="text-xs text-gray-400">{{ $invite->candidate_name }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-400">{{ __('tests.time_remaining') }}</div>
                <div class="text-lg font-bold font-mono" :class="seconds <= 60 ? 'text-red-600' : 'text-gray-800'" x-text="formatted"></div>
            </div>
        </div>

        <form id="test-form" method="POST" action="{{ route('test.submit', $invite->token) }}" class="max-w-3xl mx-auto p-4 sm:p-6 space-y-5">
            @csrf

            @foreach($invite->test->problems as $problem)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-mono">#{{ $problem->pivot->order_index }}</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $problem->title }}</span>
                    <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                        {{ match($problem->difficulty) { 'easy' => 'bg-green-100 text-green-700', 'medium' => 'bg-yellow-100 text-yellow-700', 'hard' => 'bg-red-100 text-red-700' } }}">
                        {{ $problem->difficulty }}
                    </span>
                </div>
                <p class="px-5 py-3 text-sm text-gray-600 leading-relaxed border-b border-gray-50">{{ $problem->description }}</p>
                <div class="p-4">
                    <textarea name="answers[{{ $problem->id }}]" rows="10" spellcheck="false"
                              placeholder="{{ __('tests.your_answer') }}"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono resize-y focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $existingAnswers[$problem->id] ?? '' }}</textarea>
                </div>
            </div>
            @endforeach

            <button type="button" @click="confirmSubmit()"
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                {{ __('tests.submit_test') }}
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
    function testTimer(initialSeconds) {
        return {
            seconds: initialSeconds,
            interval: null,

            get formatted() {
                const h = Math.floor(this.seconds / 3600);
                const m = Math.floor((this.seconds % 3600) / 60);
                const s = this.seconds % 60;
                if (h > 0) {
                    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                }
                return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            },

            start() {
                this.interval = setInterval(() => {
                    this.seconds--;
                    if (this.seconds <= 0) {
                        clearInterval(this.interval);
                        document.getElementById('test-form').submit();
                    }
                }, 1000);
            },

            confirmSubmit() {
                if (confirm('{{ __('tests.confirm_submit') }}')) {
                    clearInterval(this.interval);
                    document.getElementById('test-form').submit();
                }
            },
        };
    }
    </script>
    @endpush

@else
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md text-center bg-white rounded-2xl shadow-lg p-8">
            <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('tests.completed') }}</h1>
            <p class="text-sm text-gray-500">Thanks, {{ $invite->candidate_name }} — your answers for "{{ $invite->test->title }}" have been recorded. The team will be in touch.</p>
        </div>
    </div>
@endif

@stack('scripts')
</body>
</html>
