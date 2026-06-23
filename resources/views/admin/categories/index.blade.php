@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div x-data="{ showForm: false, editId: null, name: '', parentId: '' }" class="space-y-5">

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + Add Category
    </button>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                <input type="text" name="name" required placeholder="Category name"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Parent (optional)</label>
                <select name="parent_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48">
                    <option value="">— None —</option>
                    @foreach ($categories as $cat)
                        @if(!$cat->parent_id)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                Save
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Parent</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Docs</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">
                        @if($cat->parent_id) <span class="text-gray-400 mr-1">└</span> @endif
                        {{ $cat->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $cat->parent?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $cat->resources_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
