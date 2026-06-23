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
