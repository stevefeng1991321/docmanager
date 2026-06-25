@extends('layouts.admin')
@section('title', 'Edit Trend')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Edit Trend</h1>
        <a href="{{ route('admin.science-tech.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
    </div>

    <form method="POST" action="{{ route('admin.science-tech.update', $trend) }}"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <input type="number" name="year" value="{{ old('year', $trend->year) }}" min="2000" max="2100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('year') border-red-400 @enderror">
                @error('year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('status') border-red-400 @enderror">
                    @foreach(['draft', 'published', 'archived'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $trend->status) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $trend->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
            <textarea name="summary" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('summary') border-red-400 @enderror"
                      placeholder="Short description shown in listings…">{{ old('summary', $trend->summary) }}</textarea>
            @error('summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
            <textarea name="content" rows="14"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y @error('content') border-red-400 @enderror">{{ old('content', $trend->content) }}</textarea>
            @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Save Changes
            </button>
            <a href="{{ route('admin.science-tech.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
