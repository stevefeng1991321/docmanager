@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-xl font-bold text-gray-800">Notifications</h1>
        @if($notifications->contains('is_read', false))
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf @method('PATCH')
            <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">Mark all as read</button>
        </form>
        @endif
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-50">
        @forelse($notifications as $notif)
        <div class="flex items-start gap-4 px-5 py-4 {{ $notif->is_read ? '' : 'bg-blue-50/40' }}">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm">{{ $notif->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $notif->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at?->diffForHumans() ?? '—' }}</p>
            </div>
            <div class="flex gap-2 flex-shrink-0 text-xs">
                @if(!$notif->is_read)
                <form method="POST" action="{{ route('notifications.read', $notif) }}">
                    @csrf @method('PATCH')
                    <button class="text-blue-500 hover:text-blue-700">Mark read</button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notif) }}">
                    @csrf @method('DELETE')
                    <button class="text-gray-400 hover:text-red-500">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <p class="px-5 py-10 text-center text-gray-400 text-sm">No notifications.</p>
        @endforelse
    </div>
    <div>{{ $notifications->links() }}</div>
</div>
@endsection
