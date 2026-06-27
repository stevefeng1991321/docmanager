@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="max-w-xl space-y-6">

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')

        {{-- Appearance --}}
        <h3 class="font-semibold text-gray-800">Appearance</h3>
        <div>
            <p class="text-xs text-gray-500 mb-3">Choose a colour theme for the admin panel.</p>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @foreach($themes as $key => $t)
                @php $sel = ($settings['admin_theme'] ?? 'default') === $key; @endphp
                <label class="cursor-pointer group relative block">
                    <input type="radio" name="admin_theme" value="{{ $key }}" @checked($sel) class="sr-only">
                    <div class="rounded-xl overflow-hidden h-14 flex transition"
                         style="border:{{ $sel ? '2px solid '.$t['primary'] : '1px solid #e5e7eb' }}">
                        {{-- Mini sidebar --}}
                        <div class="w-8 flex-shrink-0 flex flex-col justify-center gap-1 px-1.5"
                             style="background:{{ $t['sb_bg'] }}">
                            <div class="h-1.5 rounded-sm" style="background:{{ $t['primary'] }}"></div>
                            <div class="h-1 rounded-sm" style="background:rgba(255,255,255,.15)"></div>
                            <div class="h-1 rounded-sm" style="background:rgba(255,255,255,.1)"></div>
                            <div class="h-1 rounded-sm" style="background:rgba(255,255,255,.1)"></div>
                        </div>
                        {{-- Mini content --}}
                        <div class="flex-1 bg-gray-50 flex flex-col justify-center gap-1 px-2">
                            <div class="h-1.5 rounded-full bg-gray-200 w-3/4"></div>
                            <div class="h-1 rounded-full bg-gray-100 w-full"></div>
                            <div class="h-1 rounded-full bg-gray-100 w-4/5"></div>
                        </div>
                    </div>
                    @if($sel)
                    <div class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full flex items-center justify-center text-white text-[9px] leading-none font-bold"
                         style="background:{{ $t['primary'] }}">✓</div>
                    @endif
                    <p class="text-center text-[11px] mt-1.5 font-medium {{ $sel ? '' : 'text-gray-500' }}"
                       style="{{ $sel ? 'color:'.$t['primary'] : '' }}">{{ $t['name'] }}</p>
                </label>
                @endforeach
            </div>
        </div>

        <hr class="border-gray-100">

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

        <button type="submit" class="px-6 py-2.5 btn-primary text-sm font-semibold rounded-lg">
            Save Settings
        </button>
    </form>

</div>

@push('scripts')
@php
$themesJson = collect(config('admin_themes'))->map(fn($t) => [
    'sb_bg'          => $t['sb_bg'],
    'primary'        => $t['primary'],
    'primary_dk'     => $t['primary_dk'],
    'primary_lt'     => $t['primary_lt'],
    'primary_mlt'    => $t['primary_mlt'],
    'primary_border' => $t['primary_border'],
]);
@endphp
<script>
(function () {
    var themes = @json($themesJson);
    document.querySelectorAll('input[name="admin_theme"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            var t = themes[this.value];
            if (!t) return;
            var root = document.documentElement;
            root.style.setProperty('--sb-bg',          t.sb_bg);
            root.style.setProperty('--primary',         t.primary);
            root.style.setProperty('--primary-dk',      t.primary_dk);
            root.style.setProperty('--primary-lt',      t.primary_lt);
            root.style.setProperty('--primary-mlt',     t.primary_mlt);
            root.style.setProperty('--primary-border',  t.primary_border);
        });
    });
})();
</script>
@endpush
@endsection
