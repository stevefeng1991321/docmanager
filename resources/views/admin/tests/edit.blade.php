@extends('layouts.admin')
@section('title', 'Edit Test')

@section('content')
@php
    $initialPoints = [];
    $allProblemsJson = [];
    foreach ($problemBank as $p) {
        $initialPoints[$p->id] = (int) old('points.'.$p->id, $pointsById[$p->id] ?? 100);
        $allProblemsJson[] = [
            'id'         => (string) $p->id,
            'title'      => $p->title,
            'difficulty' => $p->difficulty,
            'category'   => $p->category,
        ];
    }
@endphp
<div x-data="testForm({{ \Illuminate\Support\Js::from($initialPoints) }}, {{ \Illuminate\Support\Js::from($allProblemsJson) }})" class="max-w-4xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">{{ __('admin.tests.edit') }}</h1>
        <a href="{{ route('admin.tests.show', $test) }}" class="text-sm text-gray-500 hover:text-gray-700">{{ __('common.back') }}</a>
    </div>

    <form method="POST" action="{{ route('admin.tests.update', $test) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.tests.title_label') }}</label>
                <input type="text" name="title" value="{{ old('title', $test->title) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.tests.description_label') }} <span class="text-gray-400">{{ __('common.shown_to_candidate') }}</span></label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('description') border-red-400 @enderror">{{ old('description', $test->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.tests.time_limit_label') }}</label>
                    <input type="number" name="time_limit_minutes" value="{{ old('time_limit_minutes', $test->time_limit_minutes) }}" min="1" max="600"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('time_limit_minutes') border-red-400 @enderror">
                    @error('time_limit_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.tests.status_label') }}</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('status') border-red-400 @enderror">
                        @foreach(['draft' => 'Draft (not yet invitable)', 'active' => 'Active (invites can be created)', 'archived' => 'Archived (no new invites)'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $test->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="text-sm font-semibold text-gray-700">Problems from the Bank</span>
                    <span class="text-xs text-gray-400 ml-2"><span x-text="selected.length"></span> selected</span>
                </div>
                <button type="button" @click="showPicker = true"
                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">
                    + Add Existing Problem
                </button>
            </div>
            @error('problems') <p class="text-red-500 text-xs px-5 pt-3">{{ $message }}</p> @enderror

            <div class="divide-y divide-gray-50">
                <template x-for="p in selectedProblems" :key="p.id">
                    <div class="flex items-center gap-3 px-5 py-2.5">
                        <span class="flex-1 text-sm text-gray-800" x-text="p.title"></span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize"
                              :class="{
                                  'bg-green-100 text-green-700': p.difficulty === 'easy',
                                  'bg-yellow-100 text-yellow-700': p.difficulty === 'medium',
                                  'bg-red-100 text-red-700': p.difficulty === 'hard',
                              }" x-text="p.difficulty"></span>
                        <span class="text-xs text-gray-400 w-24 truncate" x-text="p.category"></span>
                        <span class="flex items-center gap-1">
                            <input type="number" :name="`points[${p.id}]`" x-model.number="points[p.id]" min="1" max="1000"
                                   class="w-16 border border-gray-300 rounded-lg px-2 py-1 text-xs text-right">
                            <span class="text-xs text-gray-400">pts</span>
                        </span>
                        <button type="button" @click="toggle(p.id)" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                    </div>
                </template>
                <p x-show="selected.length === 0" class="px-5 py-6 text-center text-xs text-gray-400">
                    No existing problems added yet. Click "+ Add Existing Problem" to pick from the bank.
                </p>
            </div>
        </div>

        {{-- Add Existing Problem modal --}}
        <div x-show="showPicker" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             @keydown.escape.window="showPicker = false">
            <div @click.outside="showPicker = false"
                 class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3">
                    <span class="text-sm font-semibold text-gray-700">Add Existing Problem</span>
                    <input type="text" x-model="search" placeholder="Search problems…"
                           class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
                    @foreach($problemBank as $problem)
                    <label x-show="matches({{ $problem->id }}, {{ \Illuminate\Support\Js::from($problem->title) }}, {{ \Illuminate\Support\Js::from($problem->category) }})"
                           class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="problems[]" value="{{ $problem->id }}"
                               x-model="selected"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-gray-400 font-mono w-6">{{ $problem->order_index }}</span>
                        <span class="flex-1 text-sm text-gray-800">{{ $problem->title }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                            {{ match($problem->difficulty) { 'easy' => 'bg-green-100 text-green-700', 'medium' => 'bg-yellow-100 text-yellow-700', 'hard' => 'bg-red-100 text-red-700' } }}">
                            {{ $problem->difficulty }}
                        </span>
                        <span class="text-xs text-gray-400 w-24 truncate">{{ $problem->category }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
                    <span class="text-xs text-gray-400"><span x-text="selected.length"></span> selected</span>
                    <button type="button" @click="showPicker = false"
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                        Done
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-sm font-semibold text-gray-700">Custom Problems</span>
                    <span class="text-xs text-gray-400 ml-2">not in the bank — created just for this test</span>
                </div>
                <button type="button" @click="addCustom()"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg transition">
                    + Add Custom Problem
                </button>
            </div>

            <template x-for="(custom, i) in customs" :key="i">
                <div class="p-4 border-b border-gray-50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500">Custom Problem <span x-text="i + 1"></span></span>
                        <button type="button" @click="removeCustom(i)" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" :name="`new_problems[${i}][title]`" x-model="custom.title" placeholder="Title"
                               class="col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <select :name="`new_problems[${i}][difficulty]`" x-model="custom.difficulty"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <textarea :name="`new_problems[${i}][description]`" x-model="custom.description" rows="2"
                              placeholder="Problem description (shown to the candidate)"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y"></textarea>
                    <textarea :name="`new_problems[${i}][solution_code]`" x-model="custom.solution_code" rows="3"
                              placeholder="Reference solution (optional, used during grading)" spellcheck="false"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono resize-y"></textarea>
                    <div class="flex items-center gap-3">
                        <input type="text" :name="`new_problems[${i}][category]`" x-model="custom.category"
                               placeholder="Category (optional, default: Custom)"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <div class="flex items-center gap-1">
                            <input type="number" :name="`new_problems[${i}][points]`" x-model.number="custom.points" min="1" max="1000"
                                   class="w-20 border border-gray-300 rounded-lg px-2 py-1.5 text-xs text-right">
                            <span class="text-xs text-gray-400">pts</span>
                        </div>
                    </div>
                </div>
            </template>

            <p x-show="customs.length === 0" class="px-5 py-4 text-xs text-gray-400">No custom problems added.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                {{ __('common.save_changes') }}
            </button>
            <a href="{{ route('admin.tests.show', $test) }}" class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                {{ __('common.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function testForm(initialPoints, allProblems) {
    return {
        search: '',
        showPicker: false,
        selected: @json(old('problems', array_map('strval', $selectedIds))),
        points: initialPoints,
        allProblems: allProblems,
        customs: @json(old('new_problems', [])),

        matches(id, title, category) {
            const q = this.search.toLowerCase();
            if (!q) return true;
            return title.toLowerCase().includes(q) || category.toLowerCase().includes(q);
        },

        get selectedProblems() {
            return this.selected
                .map(id => this.allProblems.find(p => p.id === id))
                .filter(Boolean);
        },

        toggle(id) {
            const idx = this.selected.indexOf(id);
            if (idx > -1) this.selected.splice(idx, 1);
            else this.selected.push(id);
        },

        addCustom() {
            this.customs.push({ title: '', description: '', difficulty: 'medium', category: '', solution_code: '', points: 100 });
        },

        removeCustom(i) {
            this.customs.splice(i, 1);
        },
    };
}
</script>
@endpush
