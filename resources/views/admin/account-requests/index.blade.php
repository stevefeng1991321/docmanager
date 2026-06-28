@extends('layouts.admin')
@section('title', 'Account Requests')

@section('content')

{{-- Reset link generated modal --}}
@if(session('reset_url'))
<div id="resetLinkModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg shadow-xl space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Reset Link Generated</h3>
                <p class="text-sm text-gray-500">Copy this link and hand it to the user. It expires in 24 hours.</p>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 flex items-center gap-2">
            <code id="resetUrl" class="text-xs text-gray-700 break-all flex-1 select-all">{{ session('reset_url') }}</code>
            <button onclick="copyResetUrl()" title="Copy"
                    class="flex-shrink-0 p-1.5 rounded-lg hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition">
                <svg id="copyIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>
        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
            Do not share this link via email or any network channel. Hand it directly to the user in person or via a secure internal channel.
        </p>
        <div class="flex justify-end">
            <button onclick="document.getElementById('resetLinkModal').remove()"
                    class="px-5 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg transition">
                Done
            </button>
        </div>
    </div>
</div>
<script>
function copyResetUrl() {
    navigator.clipboard.writeText(document.getElementById('resetUrl').textContent.trim()).then(() => {
        const icon = document.getElementById('copyIcon');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        setTimeout(() => {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>';
        }, 2000);
    });
}
</script>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">{{ __('admin.account_requests.heading') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $requests->total() }} request(s)</p>
        </div>
        <form method="GET" class="flex gap-2">
            <select name="type" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <option value="">All Types</option>
                <option value="username_change"  {{ request('type') === 'username_change'  ? 'selected' : '' }}>Username Change</option>
                <option value="account_deletion" {{ request('type') === 'account_deletion' ? 'selected' : '' }}>Account Deletion</option>
                <option value="password_reset"   {{ request('type') === 'password_reset'   ? 'selected' : '' }}>Password Reset</option>
            </select>
            <select name="status" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <option value="">All Statuses</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>{{ __('admin.account_requests.pending') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('admin.account_requests.approved') }}</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('admin.account_requests.rejected') }}</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">{{ __('admin.account_requests.col_name') }}</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.account_requests.detail_heading') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.account_requests.col_status') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.account_requests.col_submitted') }}</th>
                    <th class="px-6 py-3 text-left">{{ __('admin.account_requests.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 {{ session('reset_request_id') == $req->id ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-3 font-medium text-gray-900">
                            {{ $req->user?->username ?? '(deleted)' }}
                            <span class="block text-xs text-gray-400">{{ $req->user?->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($req->type === 'username_change')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Username Change
                                </span>
                            @elseif($req->type === 'password_reset')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Password Reset
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Account Deletion
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            @if($req->type === 'username_change')
                                <span class="text-xs">→ <strong>{{ $req->new_username }}</strong></span>
                            @elseif($req->type === 'password_reset')
                                @if($req->reset_token && $req->reset_token_expires_at?->isFuture())
                                    <span class="text-xs text-green-600">Link valid · expires {{ $req->reset_token_expires_at->diffForHumans() }}</span>
                                @elseif($req->reset_token)
                                    <span class="text-xs text-red-500">Link expired — regenerate below</span>
                                @else
                                    <span class="text-xs text-gray-400">No link generated yet</span>
                                @endif
                            @elseif($req->reason)
                                <span class="text-xs text-gray-500">{{ Str::limit($req->reason, 60) }}</span>
                            @else
                                <span class="text-xs text-gray-400">No reason given</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('admin.account_requests.pending') }}</span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('admin.account_requests.approved') }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ __('admin.account_requests.rejected') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            @if($req->status === 'pending')
                                @if($req->type === 'password_reset')
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.account-requests.generate-reset-link', $req) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="px-3 py-1 bg-purple-100 text-purple-700 hover:bg-purple-200 rounded-lg text-xs font-medium transition">
                                                {{ __('admin.account_requests.set_password') }}
                                            </button>
                                        </form>
                                        <button type="button" onclick="showRejectModal({{ $req->id }})"
                                                class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition">
                                            {{ __('admin.account_requests.reject_action') }}
                                        </button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.account-requests.approve', $req) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-medium transition">
                                                {{ __('admin.account_requests.approve_action') }}
                                            </button>
                                        </form>
                                        <button type="button" onclick="showRejectModal({{ $req->id }})"
                                                class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition">
                                            {{ __('admin.account_requests.reject_action') }}
                                        </button>
                                    </div>
                                @endif
                            @else
                                @if($req->admin_note)
                                    <span class="text-xs text-gray-400" title="{{ $req->admin_note }}">Note: {{ Str::limit($req->admin_note, 40) }}</span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('admin.account_requests.no_requests') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $requests->links() }}</div>
    @endif
</div>

{{-- Reject modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h3 class="font-semibold text-gray-900 mb-4">{{ __('admin.account_requests.reject_action') }}</h3>
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin.account_requests.rejection_reason') }}</label>
                <textarea name="admin_note" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('common.cancel') }}</button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    {{ __('admin.account_requests.reject_button') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal(requestId) {
        document.getElementById('rejectForm').action =
            '/admin/account-requests/' + requestId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }
</script>

@endsection
