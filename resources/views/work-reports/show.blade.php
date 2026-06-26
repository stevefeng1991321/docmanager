@extends('layouts.app')
@section('title', $workReport->title)

@php
    $me = auth()->user();
    $canEdit = $workReport->canBeEditedBy($me);
    $canReview = $workReport->canBeReviewedBy($me) && in_array($workReport->status, ['submitted', 'under_review']);
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                        {{ match($workReport->status) {
                            'draft' => 'bg-gray-100 text-gray-600',
                            'submitted' => 'bg-blue-100 text-blue-700',
                            'under_review' => 'bg-amber-100 text-amber-700',
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                        } }}">
                        {{ str_replace('_', ' ', $workReport->status) }}
                    </span>
                    <span class="text-xs text-gray-400 capitalize">{{ $workReport->type }} report</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">{{ $workReport->title }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $workReport->employee->full_name }} · {{ $workReport->report_date->format('M j, Y') }}
                    @if($workReport->project) · {{ $workReport->project->name }} @endif
                </p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                @if($canEdit)
                    <a href="{{ route('work-reports.edit', $workReport) }}" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Edit</a>
                    @if($workReport->isDraft() || $workReport->isRejected())
                        <form action="{{ route('work-reports.submit', $workReport) }}" method="POST">
                            @csrf
                            <button class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Submit</button>
                        </form>
                    @endif
                    <form action="{{ route('work-reports.destroy', $workReport) }}" method="POST" onsubmit="return confirm('Delete this report?')">
                        @csrf @method('DELETE')
                        <button class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition">Delete</button>
                    </form>
                @endif
                @if($workReport->isOwnedBy($me))
                    <form action="{{ route('work-reports.duplicate', $workReport) }}" method="POST">
                        @csrf
                        <button class="text-xs px-3 py-1.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">Duplicate</button>
                    </form>
                @endif
                <a href="{{ route('work-reports.index') }}" class="text-xs px-3 py-1.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">← Reports</a>
            </div>
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100 text-sm">
            <div><dt class="text-gray-400 text-xs">Work Hours</dt><dd class="text-gray-700">{{ $workReport->work_hours ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Progress</dt><dd class="text-gray-700">{{ $workReport->overall_progress !== null ? $workReport->overall_progress.'%' : '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Client</dt><dd class="text-gray-700">{{ $workReport->client_name ?? '—' }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Submitted</dt><dd class="text-gray-700">{{ $workReport->submitted_at?->format('M j, Y') ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Summary --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 class="text-sm font-semibold text-gray-700">Summary</h2>
        @foreach(['tasks_completed' => 'Tasks Completed', 'task_descriptions' => 'Task Descriptions', 'challenges' => 'Challenges / Issues', 'solutions' => 'Solutions Implemented', 'notes' => 'Additional Notes'] as $field => $label)
            @if($workReport->$field)
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase mb-1">{{ $label }}</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-wrap">{{ $workReport->$field }}</dd>
            </div>
            @endif
        @endforeach
    </div>

    {{-- Task Tracking --}}
    @if($workReport->tasks->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100"><h2 class="text-sm font-semibold text-gray-700">Task Tracking</h2></div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Task</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">% Complete</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Hours</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($workReport->tasks as $task)
                <tr>
                    <td class="px-4 py-2 text-gray-700">{{ $task->title }}</td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                            {{ match($task->status) { 'completed' => 'bg-green-100 text-green-700', 'in_progress' => 'bg-blue-100 text-blue-700', 'planned' => 'bg-gray-100 text-gray-600' } }}">
                            {{ str_replace('_',' ',$task->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium capitalize
                            {{ match($task->priority) { 'high' => 'bg-red-100 text-red-700', 'medium' => 'bg-yellow-100 text-yellow-700', 'low' => 'bg-gray-100 text-gray-600' } }}">
                            {{ $task->priority }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-gray-500">{{ $task->completion_percent }}%</td>
                    <td class="px-4 py-2 text-gray-500">{{ $task->time_spent_hours ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Attachments --}}
    <div x-data="{ previewSrc: null, previewTitle: '' }" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Attachments</h2>

        <form action="{{ route('work-reports.attachments.store', $workReport) }}" method="POST" enctype="multipart/form-data"
              class="flex flex-wrap items-end gap-3 mb-4 pb-4 border-b border-gray-100">
            @csrf
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                <input type="text" name="title" required placeholder="e.g. Screenshot of dashboard"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">File</label>
                <input type="file" name="file" required class="text-sm">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">Upload</button>
        </form>
        @error('file') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

        <div class="divide-y divide-gray-50">
            @forelse($workReport->attachments as $doc)
            <div class="flex items-center justify-between py-2.5">
                <div class="flex items-center gap-3">
                    @if($doc->isImage())
                        <button type="button"
                                @click="previewSrc = {{ \Illuminate\Support\Js::from(route('work-reports.attachments.preview', [$workReport, $doc])) }}; previewTitle = {{ \Illuminate\Support\Js::from($doc->title) }}"
                                class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0 hover:opacity-80 transition">
                            <img src="{{ route('work-reports.attachments.preview', [$workReport, $doc]) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover">
                        </button>
                    @else
                        <div class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <div class="text-sm text-gray-800">{{ $doc->title }}</div>
                        <div class="text-xs text-gray-400">uploaded {{ $doc->created_at->format('M j, Y') }} by {{ $doc->uploadedBy?->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('work-reports.attachments.download', [$workReport, $doc]) }}" class="text-xs text-blue-600 hover:text-blue-800">Download</a>
                    <form action="{{ route('work-reports.attachments.destroy', [$workReport, $doc]) }}" method="POST" onsubmit="return confirm('Delete this attachment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-center text-xs text-gray-400 py-4">No attachments yet.</p>
            @endforelse
        </div>

        {{-- Image lightbox --}}
        <div x-show="previewSrc !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
             @keydown.escape.window="previewSrc = null" @click="previewSrc = null">
            <div class="max-w-3xl max-h-[85vh]" @click.stop>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-white" x-text="previewTitle"></span>
                    <button type="button" @click="previewSrc = null" class="text-white/80 hover:text-white text-sm">✕ Close</button>
                </div>
                <img :src="previewSrc" :alt="previewTitle" class="max-w-full max-h-[75vh] rounded-lg shadow-xl">
            </div>
        </div>
    </div>

    {{-- Review actions --}}
    @if($canReview)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Review</h2>
        <form action="{{ route('work-reports.review', $workReport) }}" method="POST" class="space-y-3">
            @csrf @method('PATCH')
            <textarea name="comment" rows="2" placeholder="Optional comment for the employee…"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y"></textarea>
            <div class="flex gap-2">
                @if($workReport->isSubmitted())
                <button type="submit" name="decision" value="under_review" class="text-xs px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition">Mark Under Review</button>
                @endif
                <button type="submit" name="decision" value="approved" class="text-xs px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">Approve</button>
                <button type="submit" name="decision" value="rejected" class="text-xs px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Reject</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Comments --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Discussion</h2>

        <div class="space-y-3 mb-4">
            @forelse($workReport->comments as $comment)
            <div class="flex gap-3 {{ $comment->type !== 'comment' ? 'bg-gray-50 rounded-lg p-3' : '' }}">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase flex-shrink-0">
                    {{ substr($comment->user->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</span>
                        @if($comment->type !== 'comment')
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium bg-amber-100 text-amber-700">{{ str_replace('_',' ',$comment->type) }}</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $comment->created_at->format('M j, g:ia') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-0.5 whitespace-pre-wrap">{{ $comment->body }}</p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400">No comments yet.</p>
            @endforelse
        </div>

        <form action="{{ route('work-reports.comments.store', $workReport) }}" method="POST" class="flex gap-3 items-start">
            @csrf
            <textarea name="body" rows="2" required placeholder="Add a comment…"
                      class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y"></textarea>
            <div class="flex flex-col gap-2">
                @if($workReport->canBeReviewedBy($me))
                <select name="type" class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                    <option value="comment">Comment</option>
                    <option value="feedback">Feedback</option>
                    <option value="revision_request">Revision Request</option>
                </select>
                @endif
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">Post</button>
            </div>
        </form>
    </div>
</div>
@endsection
