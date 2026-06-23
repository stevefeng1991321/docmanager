@extends('layouts.admin')
@section('title', 'Roles & Permissions')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-64">Permission</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Admin</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Editor</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Viewer</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php
            $yes = '<span class="text-green-500 text-base">✔</span>';
            $no  = '<span class="text-gray-300 text-base">✗</span>';
            $rows = [
                ['Upload documents',              true,  true,  false],
                ['Edit / delete documents',        true,  true,  false],
                ['Download documents',             true,  true,  true ],
                ['View / preview documents',       true,  true,  true ],
                ['Search',                         true,  true,  true ],
                ['Favorites & bookmarks',          true,  true,  true ],
                ['Manage tags & categories',       true,  true,  false],
                ['Manage document versions',       true,  true,  false],
                ['Lock / unlock documents',        true,  true,  false],
                ['View analytics',                 true,  true,  false],
                ['Export logs (CSV)',               true,  false, false],
                ['Manage users & roles',           true,  false, false],
                ['View audit logs',                true,  false, false],
                ['Manage jobs / queue',            true,  false, false],
                ['Manage storage quotas',          true,  false, false],
                ['Re-index search',                true,  false, false],
                ['System settings',                true,  false, false],
            ];
            @endphp
            @foreach($rows as [$label, $admin, $editor, $viewer])
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-2.5 text-gray-700">{{ $label }}</td>
                <td class="px-5 py-2.5 text-center">{!! $admin  ? $yes : $no !!}</td>
                <td class="px-5 py-2.5 text-center">{!! $editor ? $yes : $no !!}</td>
                <td class="px-5 py-2.5 text-center">{!! $viewer ? $yes : $no !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<p class="mt-3 text-xs text-gray-400">Role assignments are managed per user via <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:underline">Users</a>.</p>
@endsection
