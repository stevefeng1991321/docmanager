@extends('layouts.admin')
@section('title', 'New Problem')

@section('content')
@php
    $initialCases = collect(old('test_cases', []))->values()->all();
@endphp
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">New Problem</h1>
        <a href="{{ route('admin.problems.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Problems</a>
    </div>

    <form method="POST" action="{{ route('admin.problems.store') }}"
          x-data="testCaseBuilder({{ \Illuminate\Support\Js::from($initialCases) }})"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Index</label>
                <input type="number" name="order_index" value="{{ old('order_index', 1) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('order_index') border-red-400 @enderror">
                @error('order_index') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                <select name="difficulty"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('difficulty') border-red-400 @enderror">
                    @foreach(['easy', 'medium', 'hard'] as $d)
                        <option value="{{ $d }}" @selected(old('difficulty') === $d)>{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
                @error('difficulty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ old('category') }}" list="categories-list"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('category') border-red-400 @enderror">
            <datalist id="categories-list">
                @foreach(['JavaScript', 'Math', 'Algorithms', 'AI'] as $c)
                    <option value="{{ $c }}">
                @endforeach
            </datalist>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Solution Code</label>
            <textarea name="solution_code" rows="12" spellcheck="false"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono resize-y @error('solution_code') border-red-400 @enderror">{{ old('solution_code') }}</textarea>
            @error('solution_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-gray-100 pt-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Function Name <span class="text-gray-400">(for auto-grading)</span></label>
                <input type="text" name="function_name" value="{{ old('function_name') }}" placeholder="e.g. evenOrOdd"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono @error('function_name') border-red-400 @enderror">
                @error('function_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Must match the function the candidate is expected to define. Leave blank to grade this problem manually.</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-medium text-gray-700">Test Cases</label>
                    <button type="button" @click="addCase()"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg transition">
                        + Add Test Case
                    </button>
                </div>

                <template x-for="(c, i) in cases" :key="i">
                    <div class="flex items-start gap-2 mb-2">
                        <input type="text" :name="`test_cases[${i}][args]`" x-model="c.args" placeholder="Args (JSON array), e.g. [4]"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                        <input type="text" :name="`test_cases[${i}][expected]`" x-model="c.expected" placeholder='Expected (JSON), e.g. "Even"'
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono">
                        <button type="button" @click="removeCase(i)" class="text-xs text-red-500 hover:text-red-700 px-1 py-2.5">Remove</button>
                    </div>
                </template>
                @error('test_cases') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @foreach(old('test_cases', []) as $i => $case)
                    @error("test_cases.{$i}.args") <p class="text-red-500 text-xs mt-1">Row {{ $i + 1 }}: {{ $message }}</p> @enderror
                    @error("test_cases.{$i}.expected") <p class="text-red-500 text-xs mt-1">Row {{ $i + 1 }}: {{ $message }}</p> @enderror
                @endforeach
                <p x-show="cases.length === 0" class="text-xs text-gray-400">No test cases yet — this problem will be graded manually.</p>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Create Problem
            </button>
            <a href="{{ route('admin.problems.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function testCaseBuilder(initialCases) {
    return {
        cases: initialCases.length ? initialCases : [],
        addCase() {
            this.cases.push({ args: '', expected: '' });
        },
        removeCase(i) {
            this.cases.splice(i, 1);
        },
    };
}
</script>
@endpush
