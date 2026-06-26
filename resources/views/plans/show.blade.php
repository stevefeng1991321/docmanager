@extends('layouts.app')
@section('title', $plan->title)

@section('content')

    @php
        $sc = ['draft'=>'bg-gray-100 text-gray-600','pending'=>'bg-yellow-100 text-yellow-700','in_progress'=>'bg-blue-100 text-blue-700','on_hold'=>'bg-orange-100 text-orange-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700','archived'=>'bg-purple-100 text-purple-700'];
        $pc = ['low'=>'bg-green-100 text-green-700','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','critical'=>'bg-red-100 text-red-700'];
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="text-xs text-gray-400 font-mono">{{ $plan->plan_number }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sc[$plan->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $plan->status_label }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $pc[$plan->priority] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($plan->priority) }}</span>
                @if($plan->is_overdue)
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Overdue</span>
                @endif
            </div>
            <h2 class="text-lg font-semibold text-gray-900 leading-tight">{{ $plan->title }}</h2>
            @if($plan->description)
            <p class="text-sm text-gray-500 mt-1">{{ $plan->description }}</p>
            @endif
        </div>
        <a href="{{ route('plans.index') }}"
           class="flex-shrink-0 px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            ← Back
        </a>
    </div>

    @if(session('message'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Progress + Tasks + Comments + Attachments --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Progress --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $plan->progress }}%</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all {{ $plan->progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                         style="width:{{ $plan->progress }}%"></div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    {{ $plan->tasks->where('status','completed')->count() }} / {{ $plan->tasks->count() }} tasks completed
                </div>
            </div>

            {{-- Tasks (read-only) --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Tasks ({{ $plan->tasks->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($plan->tasks->sortBy('sort_order') as $task)
                    <div class="px-4 py-3 flex items-start gap-3">
                        <div class="mt-0.5 w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0
                                    {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300' }}">
                            @if($task->status === 'completed')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-800 {{ $task->status==='completed' ? 'line-through text-gray-400' : '' }}">
                                    {{ $task->title }}
                                </span>
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs {{ $pc[$task->priority] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @if($task->is_overdue)
                                <span class="text-xs text-red-500 font-medium">Overdue</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5 flex gap-3">
                                @if($task->assignedTo)
                                    @php $isMe = $task->assignedTo->user_id === auth()->id(); @endphp
                                    <span class="{{ $isMe ? 'text-blue-600 font-medium' : '' }}">
                                        {{ $isMe ? 'You' : $task->assignedTo->full_name }}
                                    </span>
                                @endif
                                @if($task->due_date)<span>Due {{ $task->due_date->format('M d') }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="px-4 py-6 text-sm text-gray-400 text-center">No tasks yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Comments --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Comments ({{ $plan->comments->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($plan->comments as $comment)
                    <div class="px-4 py-3 flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-blue-700">
                            {{ strtoupper(substr($comment->user->name,0,1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-xs font-medium text-gray-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->body }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="px-4 py-6 text-sm text-gray-400 text-center">No comments yet.</p>
                    @endforelse
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('plans.comments.store', $plan) }}">
                        @csrf
                        <textarea name="body" rows="2" placeholder="Write a comment…" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none mb-2"></textarea>
                        <button type="submit"
                                class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                            Post
                        </button>
                    </form>
                </div>
            </div>

            {{-- Attachments (download only) --}}
            @if($plan->attachments->count())
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-800">Attachments ({{ $plan->attachments->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($plan->attachments as $att)
                    <div class="px-4 py-2.5 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0 text-xs font-bold uppercase">
                            {{ pathinfo($att->original_name, PATHINFO_EXTENSION) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-800 truncate">{{ $att->original_name }}</div>
                            <div class="text-xs text-gray-400">{{ number_format($att->file_size/1024, 1) }} KB</div>
                        </div>
                        <a href="{{ route('admin.plans.attachments.download', [$plan, $att]) }}"
                           class="text-xs text-blue-600 hover:text-blue-800 font-medium flex-shrink-0">
                            Download
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Details sidebar --}}
        <div class="space-y-4">

            <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Details</h3>
                @php
                    $rows = [
                        ['label' => 'Category',  'value' => ucfirst($plan->category) . ' Plan'],
                        ['label' => 'Owner',      'value' => $plan->owner->name],
                        ['label' => 'Department', 'value' => $plan->department?->name ?? '—'],
                        ['label' => 'Project',    'value' => $plan->project?->name ?? '—'],
                        ['label' => 'Start Date', 'value' => $plan->start_date?->format('M d, Y') ?? '—'],
                        ['label' => 'Due Date',   'value' => $plan->due_date?->format('M d, Y') ?? '—'],
                        ['label' => 'Est. Hours', 'value' => $plan->estimated_hours ? $plan->estimated_hours . 'h' : '—'],
                    ];
                @endphp
                @foreach($rows as $row)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 text-xs">{{ $row['label'] }}</span>
                    <span class="text-gray-800 text-xs font-medium text-right">{{ $row['value'] }}</span>
                </div>
                @endforeach
            </div>

            @if($plan->employees->count())
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2 mb-3">
                    Team ({{ $plan->employees->count() }})
                </h3>
                <div class="space-y-1.5">
                    @foreach($plan->employees as $emp)
                    @php $isMe = $emp->user_id === auth()->id(); @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold
                                    {{ $isMe ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-700' }}">
                            {{ strtoupper(substr($emp->full_name,0,1)) }}
                        </div>
                        <span class="text-xs {{ $isMe ? 'text-blue-700 font-medium' : 'text-gray-700' }}">
                            {{ $emp->full_name }}{{ $isMe ? ' (You)' : '' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($plan->tags && count($plan->tags))
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2 mb-3">Tags</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($plan->tags as $tag)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($plan->notes)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2 mb-2">Notes</h3>
                <p class="text-xs text-gray-600 whitespace-pre-line">{{ $plan->notes }}</p>
            </div>
            @endif
        </div>
    </div>

@endsection
