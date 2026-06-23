@extends('layouts.admin')
@section('title', 'Account Requests')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">Account Requests</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $requests->total() }} request(s)</p>
        </div>
        <form method="GET" class="flex gap-2">
            <select name="type" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <option value="">All Types</option>
                <option value="username_change" {{ request('type') === 'username_change' ? 'selected' : '' }}>Username Change</option>
                <option value="account_deletion" {{ request('type') === 'account_deletion' ? 'selected' : '' }}>Account Deletion</option>
            </select>
            <select name="status" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                <option value="">All Statuses</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Details</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Submitted</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">
                            {{ $req->user?->username ?? '(deleted)' }}
                            <span class="block text-xs text-gray-400">{{ $req->user?->name }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($req->type === 'username_change')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Username Change
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
                            @elseif($req->reason)
                                <span class="text-xs text-gray-500">{{ Str::limit($req->reason, 60) }}</span>
                            @else
                                <span class="text-xs text-gray-400">No reason given</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3">
                            @if($req->status === 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.account-requests.approve', $req) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-medium transition">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button" onclick="showRejectModal({{ $req->id }})"
                                            class="px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition">
                                        Reject
                                    </button>
                                </div>
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
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No account requests.</td></tr>
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
        <h3 class="font-semibold text-gray-900 mb-4">Reject Request</h3>
        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note to user (optional)</label>
                <textarea name="admin_note" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Reject
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
