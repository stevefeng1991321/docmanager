@extends('layouts.admin')
@section('title', 'Employees')

@section('content')
<div class="space-y-5">

    @include('admin.employees._tabs', ['active' => 'employees'])

    <div class="flex items-center justify-between flex-wrap gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.employees.search_placeholder') }}"
                   class="w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <select name="department" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">{{ __('admin.employees.all_departments') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(request('department') == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
            <select name="position" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Positions</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}" @selected(request('position') == $pos->id)>{{ $pos->title }}</option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">{{ __('common.all_status') }}</option>
                @foreach(['active','inactive','resigned','terminated'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="date_desc" @selected($sort === 'date_desc')>Newest Hires</option>
                <option value="date_asc"  @selected($sort === 'date_asc') >Oldest Hires</option>
                <option value="name_asc"  @selected($sort === 'name_asc') >Name A–Z</option>
                <option value="name_desc" @selected($sort === 'name_desc')>Name Z–A</option>
                <option value="code_asc"  @selected($sort === 'code_asc') >Employee Code</option>
            </select>
            <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('common.filter') }}</button>
            @if(request()->hasAny(['search','department','position','status','sort']))
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">{{ __('common.clear') }}</a>
            @endif
        </form>
        <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + {{ __('admin.employees.new_employee') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_id') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_department') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_position') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.employees.col_hire_date') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.employees.show', $employee) }}" class="flex items-center gap-2.5 group">
                            @if($employee->photoUrl())
                                <img src="{{ $employee->photoUrl() }}" class="w-8 h-8 rounded-full object-cover" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase text-xs flex-shrink-0">
                                    {{ substr($employee->full_name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-medium text-gray-800 group-hover:text-blue-600 truncate">{{ $employee->full_name }}</div>
                                @if($employee->email)
                                    <div class="text-xs text-gray-400 truncate">{{ $employee->email }}</div>
                                @endif
                            </div>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $employee->employee_code }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($employee->employment_status) {
                                'active' => 'bg-green-100 text-green-700',
                                'inactive' => 'bg-gray-100 text-gray-600',
                                'resigned' => 'bg-amber-100 text-amber-700',
                                'terminated' => 'bg-red-100 text-red-700',
                            } }}">
                            {{ $employee->employment_status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $employee->date_of_joining?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="text-xs text-gray-500 hover:text-blue-600 mr-3">{{ __('admin.employees.edit_action') }}</a>
                        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline"
                              onsubmit="return confirm('{{ __('admin.employees.confirm_delete', ['name' => '']) }}' + '{{ addslashes($employee->full_name) }}' + '?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">{{ __('admin.employees.delete_action') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">{{ __('admin.employees.no_employees') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-500">
                    Showing {{ $employees->firstItem() ?? 0 }}–{{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }}
                </span>
                @if($employees->total() > 10)
                <form method="GET" class="flex items-center gap-2">
                    @foreach(request()->except(['per_page', 'page']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label class="text-xs text-gray-500 whitespace-nowrap">{{ __('common.per_page') }}</label>
                    <select name="per_page" onchange="this.form.submit()"
                            class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                        @foreach(config('pagination.per_page_options') as $n)
                            <option value="{{ $n }}" @selected((int) request('per_page', config('pagination.default_per_page')) === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
            {{ $employees->links('vendor.pagination.admin-compact') }}
        </div>
    </div>
</div>
@endsection
