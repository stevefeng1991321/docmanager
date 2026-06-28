@extends('layouts.admin')
@section('title', $workReport->title)

@php
    $statusStyle = match($workReport->status) {
        'draft'        => 'bg-gray-100 text-gray-600',
        'submitted'    => 'bg-blue-100 text-blue-700',
        'under_review' => 'bg-amber-100 text-amber-700',
        'approved'     => 'bg-green-100 text-green-700',
        'rejected'     => 'bg-red-100 text-red-700',
        default        => 'bg-gray-100 text-gray-600',
    };
    $canReview = in_array($workReport->status, ['submitted', 'under_review']);
@endphp

@section('content')
<div class="space-y-5 max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.work-reports.index') }}" class="hover:text-blue-600 transition-colors">{{ __('admin.work_reports.heading') }}</a>
        <span>/</span>
        <span class="text-gray-700 truncate">{{ $workReport->title }}</span>
    </div>

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize {{ $statusStyle }}">
                        {{ str_replace('_', ' ', $workReport->status) }}
                    </span>
                    <span class="text-xs text-gray-400 capitalize">{{ $workReport->type }} report</span>
                    @if($workReport->project)
                        <span class="text-xs text-gray-400">· {{ $workReport->project->name }}</span>
                    @endif
                </div>
                <h1 class="text-lg font-semibold text-gray-900 leading-snug">{{ $workReport->title }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $workReport->employee->full_name }}
                    @if($workReport->employee->department)
                        · {{ $workReport->employee->department->name }}
                    @endif
                    @if($workReport->employee->manager)
                        · Manager: {{ $workReport->employee->manager->full_name }}
                    @endif
                    · {{ $workReport->report_date->format('M j, Y') }}
                </p>
            </div>
            <a href="{{ route('work-reports.show', $workReport) }}" target="_blank"
               class="flex items-center gap-1.5 text-xs px-3 py-1.5 border border-gray-200 rounded-lg text-gray-500 hover:text-blue-600 hover:border-blue-300 transition flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Employee View
            </a>
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-3 mt-4 pt-4 border-t border-gray-100 text-sm">
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Work Hours</dt>
                <dd class="font-medium text-gray-700">{{ $workReport->work_hours ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Progress</dt>
                <dd class="font-medium text-gray-700">
                    @if($workReport->overall_progress !== null)
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5 max-w-[80px]">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width:{{ $workReport->overall_progress }}%"></div>
                            </div>
                            <span>{{ $workReport->overall_progress }}%</span>
                        </div>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Client</dt>
                <dd class="font-medium text-gray-700">{{ $workReport->client_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Submitted</dt>
                <dd class="font-medium text-gray-700">{{ $workReport->submitted_at?->format('M j, Y') ?? '—' }}</dd>
            </div>
            @if($workReport->reviewed_at)
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Reviewed</dt>
                <dd class="font-medium text-gray-700">{{ $workReport->reviewed_at->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Reviewed By</dt>
                <dd class="font-medium text-gray-700">{{ $workReport->reviewedBy?->name ?? '—' }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Review panel (prominent — shown only when actionable) --}}
    @if($canReview)
    <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Review Decision
        </h2>
        <form action="{{ route('admin.work-reports.review', $workReport) }}" method="POST" class="space-y-3">
            @csrf @method('PATCH')
            <textarea name="comment" rows="2" placeholder="Optional comment for the employee…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
            <div class="flex flex-wrap gap-2">
                @if($workReport->status === 'submitted')
                <button type="submit" name="decision" value="under_review"
                        class="text-xs px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg transition font-medium">
                    Mark Under Review
                </button>
                @endif
                <button type="submit" name="decision" value="approved"
                        class="text-xs px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium">
                    {{ __('admin.work_reports.approve_action') }}
                </button>
                <button type="submit" name="decision" value="rejected"
                        class="text-xs px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition font-medium">
                    {{ __('admin.work_reports.reject_action') }}
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Summary --}}
    @php
        $summaryFields = [
            'tasks_completed'    => 'Tasks Completed',
            'task_descriptions'  => 'Task Descriptions',
            'challenges'         => 'Challenges / Issues',
            'solutions'          => 'Solutions Implemented',
            'notes'              => 'Additional Notes',
        ];
        $hasAnySummary = collect($summaryFields)->keys()->some(fn ($f) => !empty($workReport->$f));
    @endphp
    @if($hasAnySummary)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
        <h2 class="text-sm font-semibold text-gray-800">{{ __('common.summary') }}</h2>
        @foreach($summaryFields as $field => $label)
            @if(!empty($workReport->$field))
            <div>
                <dt class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">{{ $label }}</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ $workReport->$field }}</dd>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    {{-- Task Tracking --}}
    @if($workReport->tasks->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">Task Tracking
                <span class="ml-1.5 text-xs font-normal text-gray-400">({{ $workReport->tasks->count() }} tasks)</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Task</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Complete</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Hours</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($workReport->tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-700">{{ $task->title }}</td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                                {{ match($task->status) {
                                    'completed'   => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    default       => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                                {{ match($task->priority) {
                                    'high'   => 'bg-red-100 text-red-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    default  => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ $task->priority }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full" style="width:{{ $task->completion_percent }}%"></div>
                                </div>
                                <span class="text-xs">{{ $task->completion_percent }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $task->time_spent_hours ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Attachments --}}
    @if($workReport->attachments->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5"
         x-data="{ previewSrc: null, previewTitle: '' }">
        <h2 class="text-sm font-semibold text-gray-800 mb-3">
            Attachments
            <span class="ml-1.5 text-xs font-normal text-gray-400">({{ $workReport->attachments->count() }})</span>
        </h2>
        <div class="divide-y divide-gray-50">
            @foreach($workReport->attachments as $doc)
            <div class="flex items-center justify-between py-2.5">
                <div class="flex items-center gap-3 min-w-0">
                    @if($doc->isImage())
                        <button type="button"
                                @click="previewSrc = {{ \Illuminate\Support\Js::from(route('work-reports.attachments.preview', [$workReport, $doc])) }}; previewTitle = {{ \Illuminate\Support\Js::from($doc->title) }}"
                                class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 hover:opacity-75 transition">
                            <img src="{{ route('work-reports.attachments.preview', [$workReport, $doc]) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover">
                        </button>
                    @else
                        <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-sm text-gray-800 truncate">{{ $doc->title }}</div>
                        <div class="text-xs text-gray-400">
                            {{ $doc->created_at->format('M j, Y') }}
                            @if($doc->uploadedBy) · {{ $doc->uploadedBy->name }} @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('work-reports.attachments.download', [$workReport, $doc]) }}"
                   class="text-xs text-blue-600 hover:text-blue-800 flex-shrink-0 ml-4">
                    Download
                </a>
            </div>
            @endforeach
        </div>

        {{-- Image lightbox --}}
        <div x-show="previewSrc !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
             @keydown.escape.window="previewSrc = null" @click="previewSrc = null">
            <div class="max-w-3xl max-h-[85vh]" @click.stop>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-white" x-text="previewTitle"></span>
                    <button type="button" @click="previewSrc = null" class="text-white/70 hover:text-white text-sm ml-4">✕ Close</button>
                </div>
                <img :src="previewSrc" :alt="previewTitle" class="max-w-full max-h-[75vh] rounded-lg shadow-xl">
            </div>
        </div>
    </div>
    @endif

    {{-- Discussion / Comments --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Discussion</h2>

        @if($workReport->comments->isNotEmpty())
        <div class="space-y-3 mb-5">
            @foreach($workReport->comments as $comment)
            <div class="flex gap-3 {{ $comment->type !== 'comment' ? 'bg-gray-50 rounded-lg p-3' : '' }}">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-xs uppercase flex-shrink-0 mt-0.5">
                    {{ substr($comment->user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5">
                        <span class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</span>
                        @if($comment->type !== 'comment')
                        <span class="text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-medium">
                            {{ str_replace('_', ' ', $comment->type) }}
                        </span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $comment->created_at->format('M j, g:ia') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-0.5 whitespace-pre-wrap">{{ $comment->body }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 mb-5">No comments yet.</p>
        @endif

        <form action="{{ route('admin.work-reports.comments.store', $workReport) }}" method="POST"
              class="flex gap-3 items-start pt-4 border-t border-gray-100">
            @csrf
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-semibold text-xs uppercase flex-shrink-0 mt-0.5">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 space-y-2">
                <textarea name="body" rows="2" required placeholder="Add a comment, feedback, or revision request…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
                <div class="flex items-center gap-2">
                    <select name="type" class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="comment">Comment</option>
                        <option value="feedback">Feedback</option>
                        <option value="revision_request">Revision Request</option>
                    </select>
                    <button type="submit"
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                        Post
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
