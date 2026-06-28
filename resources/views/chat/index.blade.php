@extends('layouts.app')

@section('title', 'Messages')

@php
$authId   = auth()->id();
$convData = $conversations->map(function($c) use ($authId) {
    $other = $c->participants->where('user_id', '!=', $authId)->first();
    $name  = $c->type === 'group' ? ($c->name ?? 'Group') : ($other?->user->name ?? 'Unknown');
    return [
        'id'              => $c->id,
        'type'            => $c->type,
        'name'            => $name,
        'initial'         => strtoupper(substr($name, 0, 1)),
        'last_message'    => $c->latestMessage?->body,
        'last_message_at' => $c->last_message_at?->toISOString(),
        'unread_count'    => $c->unread_count,
    ];
})->values();
@endphp

@section('content')
<div x-data="chatIndex()" x-init="init()" @chat:new-message.window="onNewMessage($event.detail)">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Messages</h1>
        <button
            @click="openNewChat()"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Chat
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <template x-if="conversations.length === 0">
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 mb-3">No conversations yet</p>
                <button @click="openNewChat()" class="text-sm font-medium text-blue-600 hover:underline">Start a new chat</button>
            </div>
        </template>

        <template x-for="conv in conversations" :key="conv.id">
            <a :href="'/chat/' + conv.id"
               class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-semibold text-blue-700 text-sm"
                         x-text="conv.initial"></div>
                    <template x-if="conv.type === 'group'">
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-purple-100 border border-white flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM2 8a2 2 0 114 0A2 2 0 012 8zM16 18v-1a5 5 0 00-5-5H9a5 5 0 00-5 5v1h12z"/></svg>
                        </span>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-medium text-sm text-gray-800 truncate" x-text="conv.name"></span>
                        <span class="text-xs text-gray-400 flex-shrink-0" x-text="formatTime(conv.last_message_at)"></span>
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5" x-text="conv.last_message || 'No messages yet'"></p>
                </div>
                <template x-if="conv.unread_count > 0">
                    <span class="flex-shrink-0 min-w-[20px] h-5 rounded-full bg-blue-600 text-white text-xs font-semibold flex items-center justify-center px-1"
                          x-text="conv.unread_count"></span>
                </template>
            </a>
        </template>
    </div>

    {{-- New Chat Modal --}}
    <div
        x-show="showNewChat"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        @keydown.escape.window="showNewChat = false"
    >
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4" @click.outside="showNewChat = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">New Conversation</h3>
                <button @click="showNewChat = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex gap-2">
                    <button @click="newChatType = 'private'; selectedUsers = []"
                            :class="newChatType === 'private' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="flex-1 py-2 text-sm font-medium rounded-lg transition">Private</button>
                    <button @click="newChatType = 'group'; selectedUsers = []"
                            :class="newChatType === 'group' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="flex-1 py-2 text-sm font-medium rounded-lg transition">Group</button>
                </div>
                <div x-show="newChatType === 'group'">
                    <input type="text" x-model="groupName" placeholder="Group name"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 mb-2">
                        <span x-show="newChatType === 'private'">Select a person</span>
                        <span x-show="newChatType === 'group'">Select members</span>
                    </p>
                    <div x-show="loadingUsers" class="py-6 text-center text-sm text-gray-400">Loading…</div>
                    <div class="max-h-56 overflow-y-auto space-y-1" x-show="!loadingUsers">
                        <template x-for="user in users" :key="user.id">
                            <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-sm font-semibold text-blue-700 flex-shrink-0"
                                     x-text="user.name.charAt(0).toUpperCase()"></div>
                                <span class="text-sm text-gray-700 flex-1" x-text="user.name"></span>
                                <input type="checkbox"
                                       :checked="selectedUsers.includes(user.id)"
                                       @change="toggleUser(user.id)"
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            </label>
                        </template>
                    </div>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button @click="showNewChat = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button @click="createConversation()"
                        :disabled="selectedUsers.length === 0 || creating"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!creating">Start Chat</span>
                    <span x-show="creating">Creating…</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatIndex() {
    const INITIAL = @json($convData);

    return {
        conversations: INITIAL,
        showNewChat:   false,
        newChatType:   'private',
        users:         [],
        selectedUsers: [],
        groupName:     '',
        loadingUsers:  false,
        creating:      false,

        init() {},

        onNewMessage(e) {
            const conv = this.conversations.find(c => c.id === e.conversation_id);
            if (conv) {
                conv.last_message    = e.body;
                conv.last_message_at = new Date().toISOString();
                conv.unread_count    = (conv.unread_count || 0) + 1;
                // Bubble the updated conversation to the top
                this.conversations = [
                    conv,
                    ...this.conversations.filter(c => c.id !== e.conversation_id),
                ];
            }
        },

        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            const now = new Date();
            const diffDays = Math.floor((now - d) / 86400000);
            if (diffDays === 0) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7)  return d.toLocaleDateString([], { weekday: 'short' });
            return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
        },

        async openNewChat() {
            this.showNewChat   = true;
            this.selectedUsers = [];
            this.groupName     = '';
            this.newChatType   = 'private';
            if (this.users.length === 0) {
                this.loadingUsers = true;
                try {
                    const res = await axios.get('/chat/users');
                    this.users = res.data;
                } catch(e) {
                    console.error('Failed to load users', e);
                } finally {
                    this.loadingUsers = false;
                }
            }
        },

        toggleUser(id) {
            if (this.selectedUsers.includes(id)) {
                this.selectedUsers = this.selectedUsers.filter(u => u !== id);
            } else {
                if (this.newChatType === 'private') this.selectedUsers = [id];
                else this.selectedUsers.push(id);
            }
        },

        async createConversation() {
            if (!this.selectedUsers.length) return;
            this.creating = true;
            try {
                const res = await axios.post('/chat/start', {
                    type:     this.newChatType,
                    user_ids: this.selectedUsers,
                    name:     this.newChatType === 'group' ? this.groupName : null,
                });
                window.location.href = '/chat/' + res.data.id;
            } catch(e) {
                console.error('Failed to create conversation', e);
                this.creating = false;
            }
        },
    };
}
</script>
@endpush
