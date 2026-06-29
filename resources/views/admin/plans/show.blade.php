@extends('layouts.admin')
@section('title', $plan->title)

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="text-xs text-gray-400 font-mono">{{ $plan->plan_number }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->status->badge() }}">{{ $plan->status->label() }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $plan->priority->badge() }}">{{ $plan->priority->label() }}</span>
                @if($plan->is_overdue)
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Overdue</span>
                @endif
            </div>
            <h2 class="text-lg font-semibold text-gray-900 leading-tight">{{ $plan->title }}</h2>
            @if($plan->description)
            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $plan->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('admin.plans.index') }}" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">← Back</a>
            <a href="{{ route('admin.plans.edit', $plan) }}" class="px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Edit</a>
            <form method="POST" action="{{ route('admin.plans.duplicate', $plan) }}" class="contents">
                @csrf
                <button type="submit" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Duplicate</button>
            </form>
            <form method="POST" action="{{ route('admin.plans.archive', $plan) }}" class="contents">
                @csrf @method('PATCH')
                <button type="submit" class="px-3 py-1.5 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Archive</button>
            </form>
        </div>
    </div>

    @if(session('message'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Tasks + Comments + Attachments --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Progress bar --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $plan->progress }}%</span>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all {{ $plan->progress >= 100 ? 'bg-green-500' : 'bg-blue-500' }}" style="width:{{ $plan->progress }}%"></div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    {{ $plan->tasks->where('status', \App\Enums\PlanTaskStatus::Completed)->count() }} / {{ $plan->tasks->count() }} tasks completed
                </div>
            </div>

            {{-- Tasks --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="taskPanel()">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Tasks ({{ $plan->tasks->count() }})</h3>
                    <button @click="showAdd = !showAdd" class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Add Task</button>
                </div>

                {{-- Add task form --}}
                <div x-show="showAdd" x-cloak class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <form method="POST" action="{{ route('admin.plans.tasks.store', $plan) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <input type="text" name="title" placeholder="Task title…" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Unassigned</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    @foreach(['low','medium','high','critical'] as $p)
                                    <option value="{{ $p }}" @selected($p==='medium')>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="date" name="due_date"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <button type="button" @click="showAdd=false" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Add Task</button>
                        </div>
                    </form>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($plan->tasks->sortBy('sort_order') as $task)
                    <div class="px-4 py-3 flex items-start gap-3 hover:bg-gray-50 transition" id="task-{{ $task->id }}">
                        {{-- Toggle --}}
                        <form method="POST" action="{{ route('admin.plans.tasks.toggle', [$plan, $task]) }}" class="mt-0.5 flex-shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-5 h-5 rounded border-2 flex items-center justify-center transition {{ $task->status === \App\Enums\PlanTaskStatus::Completed ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 hover:border-green-400' }}">
                                @if($task->status === \App\Enums\PlanTaskStatus::Completed)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>
                        </form>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-800 {{ $task->status === \App\Enums\PlanTaskStatus::Completed ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</span>
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs {{ $task->priority->badge() }}">{{ $task->priority->label() }}</span>
                                @if($task->is_overdue)
                                <span class="text-xs text-red-500 font-medium">Overdue</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5 flex gap-3">
                                @if($task->assignedTo) <span>{{ $task->assignedTo->full_name }}</span> @endif
                                @if($task->due_date) <span>Due {{ $task->due_date->format('M d') }}</span> @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.plans.tasks.destroy', [$plan, $task]) }}"
                              onsubmit="return confirm('Delete task?')" class="flex-shrink-0">
                            @csrf @method('DELETE')
                            <button class="text-gray-300 hover:text-red-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
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
                    @foreach($plan->comments as $comment)
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
                        @if($comment->user_id === auth()->id())
                        <form method="POST" action="{{ route('admin.plans.comments.destroy', [$plan, $comment]) }}"
                              onsubmit="return confirm('Delete comment?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-300 hover:text-red-400 transition text-xs">✕</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.plans.comments.store', $plan) }}">
                        @csrf
                        <textarea name="body" rows="2" placeholder="Write a comment…" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none mb-2"></textarea>
                        <button type="submit" class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Post</button>
                    </form>
                </div>
            </div>

            {{-- Attachments --}}
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
                           class="text-xs text-blue-600 hover:text-blue-800 font-medium">Download</a>
                        <form method="POST" action="{{ route('admin.plans.attachments.destroy', [$plan, $att]) }}"
                              onsubmit="return confirm('Delete attachment?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-300 hover:text-red-400 transition text-xs">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('admin.plans.attachments.store', $plan) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex gap-2">
                            <input type="file" name="file" required
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-500 file:mr-3 file:border-0 file:bg-blue-50 file:text-blue-700 file:text-xs file:font-medium file:px-2 file:py-1 file:rounded focus:outline-none">
                            <button type="submit" class="px-4 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex-shrink-0">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Details sidebar --}}
        <div class="space-y-4">

            {{-- Details --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2">Details</h3>
                @php
                    $rows = [
                        ['label' => 'Category',   'value' => ucfirst($plan->category) . ' Plan'],
                        ['label' => 'Owner',       'value' => $plan->owner->name],
                        ['label' => 'Department',  'value' => $plan->department?->name ?? '—'],
                        ['label' => 'Project',     'value' => $plan->project?->name ?? '—'],
                        ['label' => 'Start Date',  'value' => $plan->start_date?->format('M d, Y') ?? '—'],
                        ['label' => 'Due Date',    'value' => $plan->due_date?->format('M d, Y') ?? '—'],
                        ['label' => 'Est. Hours',  'value' => $plan->estimated_hours ? $plan->estimated_hours . 'h' : '—'],
                        ['label' => 'Actual Hours','value' => $plan->actual_hours ? $plan->actual_hours . 'h' : '—'],
                    ];
                @endphp
                @foreach($rows as $row)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 text-xs">{{ $row['label'] }}</span>
                    <span class="text-gray-800 text-xs font-medium text-right">{{ $row['value'] }}</span>
                </div>
                @endforeach
            </div>

            {{-- Assigned employees --}}
            @if($plan->employees->count())
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2 mb-3">Assigned ({{ $plan->employees->count() }})</h3>
                <div class="space-y-1.5">
                    @foreach($plan->employees as $emp)
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-xs font-semibold text-blue-700">
                            {{ strtoupper(substr($emp->full_name,0,1)) }}
                        </div>
                        <span class="text-xs text-gray-700">{{ $emp->full_name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
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

            {{-- Notes --}}
            @if($plan->notes)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-800 border-b border-gray-100 pb-2 mb-2">Notes</h3>
                <p class="text-xs text-gray-600 whitespace-pre-line">{{ $plan->notes }}</p>
            </div>
            @endif

            {{-- Danger zone --}}
            <div class="bg-white rounded-xl border border-red-100 p-4">
                <h3 class="text-xs font-semibold text-red-600 mb-3">Danger Zone</h3>
                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                      onsubmit="return confirm('Permanently delete this plan and all its tasks?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                        Delete Plan
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
function taskPanel() {
    return { showAdd: false }
}
</script>

@endsection
