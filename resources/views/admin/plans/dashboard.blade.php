@extends('layouts.admin')
@section('title', 'Plans Dashboard')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Plans Dashboard</h2>
            <p class="text-sm text-gray-500 mt-0.5">Overview of all work plans</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.plans.index') }}"
               class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                All Plans
            </a>
            <a href="{{ route('admin.plans.create') }}"
               class="px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                + New Plan
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        @php
            $cards = [
                ['label' => 'Total',      'value' => $summary['total'],     'color' => 'bg-gray-50   border-gray-200   text-gray-700'],
                ['label' => 'Active',     'value' => $summary['active'],    'color' => 'bg-blue-50   border-blue-200   text-blue-700'],
                ['label' => 'Completed',  'value' => $summary['completed'], 'color' => 'bg-green-50  border-green-200  text-green-700'],
                ['label' => 'Overdue',    'value' => $summary['overdue'],   'color' => 'bg-red-50    border-red-200    text-red-700'],
                ['label' => 'Due Today',  'value' => $summary['due_today'], 'color' => 'bg-orange-50 border-orange-200 text-orange-700'],
                ['label' => 'Draft',      'value' => $summary['draft'],     'color' => 'bg-gray-50   border-gray-200   text-gray-500'],
                ['label' => 'On Hold',    'value' => $summary['on_hold'],   'color' => 'bg-yellow-50 border-yellow-200 text-yellow-700'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="rounded-xl border p-3 text-center {{ $card['color'] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium mt-0.5">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- By Status --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Plans by Status</h3>
            @php
                $statusColors = ['draft'=>'bg-gray-400','pending'=>'bg-yellow-400','in_progress'=>'bg-blue-500','on_hold'=>'bg-orange-400','completed'=>'bg-green-500','cancelled'=>'bg-red-400','archived'=>'bg-purple-400'];
                $statusLabels = ['draft'=>'Draft','pending'=>'Pending','in_progress'=>'In Progress','on_hold'=>'On Hold','completed'=>'Completed','cancelled'=>'Cancelled','archived'=>'Archived'];
                $total = $byStatus->sum() ?: 1;
            @endphp
            <div class="space-y-2">
                @foreach($statusLabels as $key => $label)
                @php $count = $byStatus[$key] ?? 0; $pct = round($count/$total*100); @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-600 mb-0.5">
                        <span>{{ $label }}</span><span>{{ $count }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $statusColors[$key] ?? 'bg-gray-400' }} rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- By Priority --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Plans by Priority</h3>
            @php
                $priorityColors = ['low'=>'bg-green-400','medium'=>'bg-blue-400','high'=>'bg-orange-400','critical'=>'bg-red-500'];
                $totalP = $byPriority->sum() ?: 1;
            @endphp
            <div class="space-y-2">
                @foreach(['critical','high','medium','low'] as $p)
                @php $count = $byPriority[$p] ?? 0; $pct = round($count/$totalP*100); @endphp
                <div>
                    <div class="flex justify-between text-xs text-gray-600 mb-0.5">
                        <span>{{ ucfirst($p) }}</span><span>{{ $count }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $priorityColors[$p] }} rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Task Stats --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Task Overview</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Tasks</span>
                    <span class="font-semibold text-gray-800">{{ $taskStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="font-semibold text-green-600">{{ $taskStats['completed'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Overdue Tasks</span>
                    <span class="font-semibold text-red-600">{{ $taskStats['overdue'] }}</span>
                </div>
                @if($taskStats['total'] > 0)
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Completion Rate</span>
                        <span>{{ round($taskStats['completed']/$taskStats['total']*100) }}%</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width:{{ round($taskStats['completed']/$taskStats['total']*100) }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Recent Plans --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Recent Plans</h3>
                <a href="{{ route('admin.plans.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPlans as $plan)
                <a href="{{ route('admin.plans.show', $plan) }}"
                   class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $plan->title }}</div>
                        <div class="text-xs text-gray-400">{{ $plan->plan_number }} · {{ $plan->owner->name }}</div>
                    </div>
                    <span class="ml-3 flex-shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->status->badge() }}">
                        {{ $plan->status->label() }}
                    </span>
                </a>
                @empty
                <p class="px-4 py-6 text-xs text-gray-400 text-center">No plans yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Overdue Plans --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Overdue Plans</h3>
                <span class="text-xs text-red-600 font-medium">{{ $summary['overdue'] }} overdue</span>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($overduePlans as $plan)
                <a href="{{ route('admin.plans.show', $plan) }}"
                   class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 transition">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $plan->title }}</div>
                        <div class="text-xs text-red-400">Due {{ $plan->due_date->format('M d, Y') }}</div>
                    </div>
                    <span class="ml-3 flex-shrink-0 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">
                        {{ $plan->due_date->diffForHumans() }}
                    </span>
                </a>
                @empty
                <p class="px-4 py-6 text-xs text-gray-400 text-center">No overdue plans.</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection
