@extends('layouts.admin')
@section('title', __('admin.jobs.heading'))

@section('content')
<div class="space-y-5">

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">{{ __('admin.jobs.pending') }}</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $pending }}</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">{{ __('admin.jobs.failed') }}</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $failed->total() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">{{ __('admin.jobs.failed') }}</h3>
            @if($failed->total() > 0)
            <form method="POST" action="{{ route('admin.jobs.retry-all') }}" onsubmit="return confirm('{{ __('admin.jobs.confirm_retry_all', ['n' => $failed->total()]) }}')">
                @csrf
                <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">{{ __('admin.jobs.retry_all') }}</button>
            </form>
            @endif
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.jobs.col_failed_at') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.jobs.col_queue') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.jobs.col_exception') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($failed as $job)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $job->failed_at }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $job->queue }}</td>
                    <td class="px-4 py-3 text-xs text-red-500 max-w-xs truncate">{{ Str::limit($job->exception, 120) }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.jobs.retry', $job->id) }}">
                            @csrf
                            <button class="text-xs text-blue-600 hover:text-blue-800">{{ __('admin.jobs.retry') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">{{ __('admin.jobs.no_failed') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2">{{ $failed->links() }}</div>

</div>
@endsection
