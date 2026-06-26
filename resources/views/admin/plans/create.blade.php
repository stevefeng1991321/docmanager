@extends('layouts.admin')
@section('title', 'New Plan')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">New Plan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new work plan</p>
        </div>
        <a href="{{ route('admin.plans.index') }}"
           class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            ← Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Basic Information</h3>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Plan title…">
                @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                          placeholder="Plan description…">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(['daily','weekly','monthly','quarterly','annual','personal','team','project','strategic'] as $c)
                        <option value="{{ $c }}" @selected(old('category')===$c)>{{ ucfirst($c) }} Plan</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(['low','medium','high','critical'] as $p)
                        <option value="{{ $p }}" @selected(old('priority','medium')===$p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @foreach(['draft','pending','in_progress','on_hold'] as $s)
                        <option value="{{ $s }}" @selected(old('status','draft')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                    <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">None</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected(old('department_id')==$dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Project</label>
                    <select name="project_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">None</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected(old('project_id')==$project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Estimated Hours</label>
                    <input type="number" name="estimated_hours" value="{{ old('estimated_hours') }}" min="0" step="0.5"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Assigned Employees --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3 mb-4">Assigned Employees</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 max-h-48 overflow-y-auto">
                @foreach($employees as $emp)
                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer text-xs">
                    <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}"
                           @checked(in_array($emp->id, old('employee_ids', [])))
                           class="rounded border-gray-300 text-blue-600">
                    <span class="truncate text-gray-700">{{ $emp->full_name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Tags & Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Additional Info</h3>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tags <span class="text-gray-400 font-normal">(comma separated)</span></label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="e.g. marketing, q3, urgent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                          placeholder="Internal notes…">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.plans.index') }}"
               class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" name="status" value="draft"
                    class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Save as Draft
            </button>
            <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                Create Plan
            </button>
        </div>
    </form>

@endsection
