@extends('layouts.admin')
@section('title', __('admin.storage.heading'))

@section('content')
<div class="space-y-5">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">{{ __('admin.storage.total_used') }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBytes / 1048576, 2) }} MB</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500">{{ __('admin.storage.by_type') }}</p>
            <div class="mt-2 space-y-1">
                @foreach ($byType as $row)
                <div class="flex justify-between text-sm text-gray-600">
                    <span class="uppercase text-xs font-medium">{{ $row->file_type ?: __('common.unknown') }}</span>
                    <span>{{ $row->count }} files — {{ number_format($row->total_size / 1048576, 1) }} MB</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ __('admin.storage.by_user') }}</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.storage.col_user') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.storage.col_size') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($byUser as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $user->username }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ number_format($user->resources_sum_file_size / 1048576, 2) }} MB</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-8 text-center text-gray-400">{{ __('admin.storage.no_quota') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $byUser->links() }}</div>
    </div>

</div>
@endsection
