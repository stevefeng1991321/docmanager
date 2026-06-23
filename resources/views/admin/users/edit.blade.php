@extends('layouts.admin')
@section('title', 'Edit User: ' . $user->username)

@section('content')
<div class="max-w-lg space-y-5">

    {{-- Edit form --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('username') border-red-400 @enderror">
                @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="viewer" @selected($user->role==='viewer')>Viewer</option>
                        <option value="editor" @selected($user->role==='editor')>Editor</option>
                        <option value="admin"  @selected($user->role==='admin')>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="active"   @selected($user->status==='active')>Active</option>
                        <option value="inactive" @selected($user->status==='inactive')>Inactive</option>
                    </select>
                </div>
            </div>

        </div>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Save Changes
        </button>
    </form>

    {{-- Reset password --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Reset Password</h3>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="flex gap-2">
            @csrf @method('PATCH')
            <input type="password" name="password" placeholder="New password (min 8 chars)" required minlength="8"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition">
                Reset
            </button>
        </form>
    </div>

</div>
@endsection
