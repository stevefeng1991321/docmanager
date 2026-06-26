@extends('layouts.app')
@section('title', 'My Plans')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">My Plans</h2>
            <p class="text-sm text-gray-500 mt-0.5">Plans you have been assigned to</p>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        @php
            $cards = [
                ['label' => 'Total',     'value' => $summary['total']     ?? 0, 'color' => 'bg-gray-50   border-gray-200   text-gray-700'],
                ['label' => 'Active',    'value' => $summary['active']    ?? 0, 'color' => 'bg-blue-50   border-blue-200   text-blue-700'],
                ['label' => 'Completed', 'value' => $summary['completed'] ?? 0, 'color' => 'bg-green-50  border-green-200  text-green-700'],
                ['label' => 'Overdue',   'value' => $summary['overdue']   ?? 0, 'color' => 'bg-red-50    border-red-200    text-red-700'],
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
    <form method="GET" action="{{ route('plans.index') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-5 flex flex-wrap gap-3 items-end">
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
        <button type="submit" class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Filter</button>
        @if(request()->hasAny(['status','priority']))
        <a href="{{ route('plans.index') }}" class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    @php
        $sc = ['draft'=>'bg-gray-100 text-gray-600','pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','on_hold'=>'bg-orange-100 text-orange-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','archived'=>'bg-purple-100 text-purple-700'];
        $pc = ['low'=>'bg-green-100 text-green-700','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','critical'=>'bg-red-100 text-red-700'];
    @endphp

    @if($plans->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-16 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-500 text-sm">No plans assigned to you yet.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($plans as $plan)
        <a href="{{ route('plans.show', $plan) }}"
           class="block bg-white rounded-xl border border-gray-200 p-4 hover:border-blue-300 hover:shadow-sm transition">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-xs text-gray-400 font-mono">{{ $plan->plan_number }}</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$plan->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $plan->status_label }}
                        </span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $pc[$plan->priority] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($plan->priority) }}
                        </span>
                        @if($plan->is_overdue)
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Overdue</span>
                        @endif
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $plan->title }}</h3>
                    @if($plan->description)
                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $plan->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                        <span>{{ ucfirst($plan->category) }} Plan</span>
                        @if($plan->due_date)<span>Due {{ $plan->due_date->format('M d, Y') }}</span>@endif
                        <span>{{ $plan->tasks->count() }} tasks</span>
                    </div>
                </div>
                <div class="flex-shrink-0 text-right">
                    <div class="text-sm font-semibold text-gray-700">{{ $plan->progress }}%</div>
                    <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden mt-1">
                        <div class="h-full rounded-full {{ $plan->progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width:{{ $plan->progress }}%"></div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if($plans->hasPages())
    <div class="mt-4">{{ $plans->links() }}</div>
    @endif
    @endif

@endsection
