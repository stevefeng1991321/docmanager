@php
    $r = $workReport ?? null;
    $val = fn($field, $default = null) => old($field, $r?->$field ?? $default);
    $initialTasks = old('tasks', $r?->tasks->map(fn($t) => [
        'title' => $t->title, 'status' => $t->status, 'priority' => $t->priority,
        'completion_percent' => $t->completion_percent, 'time_spent_hours' => $t->time_spent_hours,
    ])->all() ?? []);
@endphp

<div x-data="workReportForm({{ \Illuminate\Support\Js::from($initialTasks) }})" class="space-y-5">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.title') }}</label>
                <input type="text" name="title" value="{{ $val('title') }}" placeholder="e.g. Daily Progress — Checkout Redesign"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.report_type') }}</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach(['daily','weekly','monthly'] as $t)
                            <option value="{{ $t }}" @selected($val('type', 'daily') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.report_date') }}</label>
                    <input type="date" name="report_date" value="{{ $val('report_date') ? \Illuminate\Support\Carbon::parse($val('report_date'))->format('Y-m-d') : now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.project_label') }} <span class="text-gray-400">{{ __('common.optional') }}</span></label>
                <select name="project_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">{{ __('common.none') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected((string) $val('project_id') === (string) $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-gray-400">{{ __('common.optional') }}</span></label>
                <input type="text" name="client_name" value="{{ $val('client_name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.hours_worked') }}</label>
                    <input type="number" step="0.25" min="0" name="work_hours" value="{{ $val('work_hours') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Progress %</label>
                    <input type="number" min="0" max="100" name="overall_progress" value="{{ $val('overall_progress') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h2 class="text-sm font-semibold text-gray-700">{{ __('common.summary') }}</h2>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.work_summary') }}</label>
            <textarea name="tasks_completed" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('tasks_completed') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Task Descriptions</label>
            <textarea name="task_descriptions" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('task_descriptions') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('work_reports.issues') }}</label>
                <textarea name="challenges" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('challenges') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Solutions Implemented</label>
                <textarea name="solutions" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('solutions') }}</textarea>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.note') }}</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-y">{{ $val('notes') }}</textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">Task Tracking</span>
            <button type="button" @click="addTask()"
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg transition">
                + {{ __('common.add') }}
            </button>
        </div>

        <template x-for="(task, i) in tasks" :key="i">
            <div class="p-4 border-b border-gray-50 grid grid-cols-12 gap-2 items-end">
                <div class="col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Task</label>
                    <input type="text" :name="`tasks[${i}][title]`" x-model="task.title" placeholder="Task title"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select :name="`tasks[${i}][status]`" x-model="task.status" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                        <option value="planned">Planned</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
                    <select :name="`tasks[${i}][priority]`" x-model="task.priority" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">% Complete</label>
                    <input type="number" min="0" max="100" :name="`tasks[${i}][completion_percent]`" x-model.number="task.completion_percent"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Hrs</label>
                    <input type="number" step="0.25" min="0" :name="`tasks[${i}][time_spent_hours]`" x-model.number="task.time_spent_hours"
                           class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm">
                </div>
                <div class="col-span-1 text-right">
                    <button type="button" @click="removeTask(i)" class="text-xs text-red-500 hover:text-red-700">✕</button>
                </div>
            </div>
        </template>

        <p x-show="tasks.length === 0" class="px-5 py-4 text-xs text-gray-400">No tasks added yet.</p>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit" name="action" value="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('work_reports.submit_button') }}
        </button>
        <button type="submit" name="action" value="draft"
                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">
            {{ __('work_reports.save_draft') }}
        </button>
        <a href="{{ $r ? route('work-reports.show', $r) : route('work-reports.index') }}"
           class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
            {{ __('work_reports.cancel') }}
        </a>
    </div>
</div>

@push('scripts')
<script>
function workReportForm(initialTasks) {
    return {
        tasks: initialTasks,

        addTask() {
            this.tasks.push({ title: '', status: 'planned', priority: 'medium', completion_percent: 0, time_spent_hours: null });
        },

        removeTask(i) {
            this.tasks.splice(i, 1);
        },
    };
}
</script>
@endpush
