@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h3 class="font-semibold text-gray-800">Broadcast to All Active Users</h3>
        <form method="POST" action="{{ route('admin.notifications.broadcast') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                <input type="text" name="title" required maxlength="255"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Message</label>
                <textarea name="message" required rows="4"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                Send Broadcast
            </button>
        </form>
    </div>
</div>
@endsection
