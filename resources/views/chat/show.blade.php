@extends('layouts.app')

@section('title', 'Chat')

@section('content')
@php
    $others = $conversation->participants->where('user_id', '!=', auth()->id())->values();
    $chatName = $conversation->type === 'group'
        ? ($conversation->name ?? 'Group')
        : ($others->first()?->user->name ?? 'Unknown');
    $participantIds = $conversation->participants->pluck('user_id')->toArray();
@endphp

<div
    x-data="chatApp()"
    x-init="init()"
    @chat:new-message.window="onExternalMessage($event.detail)"
    class="flex bg-white rounded-xl border border-gray-200 overflow-hidden"
    style="height: calc(100vh - 10rem);"
>

    {{-- ─── Left Panel: Conversation list ──────────────────────── --}}
    <div class="w-72 flex-shrink-0 border-r border-gray-200 flex flex-col">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('chat.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-gray-800 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Messages
            </a>
            <button @click="openNewChat()" class="p-1 rounded-lg hover:bg-gray-100 text-gray-500" title="New chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>

        {{-- Conversation list --}}
        <div class="flex-1 overflow-y-auto">
            <template x-if="conversations.length === 0 && !loadingConvs">
                <p class="text-xs text-gray-400 text-center py-8">No conversations</p>
            </template>
            <template x-if="loadingConvs">
                <p class="text-xs text-gray-400 text-center py-8">Loading…</p>
            </template>
            <template x-for="conv in conversations" :key="conv.id">
                <a
                    :href="'/chat/' + conv.id"
                    class="flex items-center gap-2.5 px-3 py-2.5 border-b border-gray-50 hover:bg-gray-50 transition"
                    :class="conv.id == activeConvId ? 'bg-blue-50 border-l-2 border-l-blue-500' : ''"
                >
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-sm font-semibold text-blue-700 flex-shrink-0"
                         x-text="conv.initial"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-sm font-medium text-gray-800 truncate" x-text="conv.name"></span>
                            <span class="text-xs text-gray-400 flex-shrink-0" x-text="formatTime(conv.last_message_at)"></span>
                        </div>
                        <p class="text-xs text-gray-500 truncate" x-text="conv.last_message || 'No messages yet'"></p>
                    </div>
                    <template x-if="conv.unread_count > 0">
                        <span class="min-w-[18px] h-[18px] rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center px-1 flex-shrink-0"
                              x-text="conv.unread_count"></span>
                    </template>
                </a>
            </template>
        </div>
    </div>

    {{-- ─── Right Panel: Message area ───────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Chat header --}}
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3 flex-shrink-0">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center font-semibold text-blue-700 text-sm flex-shrink-0">
                {{ strtoupper(substr($chatName, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-sm text-gray-800">{{ $chatName }}</span>
                    @if($conversation->type === 'private' && $others->isNotEmpty())
                        <span
                            x-show="onlineUsers.includes({{ $others->first()->user_id }})"
                            class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"
                            title="Online"
                        ></span>
                    @endif
                </div>
                @if($conversation->type === 'group')
                    <p class="text-xs text-gray-400">
                        {{ $conversation->participants->count() }} members
                        @if($others->isNotEmpty())
                            &mdash; <span x-text="onlineUsers.filter(id => @json($participantIds).includes(id)).length"></span> online
                        @endif
                    </p>
                @else
                    <p class="text-xs text-gray-400"
                       x-text="onlineUsers.includes({{ $others->first()?->user_id ?? 0 }}) ? 'Online' : 'Offline'"></p>
                @endif
            </div>
        </div>

        {{-- Status banners --}}
        <div x-show="!isOnline" x-cloak class="px-4 py-2 bg-red-50 border-b border-red-200 text-xs text-red-700 font-medium flex items-center gap-2 flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            You are offline — messages will send when reconnected
        </div>
        <div x-show="isOnline && !isConnected" x-cloak class="px-4 py-2 bg-yellow-50 border-b border-yellow-200 text-xs text-yellow-700 font-medium flex items-center gap-2 flex-shrink-0">
            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Reconnecting to real-time server…
        </div>

        {{-- Message list --}}
        <div
            x-ref="messageList"
            class="flex-1 overflow-y-auto px-4 py-4 space-y-2"
        >
            <template x-if="loadingMessages">
                <div class="flex justify-center py-8">
                    <div class="w-6 h-6 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
                </div>
            </template>

            <template x-for="msg in messages" :key="msg.id ?? msg._tempId">
                <div
                    class="flex items-end gap-2"
                    :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'"
                >
                    {{-- Avatar (others only) --}}
                    <template x-if="msg.sender_id != currentUserId">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600 flex-shrink-0 mb-1"
                             x-text="msg.sender_initial || '?'"></div>
                    </template>

                    <div class="flex flex-col max-w-[68%]" :class="msg.sender_id == currentUserId ? 'items-end' : 'items-start'">
                        {{-- Sender name (group chats, others only) --}}
                        @if($conversation->type === 'group')
                            <template x-if="msg.sender_id != currentUserId">
                                <span class="text-[10px] font-medium text-gray-500 mb-0.5 px-1" x-text="msg.sender_name"></span>
                            </template>
                        @endif

                        {{-- Bubble --}}
                        <div
                            class="rounded-2xl px-3.5 py-2 text-sm leading-relaxed break-words"
                            :class="{
                                'bg-blue-600 text-white rounded-br-sm': msg.sender_id == currentUserId && !msg.deleted,
                                'bg-gray-100 text-gray-800 rounded-bl-sm': msg.sender_id != currentUserId && !msg.deleted,
                                'bg-gray-100 text-gray-400 italic': msg.deleted,
                                'opacity-60': msg.pending
                            }"
                        >
                            <template x-if="msg.deleted">
                                <span>This message was deleted</span>
                            </template>
                            <template x-if="!msg.deleted">
                                <span x-text="msg.body"></span>
                            </template>
                        </div>

                        {{-- Timestamp + read receipt --}}
                        <div class="flex items-center gap-1 mt-0.5 px-1"
                             :class="msg.sender_id == currentUserId ? 'flex-row-reverse' : ''">
                            <span class="text-[10px] text-gray-400" x-text="formatMsgTime(msg.created_at)"></span>
                            <template x-if="msg.sender_id == currentUserId && !msg.pending">
                                <span class="text-[10px]"
                                      :class="isRead(msg) ? 'text-blue-500' : 'text-gray-400'"
                                      :title="isRead(msg) ? 'Read' : 'Delivered'"
                                >
                                    <template x-if="isRead(msg)">
                                        <span>✓✓</span>
                                    </template>
                                    <template x-if="!isRead(msg)">
                                        <span>✓</span>
                                    </template>
                                </span>
                            </template>
                            <template x-if="msg.pending">
                                <span class="text-[10px] text-gray-300">⏳</span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Compose area --}}
        <div class="px-4 py-3 border-t border-gray-100 flex-shrink-0">
            <div class="flex items-end gap-2">
                <textarea
                    x-model="newMessage"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                    placeholder="Type a message… (Enter to send, Shift+Enter for new line)"
                    rows="1"
                    class="flex-1 border border-gray-300 rounded-2xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none leading-5 max-h-32 overflow-y-auto"
                    style="min-height: 42px;"
                    @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 128) + 'px'"
                ></textarea>
                <button
                    @click="sendMessage()"
                    :disabled="!newMessage.trim()"
                    class="w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center flex-shrink-0 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── New Chat Modal ───────────────────────────────────────── --}}
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
                    <div x-show="loadingUsers" class="py-4 text-center text-sm text-gray-400">Loading…</div>
                    <div class="max-h-52 overflow-y-auto space-y-1" x-show="!loadingUsers">
                        <template x-for="user in modalUsers" :key="user.id">
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
@php
$authId = auth()->id();
$initialConvs = $conversations->map(function($c) use ($authId) {
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
<script>
function chatApp() {
    const CONV_ID         = {{ $conversation->id }};
    const CURRENT_USER    = {{ auth()->id() }};
    const CURRENT_INITIAL = @json(strtoupper(substr(auth()->user()->name, 0, 1)));
    const CACHE_KEY       = 'chat_msg_' + CONV_ID;
    const PENDING_KEY     = 'chat_pend_' + CONV_ID;
    const INITIAL_CONVS   = @json($initialConvs);

    return {
        // State
        activeConvId:   CONV_ID,
        currentUserId:  CURRENT_USER,
        messages:       [],
        conversations:  INITIAL_CONVS,
        onlineUsers:    [],
        readStatus:     {},   // { userId: readAtISO }
        newMessage:     '',
        loadingMessages: true,
        loadingConvs:   false,
        isOnline:       navigator.onLine,
        isConnected:    window.Echo?.connector?.pusher?.connection?.state === 'connected',
        // New chat modal
        showNewChat:    false,
        newChatType:    'private',
        modalUsers:     [],
        selectedUsers:  [],
        groupName:      '',
        loadingUsers:   false,
        creating:       false,

        init() {
            // Load localStorage message cache instantly for offline-first feel
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                try { this.messages = JSON.parse(cached); this.loadingMessages = false; } catch(e) {}
            }

            this.loadMessages();
            this.subscribeEcho();

            window.addEventListener('online',  () => { this.isOnline = true; this.flushQueue(); });
            window.addEventListener('offline', () => { this.isOnline = false; });
        },

        async loadMessages() {
            try {
                const res = await axios.get('/chat/' + CONV_ID + '/messages');
                this.messages = res.data.data;
                // Seed read-receipt state from server so ticks work on first load
                if (res.data.read_status) {
                    Object.assign(this.readStatus, res.data.read_status);
                }
                this.loadingMessages = false;
                this.cacheMessages();
                this.markRead();
                this.$nextTick(() => this.scrollBottom());
            } catch(e) {
                this.loadingMessages = false;
            }
        },

        loadConversations() {
            // Conversations are seeded server-side via INITIAL_CONVS — no API call needed
        },

        subscribeEcho() {
            window.Echo.private('conversation.' + CONV_ID)
                .listen('.message.sent', (e) => {
                    // Own messages are handled by optimistic UI + API response.
                    // Pushing the Echo event too causes duplicate keys in x-for (wrapping bug).
                    if (e.sender_id === CURRENT_USER) return;

                    // Dedup — shouldn't happen for others, but guard anyway
                    if (this.messages.find(m => m.id === e.id)) return;

                    this.messages.push(e);
                    this.cacheMessages();

                    // Update left panel
                    const conv = this.conversations.find(c => c.id === e.conversation_id);
                    if (conv) {
                        conv.last_message    = e.body;
                        conv.last_message_at = e.created_at;
                        conv.unread_count    = (conv.unread_count || 0) + 1;
                    }

                    this.markRead();
                    this.$nextTick(() => this.scrollBottom());
                })
                .listen('.message.read', (e) => {
                    if (e.user_id !== CURRENT_USER) {
                        this.readStatus[e.user_id] = e.read_at;
                    }
                    // Clear unread for active conv in left panel
                    const conv = this.conversations.find(c => c.id === e.conversation_id);
                    if (conv && e.user_id === CURRENT_USER) conv.unread_count = 0;
                });

            // Track WebSocket connection state via Pusher.js connection (Reverb uses Pusher protocol)
            window.Echo.connector.pusher.connection.bind('connected',    () => { this.isConnected = true;  });
            window.Echo.connector.pusher.connection.bind('disconnected', () => { this.isConnected = false; });
            window.Echo.connector.pusher.connection.bind('unavailable',  () => { this.isConnected = false; });

            // Presence channel for online status
            window.Echo.join('online')
                .here((users)  => { this.onlineUsers = users.map(u => u.id); })
                .joining((user) => { if (!this.onlineUsers.includes(user.id)) this.onlineUsers.push(user.id); })
                .leaving((user) => { this.onlineUsers = this.onlineUsers.filter(id => id !== user.id); });
        },

        async sendMessage() {
            const body = this.newMessage.trim();
            if (!body) return;
            this.newMessage = '';

            // Optimistic insert — show immediately regardless of network/echo
            const tempId = '_' + Date.now();
            this.messages.push({
                _tempId: tempId,
                sender_id:      CURRENT_USER,
                sender_initial: CURRENT_INITIAL,
                body,
                created_at: new Date().toISOString(),
                type: 'text',
                pending: !this.isOnline,
            });

            // Optimistically update left panel preview
            const conv = this.conversations.find(c => c.id === CONV_ID);
            if (conv) {
                conv.last_message    = body;
                conv.last_message_at = new Date().toISOString();
            }

            this.$nextTick(() => this.scrollBottom());

            if (!this.isOnline) {
                const queue = JSON.parse(localStorage.getItem(PENDING_KEY) || '[]');
                queue.push({ body, tempId });
                localStorage.setItem(PENDING_KEY, JSON.stringify(queue));
                return;
            }

            try {
                const res = await axios.post('/chat/' + CONV_ID + '/messages', { body });
                const idx = this.messages.findIndex(m => m._tempId === tempId);
                if (idx !== -1) {
                    this.messages.splice(idx, 1, {
                        id:             res.data.id,
                        sender_id:      res.data.sender_id,
                        sender_initial: CURRENT_INITIAL,
                        body:           res.data.body,
                        created_at:     res.data.created_at,
                        type:           'text',
                        deleted:        false,
                        pending:        false,
                    });
                    // Confirm left panel with server timestamp
                    if (conv) conv.last_message_at = res.data.created_at;
                    this.cacheMessages();
                }
            } catch(e) {
                const idx = this.messages.findIndex(m => m._tempId === tempId);
                if (idx !== -1) this.messages[idx].failed = true;
                this.newMessage = body;
            }
        },

        // Update left panel when a message arrives in a DIFFERENT conversation
        // (dispatched by chatNotify() in the layout via the personal Echo channel)
        onExternalMessage(e) {
            if (e.conversation_id === CONV_ID) return;
            const conv = this.conversations.find(c => c.id === e.conversation_id);
            if (conv) {
                conv.last_message    = e.body;
                conv.last_message_at = new Date().toISOString();
                conv.unread_count    = (conv.unread_count || 0) + 1;
            }
        },

        async flushQueue() {
            const queue = JSON.parse(localStorage.getItem(PENDING_KEY) || '[]');
            if (!queue.length) return;
            for (const item of queue) {
                const body   = typeof item === 'string' ? item : item.body;
                const tempId = typeof item === 'string' ? null : item.tempId;
                try {
                    const res = await axios.post('/chat/' + CONV_ID + '/messages', { body });
                    if (tempId) {
                        const idx = this.messages.findIndex(m => m._tempId === tempId);
                        if (idx !== -1) this.messages.splice(idx, 1, { ...res.data, sender_initial: CURRENT_INITIAL });
                    }
                } catch(e) {}
            }
            localStorage.removeItem(PENDING_KEY);
            this.messages = this.messages.filter(m => !m.pending);
        },

        markRead() {
            axios.post('/chat/' + CONV_ID + '/read').catch(() => {});
            const conv = this.conversations.find(c => c.id === CONV_ID);
            if (conv) conv.unread_count = 0;
        },

        cacheMessages() {
            try {
                localStorage.setItem(CACHE_KEY, JSON.stringify(this.messages.slice(-100)));
            } catch(e) {}
        },

        isRead(msg) {
            if (!msg.created_at) return false;
            const msgTime = new Date(msg.created_at).getTime();
            // A message is read if any OTHER participant has a readStatus timestamp >= message time
            return Object.entries(this.readStatus).some(([uid, readAt]) => {
                return parseInt(uid) !== CURRENT_USER && new Date(readAt).getTime() >= msgTime;
            });
        },

        scrollBottom() {
            const el = this.$refs.messageList;
            if (el) el.scrollTop = el.scrollHeight;
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

        formatMsgTime(iso) {
            if (!iso) return '';
            return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        // ─── New chat modal ───────────────────────────────────────
        async openNewChat() {
            this.showNewChat  = true;
            this.selectedUsers = [];
            this.groupName    = '';
            this.newChatType  = 'private';
            if (this.modalUsers.length === 0) {
                this.loadingUsers = true;
                try {
                    const res = await axios.get('/chat/users');
                    this.modalUsers = res.data;
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
                this.creating = false;
            }
        },
    };
}
</script>
@endpush
