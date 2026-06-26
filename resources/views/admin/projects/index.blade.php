@extends('layouts.admin')
@section('title', 'Projects')

@section('content')
<div x-data="projectManager()" class="space-y-5">

    @include('admin.work-reports._tabs', ['active' => 'projects'])

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + Add Project
    </button>

    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.projects.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                <input type="text" name="name" required placeholder="e.g. Checkout Redesign"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department (optional)</label>
                <select name="department_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48">
                    <option value="">— None —</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="active">Active</option>
                    <option value="on_hold">On Hold</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Description (optional)</label>
                <input type="text" name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">Save</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reports</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $project->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $project->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($project->status) { 'active' => 'bg-green-100 text-green-700', 'on_hold' => 'bg-amber-100 text-amber-700', 'completed' => 'bg-gray-100 text-gray-600' } }}">
                            {{ str_replace('_',' ',$project->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $project->work_reports_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                    @click="openEdit({{ $project->id }}, {{ \Illuminate\Support\Js::from($project->name) }}, {{ \Illuminate\Support\Js::from((string) $project->department_id) }}, {{ \Illuminate\Support\Js::from($project->status) }}, {{ \Illuminate\Support\Js::from($project->description) }})"
                                    class="text-xs text-blue-600 hover:text-blue-800">Edit</button>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?')">
                                @csrf @method('DELETE')
                                <button class="text-xs @if($project->work_reports_count > 0) text-gray-300 cursor-not-allowed @else text-red-500 hover:text-red-700 @endif"
                                        @if($project->work_reports_count > 0) disabled title="Project is in use" @endif>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="editId !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @keydown.escape.window="editId = null">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
            <h3 class="font-semibold text-gray-800">Edit Project</h3>
            <form method="POST" x-bind:action="`{{ url('/admin/projects') }}/${editId}`" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name</label>
                    <input type="text" name="name" x-model="editName" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                    <select name="department_id" x-model="editDepartmentId" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— None —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" x-model="editStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea name="description" x-model="editDescription" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">Save Changes</button>
                    <button type="button" @click="editId = null" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function projectManager() {
    return {
        showForm: false,
        editId: null,
        editName: '',
        editDepartmentId: '',
        editStatus: 'active',
        editDescription: '',

        openEdit(id, name, departmentId, status, description) {
            this.editId = id;
            this.editName = name;
            this.editDepartmentId = departmentId === 'null' ? '' : departmentId;
            this.editStatus = status;
            this.editDescription = description ?? '';
        },
    }
}
</script>
@endpush
@endsection
