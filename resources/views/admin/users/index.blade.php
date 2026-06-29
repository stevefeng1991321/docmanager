@extends('layouts.admin')
@section('title', __('admin.users.heading'))

@section('content')

<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.users.search_placeholder') }}"
               class="w-56 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
        <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('admin.users.filter_all') }}</option>
            <option value="admin"  @selected(request('role') === 'admin')>{{ __('admin.roles.role_admin') }}</option>
            <option value="editor" @selected(request('role') === 'editor')>{{ __('admin.roles.role_editor') }}</option>
            <option value="viewer" @selected(request('role') === 'viewer')>{{ __('admin.roles.role_viewer') }}</option>
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">{{ __('common.all_status') }}</option>
            <option value="active"   @selected(request('status') === 'active')>{{ __('common.status_active') }}</option>
            <option value="pending"  @selected(request('status') === 'pending')>{{ __('common.status_pending') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('common.status_inactive') }}</option>
        </select>
        <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">{{ __('common.filter') }}</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
        + {{ __('admin.users.new_user') }}
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.username_label') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.name_label') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.col_role') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.col_status') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.col_last_login') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.users.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $user->username }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->name }}</td>
                        <td class="px-6 py-3">
                            @php $roleColors = ['admin' => 'bg-purple-100 text-purple-700', 'editor' => 'bg-blue-100 text-blue-700', 'viewer' => 'bg-gray-100 text-gray-600']; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $user->status->badge() }}">{{ $user->status->label() }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-400">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline text-xs">{{ __('admin.users.edit_action') }}</a>
                                @if($user->isActive())
                                    <form action="{{ route('admin.users.deactivate', $user) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-red-600 hover:underline text-xs">{{ __('admin.users.deactivate') }}</button>
                                    </form>
                                @elseif($user->status === \App\Enums\UserStatus::Inactive)
                                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-green-600 hover:underline text-xs">{{ __('admin.users.activate') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('admin.users.no_users') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

@endsection
