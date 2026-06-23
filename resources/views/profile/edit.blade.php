@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-lg space-y-5">

    <h1 class="text-xl font-bold text-gray-800">My Profile</h1>

    <form method="POST" action="{{ route('profile.update') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" value="{{ $user->username }}" disabled
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400">
            <p class="mt-1 text-xs text-gray-400">Username cannot be changed.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('name') border-red-400 @enderror">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                New Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span>
            </label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm @error('password') border-red-400 @enderror">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Save Changes
        </button>
    </form>

    {{-- Notification preferences --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-800">Notification Preferences</h3>
        @php
            $notifFields = [
                'notify_file_uploaded'     => 'New document uploaded',
                'notify_version_updated'   => 'Document version updated',
                'notify_access_denied'     => 'Access denied events',
                'notify_doc_approved'      => 'Document approved / rejected',
                'notify_account_activated' => 'Account status changes',
            ];
        @endphp
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-2">
            @csrf @method('PATCH')
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="view_mode" value="{{ $prefs->view_mode ?? 'grid' }}">
            @foreach($notifFields as $field => $label)
            <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="{{ $field }}" value="1"
                       {{ ($prefs->$field ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                {{ $label }}
            </label>
            @endforeach
            <button type="submit" class="mt-3 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                Save Preferences
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
        <h3 class="font-semibold text-red-700 mb-2">Delete Account</h3>
        <p class="text-sm text-gray-500 mb-4">Permanent — cannot be undone.</p>
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Permanently delete your account?')">
            @csrf @method('DELETE')
            <button class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                Delete My Account
            </button>
        </form>
    </div>

</div>
@endsection
