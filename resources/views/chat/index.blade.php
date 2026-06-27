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
            <button @click="openNewChatModal()"
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
                        <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold uppercase"
                             :class="c.type === 'group' ? 'bg-indigo-600' : 'bg-blue-600'"
                             x-text="c.name.charAt(0)"></div>
                        <template x-if="c.type === 'direct'">
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white"
                                  :class="onlineUsers.has(c.other_user?.id) ? 'bg-green-500' : 'bg-gray-300'"></span>
                        </template>
                        <template x-if="c.type === 'group'">
                            <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full border-2 border-white bg-indigo-100 flex items-center justify-center">
                                <svg class="w-2 h-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                            </span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium text-gray-800 truncate" x-text="c.name"></span>
                            <span class="text-xs text-gray-400 flex-shrink-0" x-text="c.last_message ? timeAgo(c.last_message.created_at) : ''"></span>
                        </div>
                        <div class="flex items-center justify-between gap-2 mt-0.5">
                            <span class="text-xs text-gray-500 truncate"
                                  x-text="c.last_message ? (c.type === 'group' && c.last_message.sender_id !== currentUser.id ? c.last_message.sender_name + ': ' : '') + c.last_message.body : 'No messages yet'">
                            </span>
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
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-9 h-9 rounded-full text-white flex items-center justify-center font-bold uppercase flex-shrink-0"
                             :class="active.type === 'group' ? 'bg-indigo-600' : 'bg-blue-600'"
                             x-text="active.name.charAt(0)"></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-800 truncate" x-text="active.name"></div>
                            <template x-if="active.type === 'direct'">
                                <div class="text-xs" :class="onlineUsers.has(active.other_user?.id) ? 'text-green-600' : 'text-gray-400'"
                                     x-text="onlineUsers.has(active.other_user?.id) ? 'Online' : 'Offline'"></div>
                            </template>
                            <template x-if="active.type === 'group'">
                                <div class="text-xs text-gray-400" x-text="active.member_count + ' members'"></div>
                            </template>
                        </div>
                        {{-- Add member button (group admin only) --}}
                        <template x-if="active.type === 'group'">
                            <button type="button" @click="showAddMember = true; newChatQuery = ''; newChatResults = []; loadUsers()"
                                    title="Add member"
                                    class="flex-shrink-0 text-gray-400 hover:text-indigo-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="messageList">
                <template x-for="m in messages" :key="m.id">
                    <div :class="m.is_mine ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[75%] sm:max-w-md">
                            {{-- Sender name in group chats (incoming only) --}}
                            <template x-if="!m.is_mine && active && active.type === 'group'">
                                <p class="text-xs text-gray-400 mb-0.5 ml-1" x-text="m.sender_name"></p>
                            </template>
                            <div class="px-3.5 py-2 rounded-2xl text-sm whitespace-pre-wrap break-words"
                                 :class="m.is_mine ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm'"
                                 x-text="m.body"></div>
                            <div class="flex items-center gap-1 mt-0.5 text-xs text-gray-400" :class="m.is_mine ? 'justify-end' : 'justify-start'">
                                <span x-text="formatTime(m.created_at)"></span>
                                <template x-if="m.is_mine && active && active.type === 'direct'">
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

    {{-- New conversation modal (Direct + Group tabs) --}}
    <div x-show="showNewChat" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showNewChat = false">
        <div @click.outside="showNewChat = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col overflow-hidden">

            {{-- Tabs --}}
            <div class="flex border-b border-gray-100 flex-shrink-0">
                <button @click="newChatMode = 'direct'"
                        class="flex-1 py-3 text-sm font-medium transition"
                        :class="newChatMode === 'direct' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'">
                    Direct Message
                </button>
                <button @click="newChatMode = 'group'"
                        class="flex-1 py-3 text-sm font-medium transition"
                        :class="newChatMode === 'group' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                    Group Chat
                </button>
            </div>

            {{-- Group name input (group mode only) --}}
            <div x-show="newChatMode === 'group'" class="px-5 pt-3 flex-shrink-0">
                <input type="text" x-model="groupName" placeholder="Group name…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <template x-if="selectedMembers.length > 0">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="m in selectedMembers" :key="m.id">
                            <span class="inline-flex items-center gap-1 text-xs bg-indigo-100 text-indigo-700 rounded-full px-2 py-0.5">
                                <span x-text="m.name"></span>
                                <button type="button" @click="toggleMember(m)" class="hover:text-red-600 leading-none">×</button>
                            </span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Search --}}
            <div class="px-5 py-3 flex-shrink-0">
                <input type="text" x-model="newChatQuery" @input.debounce.300ms="loadUsers()"
                       :placeholder="newChatMode === 'group' ? 'Add people…' : 'Search people…'"
                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Results --}}
            <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-50">
                <template x-for="u in newChatResults" :key="u.id">
                    <button @click="newChatMode === 'direct' ? startConversation(u) : toggleMember(u)"
                            class="w-full text-left px-5 py-2.5 flex items-center gap-3 hover:bg-gray-50 transition">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold uppercase flex-shrink-0"
                             x-text="u.name.charAt(0)"></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm text-gray-800 truncate" x-text="u.name"></div>
                            <div class="text-xs text-gray-400 truncate" x-text="'@' + u.username"></div>
                        </div>
                        <span x-show="newChatMode === 'group'"
                              class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition"
                              :class="isSelected(u.id) ? 'bg-indigo-600 border-indigo-600 text-black' : 'border-gray-300'">
                            <svg x-show="isSelected(u.id)" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </button>
                </template>
                <p x-show="newChatResults.length === 0" class="px-5 py-8 text-center text-xs text-gray-400">No users found.</p>
            </div>

            {{-- Create group button --}}
            <div x-show="newChatMode === 'group'" class="px-5 py-3 border-t border-gray-100 flex-shrink-0">
                <button @click="createGroup()"
                        :disabled="!canCreateGroup()"
                        class="w-full py-2 rounded-lg text-sm font-semibold transition"
                        :style="canCreateGroup()
                            ? 'background:#000;color:#fff;cursor:pointer;'
                            : 'background:#e5e7eb;color:#6b7280;cursor:not-allowed;'">
                    Create Group
                    <span x-show="selectedMembers.length > 0" x-text="'(' + (selectedMembers.length + 1) + ' people)'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Add member modal --}}
    <div x-show="showAddMember" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="showAddMember = false">
        <div @click.outside="showAddMember = false"
             class="bg-white rounded-xl shadow-xl w-full max-w-sm max-h-[70vh] flex flex-col overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700">Add Member</span>
                <input type="text" x-model="newChatQuery" @input.debounce.300ms="loadUsers()"
                       placeholder="Search people…"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-50">
                <template x-for="u in newChatResults" :key="u.id">
                    <button @click="addMember(u)" class="w-full text-left px-5 py-2.5 flex items-center gap-3 hover:bg-gray-50">
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
        showAddMember: false,
        newChatMode: 'direct',
        newChatQuery: '',
        newChatResults: [],
        groupName: '',
        selectedMembers: [],
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
            if (idx === -1) {
                // Unknown conversation (e.g. a group we were just added to) — reload the list
                this.loadConversations();
                return;
            }

            const c = { ...this.conversations[idx] };
            c.last_message = { body: e.body, sender_id: e.sender_id, sender_name: e.sender_name, created_at: e.created_at };
            c.last_message_at = e.created_at;
            if (!this.active || this.active.id !== e.conversation_id) {
                c.unread_count = (c.unread_count || 0) + 1;
            }
            this.conversations = [c, ...this.conversations.filter((_, i) => i !== idx)];
        },

        openNewChatModal() {
            this.showNewChat = true;
            this.newChatMode = 'direct';
            this.newChatQuery = '';
            this.newChatResults = [];
            this.groupName = '';
            this.selectedMembers = [];
            this.loadUsers();
        },

        toggleMember(u) {
            const idx = this.selectedMembers.findIndex(m => m.id === u.id);
            if (idx === -1) {
                this.selectedMembers = [...this.selectedMembers, u];
            } else {
                this.selectedMembers = this.selectedMembers.filter(m => m.id !== u.id);
            }
        },

        isSelected(userId) {
            return this.selectedMembers.some(m => m.id === userId);
        },

        canCreateGroup() {
            return this.groupName.trim().length > 0 && this.selectedMembers.length > 0;
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
            if (!res.ok) return;
            const conversation = await res.json();

            if (!this.conversations.find(c => c.id === conversation.id)) {
                this.conversations = [conversation, ...this.conversations];
            }

            this.showNewChat = false;
            this.openConversation(conversation);
        },

        async createGroup() {
            if (!this.groupName.trim() || this.selectedMembers.length === 0) return;

            const res = await fetch('{{ route('chat.conversations.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    name: this.groupName.trim(),
                    member_ids: this.selectedMembers.map(m => m.id),
                }),
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.message || 'Failed to create group. Please try again.');
                return;
            }
            const conversation = await res.json();

            this.conversations = [conversation, ...this.conversations];
            this.showNewChat = false;
            this.groupName = '';
            this.selectedMembers = [];
            this.openConversation(conversation);
        },

        async addMember(u) {
            if (!this.active) return;

            await fetch(`/chat/conversations/${this.active.id}/participants`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ user_id: u.id }),
            });

            this.active.member_count = (this.active.member_count || 0) + 1;
            this.showAddMember = false;
        },

        async openConversation(c) {
            if (this.currentChannelName) {
                window.Echo?.leave(this.currentChannelName);
            }

            this.active = c;
            this.messages = [];

            const res = await fetch(`/chat/conversations/${c.id}`);
            if (!res.ok) { this.active = null; return; }
            const data = await res.json();
            this.messages = data.messages;
            this.active = data.conversation;

            this.conversations = this.conversations.map(x => x.id === c.id ? { ...x, unread_count: 0 } : x);

            this.currentChannelName = 'conversation.' + c.id;
            window.Echo?.private(this.currentChannelName)
                .listen('.message.sent', e => {
                    if (!this.messages.find(m => m.id === e.id)) {
                        this.messages.push({
                            ...e,
                            is_mine: e.sender_id === this.currentUser.id,
                            status: 'sent',
                        });
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
            if (!res.ok) { this.draft = body; return; }
            const message = await res.json();
            this.messages = [...this.messages, message];

            const activeId = this.active.id;
            const idx = this.conversations.findIndex(c => c.id === activeId);
            if (idx !== -1) {
                const updated = { ...this.conversations[idx], last_message: { body: message.body, sender_id: message.sender_id, sender_name: message.sender_name, created_at: message.created_at }, last_message_at: message.created_at };
                this.conversations = [updated, ...this.conversations.filter((_, i) => i !== idx)];
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
