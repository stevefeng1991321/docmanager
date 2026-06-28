@extends('layouts.admin')
@section('title', __('admin.backup.heading'))

@section('content')
<div class="space-y-6" x-data="{ confirmRestore: null, confirmDelete: null }">

    {{-- Create Backup --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-gray-800">{{ __('admin.backup.create_backup') }}</h2>
                <p class="text-sm text-gray-500 mt-1">Export the current database to a <code>.sql</code> file stored on the server.</p>
            </div>
            <form method="POST" action="{{ route('admin.backup.store') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('admin.backup.backup_now') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Existing Backups --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">{{ __('admin.backup.heading') }}</h3>
            <span class="text-xs text-gray-400">{{ count($backups) }} file(s)</span>
        </div>

        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.backup.col_filename') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.backup.col_size') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('admin.backup.col_created') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('admin.backup.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($backups as $backup)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $backup['filename'] }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ number_format($backup['size'] / 1048576, 2) }} MB</td>
                    <td class="px-4 py-3 text-gray-500">{{ date('Y-m-d H:i:s', $backup['created_at']) }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <a href="{{ route('admin.backup.download', $backup['filename']) }}"
                               class="text-xs px-3 py-1.5 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                {{ __('admin.backup.download') }}
                            </a>
                            <button type="button"
                                    @click="confirmRestore = '{{ $backup['filename'] }}'"
                                    class="text-xs px-3 py-1.5 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 transition">
                                {{ __('admin.backup.restore') }}
                            </button>
                            <button type="button"
                                    @click="confirmDelete = '{{ $backup['filename'] }}'"
                                    class="text-xs px-3 py-1.5 rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                                {{ __('admin.backup.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-400">{{ __('admin.backup.no_backups') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Restore from Upload --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800">{{ __('admin.backup.restore') }}</h2>
        <p class="text-sm text-gray-500 mt-1 mb-4">Upload a <code>.sql</code> file exported from this or another compatible MySQL database.</p>

        <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg mb-4">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm text-amber-700">
                <strong>{{ __('common.warning') }}</strong> {{ __('admin.backup.warning') }}
                Create a backup first if you need to preserve the current state.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.backup.restore-upload') }}"
              enctype="multipart/form-data"
              x-data="{ file: null }"
              @submit.prevent="
                  if (!file) return;
                  if (!confirm('Restore from ' + file + '? This will overwrite all current data and cannot be undone.')) return;
                  $el.submit();
              ">
            @csrf
            <div class="flex items-center gap-3">
                <input type="file"
                       name="backup_file"
                       accept=".sql"
                       @change="file = $event.target.files[0]?.name"
                       class="block text-sm text-gray-500
                              file:mr-3 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-gray-100 file:text-gray-700
                              hover:file:bg-gray-200 file:transition">
                <button type="submit"
                        :disabled="!file"
                        class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    {{ __('admin.backup.restore') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Restore confirmation modal --}}
    <div x-show="confirmRestore"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @keydown.escape.window="confirmRestore = null">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4"
             @click.stop>
            <h3 class="text-base font-semibold text-gray-800">{{ __('admin.backup.restore') }}</h3>
            <p class="text-sm text-gray-600 mt-2">
                {{ __('admin.backup.confirm_restore', ['file' => '']) }}<span class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded" x-text="confirmRestore"></span>?
                <br><strong class="text-red-600">{{ __('admin.backup.warning') }}</strong>
            </p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button"
                        @click="confirmRestore = null"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    {{ __('common.cancel') }}
                </button>
                <form method="POST" :action="'{{ url('admin/backup') }}/' + confirmRestore + '/restore'">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        {{ __('admin.backup.restore') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div x-show="confirmDelete"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @keydown.escape.window="confirmDelete = null">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4"
             @click.stop>
            <h3 class="text-base font-semibold text-gray-800">{{ __('admin.backup.delete') }}</h3>
            <p class="text-sm text-gray-600 mt-2">
                {{ __('admin.backup.confirm_delete', ['file' => '']) }}<span class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded" x-text="confirmDelete"></span>?
            </p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button"
                        @click="confirmDelete = null"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    {{ __('common.cancel') }}
                </button>
                <form method="POST" :action="'{{ url('admin/backup') }}/' + confirmDelete">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        {{ __('admin.backup.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
