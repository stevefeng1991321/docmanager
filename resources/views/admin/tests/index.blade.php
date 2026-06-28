@extends('layouts.admin')
@section('title', 'Developer Tests')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ __('admin.tests.heading') }}</h1>
        </div>
        <a href="{{ route('admin.tests.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            + {{ __('admin.tests.new_test') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.col_title') }}</th>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.col_status') }}</th>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.col_questions') }}</th>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.time_limit_label') }}</th>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.col_attempts') }}</th>
                    <th class="px-4 py-2.5 text-left">{{ __('admin.tests.col_actions') }}</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tests as $test)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.tests.show', $test) }}" class="font-medium text-gray-800 hover:text-blue-600">
                            {{ $test->title }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize
                            {{ match($test->status) {
                                'active'   => 'bg-green-100 text-green-700',
                                'draft'    => 'bg-gray-100 text-gray-600',
                                'archived' => 'bg-yellow-100 text-yellow-700',
                            } }}">
                            {{ $test->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $test->problems_count }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $test->time_limit_minutes }} min</td>
                    <td class="px-4 py-3 text-gray-600">{{ $test->invites_count }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $test->created_at->format('M j, Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.tests.edit', $test) }}" class="text-xs text-gray-500 hover:text-blue-600 mr-3">{{ __('admin.tests.edit_action') }}</a>
                        <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" class="inline"
                              onsubmit="return confirm('{{ __('admin.tests.confirm_delete', ['title' => '']) }}' + '{{ addslashes($test->title) }}' + '?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">{{ __('admin.tests.delete_action') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                        {{ __('admin.tests.no_tests') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
