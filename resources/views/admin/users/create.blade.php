@extends('layouts.admin')
@section('title', __('admin.users.new_user'))

@section('content')
<div class="max-w-lg">
<form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
    @csrf
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.username_label') }} <span class="text-red-500">*</span></label>
            <input type="text" name="username" value="{{ old('username') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('username') border-red-400 @enderror">
            @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.name_label') }} <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('name') border-red-400 @enderror">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.password_label') }} <span class="text-red-500">*</span></label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('password') border-red-400 @enderror">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.users.role_label') }} <span class="text-red-500">*</span></label>
            <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="viewer" @selected(old('role','viewer')==='viewer')>{{ __('admin.roles.role_viewer') }}</option>
                <option value="editor" @selected(old('role')==='editor')>{{ __('admin.roles.role_editor') }}</option>
                <option value="admin"  @selected(old('role')==='admin')>{{ __('admin.roles.role_admin') }}</option>
            </select>
        </div>

    </div>
    <div class="flex gap-3">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            {{ __('admin.users.create') }}
        </button>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
            {{ __('common.cancel') }}
        </a>
    </div>
</form>
</div>
@endsection
