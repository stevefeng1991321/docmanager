@extends('layouts.admin')
@section('title', 'Departments')

@section('content')
<div x-data="departmentManager()" class="space-y-5">

    @include('admin.employees._tabs', ['active' => 'departments'])

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + Add Department
    </button>

    {{-- Add form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                <input type="text" name="name" required placeholder="e.g. Engineering"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description (optional)</label>
                <input type="text" name="description" placeholder="Short description"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                Save
            </button>
        </form>
    </div>

    {{-- Department table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employees</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($departments as $dept)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $dept->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $dept->description ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $dept->employees_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                    @click="openEdit({{ $dept->id }}, {{ \Illuminate\Support\Js::from($dept->name) }}, {{ \Illuminate\Support\Js::from($dept->description) }})"
                                    class="text-xs text-blue-600 hover:text-blue-800">Edit</button>
                            <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}"
                                  onsubmit="return confirm('Delete this department?')">
                                @csrf @method('DELETE')
                                <button class="text-xs @if($dept->employees_count > 0) text-gray-300 cursor-not-allowed @else text-red-500 hover:text-red-700 @endif"
                                        @if($dept->employees_count > 0) disabled title="Department is in use" @endif>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No departments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Edit modal --}}
    <div x-show="editId !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @keydown.escape.window="editId = null">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
            <h3 class="font-semibold text-gray-800">Edit Department</h3>
            <form method="POST" x-bind:action="`{{ url('/admin/departments') }}/${editId}`" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                    <input type="text" name="name" x-model="editName" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <input type="text" name="description" x-model="editDescription"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
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
function departmentManager() {
    return {
        showForm: false,
        editId: null,
        editName: '',
        editDescription: '',

        openEdit(id, name, description) {
            this.editId          = id;
            this.editName        = name;
            this.editDescription = description ?? '';
        },
    }
}
</script>
@endpush

@endsection
