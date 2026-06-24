@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div x-data="categoryManager()" class="space-y-5">

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + Add Category
    </button>

    {{-- Add form --}}
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

    {{-- Category table --}}
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
                @forelse ($categories as $parent)
                {{-- Parent row --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $parent->name }}</td>
                    <td class="px-4 py-3 text-gray-400">—</td>
                    <td class="px-4 py-3 text-gray-500">{{ $parent->resources_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                    @click="openEdit({{ $parent->id }}, '{{ addslashes($parent->name) }}', '')"
                                    class="text-xs text-blue-600 hover:text-blue-800">Edit</button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $parent) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-xs @if($parent->resources_count > 0 || $parent->children_count > 0) text-gray-300 cursor-not-allowed @else text-red-500 hover:text-red-700 @endif"
                                        @if($parent->resources_count > 0 || $parent->children_count > 0) disabled title="Category is in use" @endif>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Children rows --}}
                @foreach($parent->children as $child)
                <tr class="hover:bg-gray-50 bg-gray-50/50">
                    <td class="px-4 py-3 text-gray-700">
                        <span class="text-gray-300 mr-1 ml-3">└</span>{{ $child->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $parent->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $child->resources_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                    @click="openEdit({{ $child->id }}, '{{ addslashes($child->name) }}', '{{ $child->parent_id }}')"
                                    class="text-xs text-blue-600 hover:text-blue-800">Edit</button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-xs @if($child->resources_count > 0) text-gray-300 cursor-not-allowed @else text-red-500 hover:text-red-700 @endif"
                                        @if($child->resources_count > 0) disabled title="Category is in use" @endif>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Edit modal --}}
    <div x-show="editId !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @keydown.escape.window="editId = null">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
            <h3 class="font-semibold text-gray-800">Edit Category</h3>
            <form method="POST" x-bind:action="`{{ url('/admin/categories') }}/${editId}`" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                    <input type="text" name="name" x-model="editName" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Parent (optional)</label>
                    <select name="parent_id" x-model="editParentId"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— None —</option>
                        @foreach ($categories as $cat)
                            @if(!$cat->parent_id)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Save Changes
                    </button>
                    <button type="button" @click="editId = null"
                            class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function categoryManager() {
    return {
        showForm: false,
        editId: null,
        editName: '',
        editParentId: '',

        openEdit(id, name, parentId) {
            this.editId      = id;
            this.editName    = name;
            this.editParentId = String(parentId);
        },
    }
}
</script>
@endpush

@endsection
