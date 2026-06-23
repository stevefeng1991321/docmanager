@extends('layouts.admin')
@section('title', 'Audit Logs')

@section('content')
<div class="flex justify-end mb-3">
    <a href="{{ route('admin.audit-logs.export', request()->query()) }}"
       class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm rounded-lg transition">
        ↓ Export CSV
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Resource</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Details</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse ($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $log->user?->username ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">{{ $log->action }}</span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $log->resource_id ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ json_encode($log->details) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No audit logs yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
