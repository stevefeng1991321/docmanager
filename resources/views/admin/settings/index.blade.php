@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="max-w-xl space-y-6">

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')
        <h3 class="font-semibold text-gray-800">Upload Limits</h3>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Max Upload Size (MB)</label>
                <input type="number" name="max_upload_mb" min="1" max="500"
                       value="{{ $settings['max_upload_mb'] ?? 50 }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Share Link Expiry (hours)</label>
                <input type="number" name="share_link_expiry_hours" min="1"
                       value="{{ $settings['share_link_expiry_hours'] ?? 24 }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <h3 class="font-semibold text-gray-800 pt-2">Security</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lockout Attempts</label>
                <input type="number" name="lockout_attempts" min="3"
                       value="{{ $settings['lockout_attempts'] ?? 5 }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lockout Duration (minutes)</label>
                <input type="number" name="lockout_minutes" min="1"
                       value="{{ $settings['lockout_minutes'] ?? 15 }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">Password Complexity</label>
            <div class="space-y-2">
                @foreach($passwordLevels as $key => $level)
                @php $active = ($settings['password_complexity'] ?? 'standard') === $key; @endphp
                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition
                              {{ $active ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                    <input type="radio" name="password_complexity" value="{{ $key }}"
                           @checked($active) class="mt-0.5 accent-blue-600 flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-sm font-medium text-gray-800">{{ $level['label'] }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded-full font-medium {{ $level['badge'] }}">
                                {{ $level['min'] }}+ chars
                            </span>
                            @if($level['mixedCase'])
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">Aa</span>
                            @endif
                            @if($level['numbers'])
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">0–9</span>
                            @endif
                            @if($level['symbols'])
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500">!@#</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $level['description'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-1.5">
                Applies to registration, profile password changes, and admin-managed accounts.
            </p>
        </div>

        <h3 class="font-semibold text-gray-800 pt-2">Retention</h3>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Trash Retention (days)</label>
            <input type="number" name="trash_retention_days" min="1"
                   value="{{ $settings['trash_retention_days'] ?? 30 }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
            Save Settings
        </button>
    </form>

</div>
@endsection
