@extends('layouts.admin')
@section('title', __('admin.users.edit') . ': ' . $user->username)

@section('content')
<div class="max-w-lg space-y-5">

    {{-- Edit form --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.username_label') }}</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('username') border-red-400 @enderror">
                @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.name_label') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.role_label') }}</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="viewer" @selected($user->role==='viewer')>{{ __('admin.roles.role_viewer') }}</option>
                        <option value="editor" @selected($user->role==='editor')>{{ __('admin.roles.role_editor') }}</option>
                        <option value="admin"  @selected($user->role==='admin')>{{ __('admin.roles.role_admin') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.status_label') }}</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="active"   @selected($user->status==='active')>{{ __('common.status_active') }}</option>
                        <option value="inactive" @selected($user->status==='inactive')>{{ __('common.status_inactive') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('admin.users.storage_quota') }}
                    <span class="text-gray-400 font-normal text-xs ml-1">— {{ __('admin.users.storage_hint') }}</span>
                </label>
                @php
                    $usedBytes = $user->storageUsedBytes();
                    $usedMb    = round($usedBytes / 1048576, 1);
                @endphp
                <input type="number" name="storage_quota_mb" min="0" max="102400"
                       value="{{ old('storage_quota_mb', $user->storage_quota_mb) }}"
                       placeholder="Unlimited"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('storage_quota_mb') border-red-400 @enderror">
                @error('storage_quota_mb') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-400">Currently using {{ $usedMb }} MB</p>
            </div>

        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('common.save_changes') }}
        </button>
    </form>

    {{-- Reset password --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">{{ __('admin.users.reset_password') }}</h3>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex gap-2">
            @csrf @method('PATCH')
            <input type="password" name="password" placeholder="New password (min 8 chars)" required minlength="8"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                {{ __('common.reset') }}
            </button>
        </form>
    </div>

</div>
@endsection
