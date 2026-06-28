@extends('layouts.admin')
@section('title', 'Positions')

@section('content')
<div x-data="positionManager()" class="space-y-5">

    @include('admin.employees._tabs', ['active' => 'positions'])

    <button @click="showForm = !showForm"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
        + {{ __('admin.positions.new_position') }}
    </button>

    {{-- Add form --}}
    <div x-show="showForm" x-cloak class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <form method="POST" action="{{ route('admin.positions.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.positions.name_label') }}</label>
                <input type="text" name="title" required placeholder="{{ __('admin.positions.name_placeholder') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.positions.department_label') }} {{ __('common.optional') }}</label>
                <select name="department_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48">
                    <option value="">— None —</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                {{ __('common.save') }}
            </button>
        </form>
    </div>

    {{-- Position table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.positions.col_name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.positions.col_department') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.positions.col_employees') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($positions as $pos)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $pos->title }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $pos->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $pos->employees_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                    @click="openEdit({{ $pos->id }}, {{ \Illuminate\Support\Js::from($pos->title) }}, {{ \Illuminate\Support\Js::from((string) $pos->department_id) }})"
                                    class="text-xs text-blue-600 hover:text-blue-800">{{ __('admin.positions.edit_action') }}</button>
                            <form method="POST" action="{{ route('admin.positions.destroy', $pos) }}"
                                  onsubmit="return confirm('{{ __('admin.positions.confirm_delete', ['name' => '']) }}')">
                                @csrf @method('DELETE')
                                <button class="text-xs @if($pos->employees_count > 0) text-gray-300 cursor-not-allowed @else text-red-500 hover:text-red-700 @endif"
                                        @if($pos->employees_count > 0) disabled title="Position is in use" @endif>
                                    {{ __('admin.positions.delete_action') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ __('admin.positions.no_positions') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Edit modal --}}
    <div x-show="editId !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @keydown.escape.window="editId = null">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" @click.stop>
            <h3 class="font-semibold text-gray-800">{{ __('admin.positions.edit') }}</h3>
            <form method="POST" x-bind:action="`{{ url('/admin/positions') }}/${editId}`" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.positions.name_label') }}</label>
                    <input type="text" name="title" x-model="editTitle" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('admin.positions.department_label') }}</label>
                    <select name="department_id" x-model="editDepartmentId"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— None —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        {{ __('common.save_changes') }}
                    </button>
                    <button type="button" @click="editId = null"
                            class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                        {{ __('common.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function positionManager() {
    return {
        showForm: false,
        editId: null,
        editTitle: '',
        editDepartmentId: '',

        openEdit(id, title, departmentId) {
            this.editId           = id;
            this.editTitle        = title;
            this.editDepartmentId = departmentId === 'null' ? '' : departmentId;
        },
    }
}
</script>
@endpush

@endsection
