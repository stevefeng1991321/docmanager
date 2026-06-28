@extends('layouts.admin')
@section('title', 'Pending Accounts')

@section('content')

<form method="POST" id="bulkForm">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800">Pending Registrations</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} account(s) awaiting activation</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" formaction="{{ route('admin.users.bulk-activate') }}"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    {{ __('admin.users.activate') }}
                </button>
                <button type="button" onclick="showRejectModal()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    {{ __('common.reject') }}
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3"><input type="checkbox" id="selectAll" class="rounded border-gray-300"></th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.username_label') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.name_label') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_registered') }}</th>
                        <th class="px-6 py-3 text-left">{{ __('admin.users.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="row-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $user->username }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-gray-400">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-3">
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-medium transition">
                                            {{ __('admin.users.activate') }}
                                        </button>
                                    </form>
                                    <button type="button" onclick="rejectOne({{ $user->id }})"
                                            class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition">
                                        {{ __('common.reject') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">{{ __('admin.account_requests.no_requests') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>
</form>

{{-- Reject modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-data>
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="font-semibold text-gray-900 mb-4">Reject Account(s)</h3>
        <form method="POST" action="{{ route('admin.users.bulk-reject') }}">
            @csrf
            <div id="rejectIdsContainer"></div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.reason_optional') }}</label>
                <textarea name="reason" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500"
                          placeholder="Your registration request was not approved."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('common.cancel') }}</button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    {{ __('common.reject') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function getCheckedIds() {
        return [...document.querySelectorAll('.row-checkbox:checked')].map(cb => cb.value);
    }

    function showRejectModal() {
        const ids = getCheckedIds();
        if (!ids.length) { alert('Select at least one account.'); return; }
        const container = document.getElementById('rejectIdsContainer');
        container.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function rejectOne(id) {
        const container = document.getElementById('rejectIdsContainer');
        container.innerHTML = `<input type="hidden" name="ids[]" value="${id}">`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
</script>

@endsection
