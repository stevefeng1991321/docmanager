@extends('layouts.app')
@section('title', 'Messages')

@section('content')
<div x-data="chatApp(@js($currentUser), @js($conversations), @js($openConversationId))"
     x-init="init()"
     class="-mx-4 sm:-mx-6 lg:-mx-8 -my-6 flex border-t border-gray-200"
     style="height: calc(100vh - 4rem);">

    {{-- Conversation list pane --}}
    <div class="w-full sm:w-80 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col"
         :class="active ? 'hidden sm:flex' : 'flex'">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
            <h2 class="font-semibold text-gray-800">Messages</h2>
            <button @click="showNewChat = true; newChatQuery = ''; newChatResults = []; loadUsers()"
                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-lg transition">
                + New
            </button>
        </div>
        <div class="p-3 border-b border-gray-100 flex-shrink-0">
            <input type="text" x-model="search" @input.debounce.300ms="loadConversations()"
                   placeholder="Search conversations &amp; messages…"
                   class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1 overflow-y-auto">
            <template x-for="c in conversations" :key="c.id">
                <button @click="openConversation(c)"
                        class="w-full text-left px-3 py-2.5 flex items-center gap-3 border-b border-gray-50 transition"
                        :class="active && active.id === c.id ? 'bg-blue-50' : 'hover:bg-gray-50'">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase"
                             x-text="c.other_user.name.charAt(0)"></div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white"
                              :class="onlineUsers.has(c.other_user.id) ? 'bg-green-500' : 'bg-gray-300'"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium text-gray-800 truncate" x-text="c.other_user.name"></span>
                            <span class="text-xs text-gray-400 flex-shrink-0" x-text="c.last_message ? timeAgo(c.last_message.created_at) : ''"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2 mt-0.5">
                            <span class="text-xs text-gray-500 truncate" x-text="c.last_message ? c.last_message.body : 'No messages yet'"></span>
                            <span x-show="c.unread_count > 0" x-cloak
                                  class="bg-red-500 text-white text-xs rounded-full min-w-[1.25rem] h-5 flex items-center justify-center px-1 flex-shrink-0"
                                  x-text="c.unread_count > 9 ? '9+' : c.unread_count"></span>
                        </div>
                    </div>
                </button>
            </template>
            <p x-show="conversations.length === 0" class="text-center text-xs text-gray-400 py-10 px-4">
                No conversations yet. Click "+ New" to message someone.
            </p>
        </div>
    </div>

    {{-- Thread pane --}}
    <div class="flex-1 flex flex-col bg-gray-50 min-w-0" :class="active ? 'flex' : 'hidden sm:flex'">
        <div x-show="!active" class="flex-1 flex items-center justify-center text-gray-400 text-sm">
            Select a conversation to start chatting
        </div>

        <div x-show="active" x-cloak class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="px-4 py-3 bg-white border-b border-gray-200 flex items-center gap-3 flex-shrink-0">
                <button type="button" @click="active = null" class="sm:hidden text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <template x-if="active">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase flex-shrink-0"
                             x-text="active.other_user.name.charAt(0)"></div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-800 truncate" x-text="active.other_user.name"></div>
                            <div class="text-xs" :class="onlineUsers.has(active.other_user.id) ? 'text-green-600' : 'text-gray-400'"
                                 x-text="onlineUsers.has(active.other_user.id) ? 'Online' : 'Offline'"></div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="messageList">
                <template x-for="m in messages" :key="m.id">
                    <div :class="m.is_mine ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[75%] sm:max-w-md">
                            <div class="px-3.5 py-2 rounded-2xl text-sm whitespace-pre-wrap break-words"
                                 :class="m.is_mine ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm'"
                                 x-text="m.body"></div>
                            <div class="flex items-center gap-1 mt-0.5 text-xs text-gray-400" :class="m.is_mine ? 'justify-end' : 'justify-start'">
                                <span x-text="formatTime(m.created_at)"></span>
                                <template x-if="m.is_mine">
                                    <span :class="m.status === 'read' ? 'text-blue-500' : 'text-gray-400'"
                                          x-text="m.status === 'sent' ? '✓' : '✓✓'"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Composer --}}
            <div class="p-3 bg-white border-t border-gray-200 flex items-end gap-2 relative flex-shrink-0">
                <div x-show="showEmoji" x-cloak @click.outside="showEmoji = false"
                     class="absolute bottom-14 left-2 bg-white border border-gray-200 rounded-lg shadow-lg p-2 grid grid-cols-8 gap-1 w-72 z-10">
                    <template x-for="e in emojis" :key="e">
                        <button type="button" @click="insertEmoji(e)" class="text-lg hover:bg-gray-100 rounded p-1" x-text="e"></button>
                    </template>
                </div>
                <button type="button" @click="showEmoji = !showEmoji"
                        class="text-2xl flex-shrink-0 leading-none px-1 hover:opacity-70 transition">🙂</button>
                <textarea x-model="draft" x-ref="composer" rows="1"
                          @keydown.enter.prevent="send()"
                          @input="autoGrow($event)"
                          placeholder="Type a message…"
                          class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none max-h-32 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <button type="button" @click="send()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex-shrink-0 transition">
                    Send
                </button>
            </div>
        </div>
    </div>

    {{-- New chat modal --}}
    <div x-show="showNewChat" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showNewChat = false">
        <div @click.outside="showNewChat = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[70vh] flex flex-col overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700">New Conversation</span>
                <input type="text" x-model="newChatQuery" @input.debounce.300ms="loadUsers()"
                       placeholder="Search people…"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
                <template x-for="u in newChatResults" :key="u.id">
                    <button @click="startConversation(u)" class="w-full text-left px-5 py-2.5 flex items-center gap-3 hover:bg-gray-50">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase flex-shrink-0"
                             x-text="u.name.charAt(0)"></div>
                        <div class="min-w-0">
                            <div class="text-sm text-gray-800 truncate" x-text="u.name"></div>
                            <div class="text-xs text-gray-400 truncate" x-text="'@' + u.username"></div>
                        </div>
                    </button>
                </template>
                <p x-show="newChatResults.length === 0" class="px-5 py-8 text-center text-xs text-gray-400">No users found.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatApp(currentUser, initialConversations, openConversationId) {
    return {
        currentUser,
        conversations: initialConversations,
        active: null,
        messages: [],
        draft: '',
        search: '',
        showEmoji: false,
        showNewChat: false,
        newChatQuery: '',
        newChatResults: [],
        onlineUsers: new Set(),
        currentChannelName: null,
        emojis: ['😀','😂','😍','😊','😉','😢','😎','😡','👍','👎','🙏','🎉','❤️','🔥','💯','👏','🤔','😴','😅','🥳','🙌','✅','❌','⭐'],

        init() {
            window.Echo?.join('online')
                .here(users => users.forEach(u => this.onlineUsers.add(u.id)))
                .joining(u => this.onlineUsers.add(u.id))
                .leaving(u => this.onlineUsers.delete(u.id));

            window.Echo?.private('App.Models.User.' + this.currentUser.id)
                .listen('.message.sent', e => this.handleIncomingForList(e));

            if (openConversationId) {
                const found = this.conversations.find(c => c.id === openConversationId);
                if (found) this.openConversation(found);
            }
        },

        handleIncomingForList(e) {
            const idx = this.conversations.findIndex(c => c.id === e.conversation_id);
            if (idx === -1) return;

            const c = this.conversations[idx];
            c.last_message = { body: e.body, sender_id: e.sender_id, created_at: e.created_at };
            c.last_message_at = e.created_at;
            if (!this.active || this.active.id !== e.conversation_id) {
                c.unread_count = (c.unread_count || 0) + 1;
            }
            this.conversations.splice(idx, 1);
            this.conversations.unshift(c);
        },

        async loadConversations() {
            const res = await fetch(`{{ route('chat.conversations.index') }}?q=${encodeURIComponent(this.search)}`);
            const data = await res.json();
            this.conversations = data.conversations;
        },

        async loadUsers() {
            const res = await fetch(`{{ route('chat.users') }}?q=${encodeURIComponent(this.newChatQuery)}`);
            const data = await res.json();
            this.newChatResults = data.users;
        },

        async startConversation(u) {
            const res = await fetch('{{ route('chat.conversations.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ user_id: u.id }),
            });
            const conversation = await res.json();

            if (!this.conversations.find(c => c.id === conversation.id)) {
                this.conversations.unshift(conversation);
            }

            this.showNewChat = false;
            this.openConversation(conversation);
        },

        async openConversation(c) {
            if (this.currentChannelName) {
                window.Echo?.leave(this.currentChannelName);
            }

            this.active = c;
            this.messages = [];

            const res = await fetch(`/chat/conversations/${c.id}`);
            const data = await res.json();
            this.messages = data.messages;

            const inList = this.conversations.find(x => x.id === c.id);
            if (inList) inList.unread_count = 0;

            this.currentChannelName = 'conversation.' + c.id;
            window.Echo?.private(this.currentChannelName)
                .listen('.message.sent', e => {
                    if (!this.messages.find(m => m.id === e.id)) {
                        this.messages.push({ ...e, is_mine: e.sender_id === this.currentUser.id, status: 'sent' });
                        this.scrollToBottom();
                    }
                    if (e.sender_id !== this.currentUser.id) {
                        this.ackDelivered(c.id);
                        this.ackRead(c.id);
                    }
                })
                .listen('.messages.read', e => {
                    if (e.user_id === this.currentUser.id) return;
                    this.messages.forEach(m => {
                        if (m.is_mine && new Date(m.created_at) <= new Date(e.read_at)) m.status = 'read';
                    });
                })
                .listen('.messages.delivered', e => {
                    if (e.user_id === this.currentUser.id) return;
                    this.messages.forEach(m => {
                        if (m.is_mine && m.status === 'sent') m.status = 'delivered';
                    });
                });

            this.$nextTick(() => this.scrollToBottom());
        },

        async ackDelivered(conversationId) {
            await fetch(`/chat/conversations/${conversationId}/delivered`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
        },

        async ackRead(conversationId) {
            await fetch(`/chat/conversations/${conversationId}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
        },

        async send() {
            const body = this.draft.trim();
            if (!body || !this.active) return;

            this.draft = '';
            this.$refs.composer.style.height = 'auto';

            const res = await fetch(`/chat/conversations/${this.active.id}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ body }),
            });
            const message = await res.json();
            this.messages.push(message);

            const inList = this.conversations.find(c => c.id === this.active.id);
            if (inList) {
                inList.last_message = { body: message.body, sender_id: message.sender_id, created_at: message.created_at };
                inList.last_message_at = message.created_at;
                this.conversations.splice(this.conversations.indexOf(inList), 1);
                this.conversations.unshift(inList);
            }

            this.$nextTick(() => this.scrollToBottom());
        },

        insertEmoji(e) {
            this.draft += e;
            this.showEmoji = false;
            this.$refs.composer.focus();
        },

        autoGrow(event) {
            const el = event.target;
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 128) + 'px';
        },

        scrollToBottom() {
            const el = this.$refs.messageList;
            if (el) el.scrollTop = el.scrollHeight;
        },

        formatTime(iso) {
            return new Date(iso).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        },

        timeAgo(iso) {
            const diff = (Date.now() - new Date(iso).getTime()) / 1000;
            if (diff < 60) return 'now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h';
            return Math.floor(diff / 86400) + 'd';
        },
    };
}
</script>
@endpush
