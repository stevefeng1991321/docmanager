@extends('layouts.admin')
@section('title', 'Edit Problem')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Edit Problem</h1>
        <a href="{{ route('admin.problems.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Problems</a>
    </div>

    <form method="POST" action="{{ route('admin.problems.update', $problem) }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Index</label>
                <input type="number" name="order_index" value="{{ old('order_index', $problem->order_index) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('order_index') border-red-400 @enderror">
                @error('order_index') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                <select name="difficulty"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('difficulty') border-red-400 @enderror">
                    @foreach(['easy', 'medium', 'hard'] as $d)
                        <option value="{{ $d }}" @selected(old('difficulty', $problem->difficulty) === $d)>{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
                @error('difficulty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $problem->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ old('category', $problem->category) }}" list="categories-list"
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
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('description') border-red-400 @enderror">{{ old('description', $problem->description) }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Solution Code</label>
            <textarea name="solution_code" rows="12" spellcheck="false"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono resize-y @error('solution_code') border-red-400 @enderror">{{ old('solution_code', $problem->solution_code) }}</textarea>
            @error('solution_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Save Changes
            </button>
            <a href="{{ route('admin.problems.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
