@extends('layouts.admin')
@section('title', 'Plans')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Plan Management</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create and manage work plans across teams</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.plans.dashboard') }}"
               class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Dashboard
            </a>
            <a href="{{ route('admin.plans.create') }}"
               class="px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                + New Plan
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
        @php
            $cards = [
                ['label'=>'Total',     'value'=>$summary['total'],     'color'=>'bg-gray-50   border-gray-200   text-gray-700'],
                ['label'=>'Active',    'value'=>$summary['active'],    'color'=>'bg-blue-50   border-blue-200   text-blue-700'],
                ['label'=>'Completed', 'value'=>$summary['completed'], 'color'=>'bg-green-50  border-green-200  text-green-700'],
                ['label'=>'Overdue',   'value'=>$summary['overdue'],   'color'=>'bg-red-50    border-red-200    text-red-700'],
                ['label'=>'Due Today', 'value'=>$summary['due_today'], 'color'=>'bg-orange-50 border-orange-200 text-orange-700'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-xl border p-3 text-center {{ $card['color'] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.plans.index') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Plan title…"
                   class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All</option>
                @foreach(['draft','pending','in_progress','on_hold','completed','cancelled','archived'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
            <select name="priority" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All</option>
                @foreach(['low','medium','high','critical'] as $p)
                <option value="{{ $p }}" @selected(request('priority')===$p)>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All</option>
                @foreach(['daily','weekly','monthly','quarterly','annual','personal','team','project','strategic'] as $c)
                <option value="{{ $c }}" @selected(request('category')===$c)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
            <select name="department_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">All</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id')==$dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Filter</button>
        @if(request()->hasAny(['search','status','priority','category','department_id','employee_id']))
        <a href="{{ route('admin.plans.index') }}" class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700 transition">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Plan</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Category</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Priority</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Progress</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden lg:table-cell">Due Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden lg:table-cell">Owner</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($plans as $plan)
                <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.plans.show', $plan) }}'">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $plan->title }}</div>
                        <div class="text-xs text-gray-400">{{ $plan->plan_number }} · {{ $plan->employees->count() }} assigned</div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ ucfirst($plan->category) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->priority->badge() }}">
                            {{ $plan->priority->label() }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->status->badge() }}">
                            {{ $plan->status->label() }}
                        </span>
                        @if($plan->is_overdue)
                        <span class="ml-1 text-xs text-red-500 font-medium">Overdue</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden w-20">
                                <div class="h-full bg-blue-500 rounded-full" style="width:{{ $plan->progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $plan->progress }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">
                        {{ $plan->due_date?->format('M d, Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden lg:table-cell">{{ $plan->owner->name }}</td>
                    <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                                  onsubmit="return confirm('Delete this plan?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">No plans found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($plans->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $plans->links() }}</div>
        @endif
    </div>

@endsection
