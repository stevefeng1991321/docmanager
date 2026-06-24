@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-lg space-y-5">

    <h1 class="text-xl font-bold text-gray-800">My Profile</h1>

    {{-- Avatar + main details --}}
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf @method('PATCH')

        {{-- Avatar --}}
        <div class="flex items-center gap-4">
            @php $avatar = $prefs->avatar ?? null; @endphp
            <div id="avatar-preview-wrap">
                @if($avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . $avatar) }}" alt="Avatar"
                         class="w-16 h-16 rounded-full object-cover border border-gray-200">
                @else
                    <div id="avatar-initials" class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-2xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <img id="avatar-preview" src="" alt="Avatar"
                         class="w-16 h-16 rounded-full object-cover border border-gray-200 hidden">
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                <input type="file" name="avatar" id="avatar-input" accept="image/*"
                       class="block text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-400">JPG, PNG, WEBP — max 5 MB</p>
            </div>
        </div>
        <script>
            document.getElementById('avatar-input').addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const preview = document.getElementById('avatar-preview');
                const initials = document.getElementById('avatar-initials');
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                if (initials) initials.classList.add('hidden');
            });
        </script>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" value="{{ $user->username }}" disabled
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400">
            <p class="mt-1 text-xs text-gray-400">Username cannot be changed directly — use the request form below.</p>
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
            <p class="mt-1 text-xs text-gray-400">{{ \App\Support\PasswordPolicy::description() }}</p>
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

    {{-- Storage usage --}}
    @php
        $usedBytes  = $user->storageUsedBytes();
        $quotaBytes = $user->storageQuotaBytes();
        $usedMb     = round($usedBytes / 1048576, 1);
        $quotaMb    = $quotaBytes ? round($quotaBytes / 1048576) : null;
        $pct        = $quotaBytes ? min(100, round($usedBytes / $quotaBytes * 100)) : 0;
        $barColor   = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-blue-500');
    @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-3">
        <h3 class="font-semibold text-gray-800">Storage Usage</h3>
        @if($quotaBytes)
            <div class="flex justify-between text-xs text-gray-500">
                <span>{{ $usedMb }} MB used</span>
                <span>{{ $quotaMb }} MB quota</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div class="{{ $barColor }} h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-xs text-gray-400">{{ $pct }}% of your quota used</p>
        @else
            <p class="text-sm text-gray-500">{{ $usedMb }} MB used &mdash; <span class="text-green-600 font-medium">Unlimited quota</span></p>
        @endif
    </div>

    {{-- Username change request --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-800">Request Username Change</h3>
        <p class="text-sm text-gray-500">Submit a request — an admin will review and apply it.</p>
        <form method="POST" action="{{ route('profile.request-username-change') }}" class="flex gap-3">
            @csrf
            <input type="text" name="new_username" placeholder="New username"
                   pattern="[a-zA-Z0-9_\-]{3,50}" title="3–50 characters: letters, numbers, underscores, hyphens"
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm @error('new_username') border-red-400 @enderror">
            <button type="submit" class="px-5 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition">
                Submit Request
            </button>
        </form>
        @error('new_username') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Account deletion request --}}
    <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-red-700">Request Account Deletion</h3>
        <p class="text-sm text-gray-500">
            Submits a deletion request for admin review. Your account will not be removed until an admin approves it.
        </p>
        <form method="POST" action="{{ route('profile.request-deletion') }}" class="space-y-3">
            @csrf
            <textarea name="reason" rows="2" placeholder="Reason (optional)"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            <button type="submit"
                    onclick="return confirm('Submit account deletion request?')"
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                Submit Deletion Request
            </button>
        </form>
    </div>

</div>
@endsection
