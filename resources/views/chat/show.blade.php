@extends('layouts.app')

@section('title', 'Chat')

@section('content')
@php
    $others         = $conversation->participants->where('user_id', '!=', auth()->id())->values();
    $chatName       = $conversation->type === 'group'
        ? ($conversation->name ?? 'Group')
        : ($others->first()?->user->name ?? 'Unknown');
    $participantIds = $conversation->participants->pluck('user_id')->toArray();
@endphp

<style>
#chat-container { height: calc(100svh - 7rem); }
@media (min-width: 768px) { #chat-container { height: calc(100vh - 10rem); } }
</style>

<div
    id="chat-container"
    x-data="chatApp()"
    x-init="init()"
    @chat:new-message.window="onExternalMessage($event.detail)"
    class="flex bg-white md:rounded-xl md:border md:border-gray-200 overflow-hidden -mx-4 sm:-mx-6 md:mx-0"
>

    {{-- ─── Left Panel (hidden on mobile, shown md+) ──────────────────────── --}}
    <div class="hidden md:flex md:w-64 lg:w-72 flex-shrink-0 border-r border-gray-200 flex-col">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('chat.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-gray-800 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Messages
            </a>
            <button @click="openNewChat()" class="p-1 rounded-lg hover:bg-gray-100 text-gray-500" title="New chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <template x-if="conversations.length === 0 && !loadingConvs">
                <p class="text-xs text-gray-400 text-center py-8">No conversations</p>
            </template>
            <template x-for="conv in conversations" :key="conv.id">
                <a :href="'/chat/' + conv.id"
                   class="flex items-center gap-2.5 px-3 py-2.5 border-b border-gray-50 hover:bg-gray-50 transition"
                   :class="conv.id == activeConvId ? 'bg-blue-50 border-l-2 border-l-blue-500' : ''">
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

    {{-- ─── Right Panel ─────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <div class="px-3 md:px-4 py-3 border-b border-gray-100 flex items-center gap-2 md:gap-3 flex-shrink-0">
            {{-- Mobile back button --}}
            <a href="{{ route('chat.index') }}" class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 flex-shrink-0 -ml-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center font-semibold text-blue-700 text-sm flex-shrink-0">
                {{ strtoupper(substr($chatName, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-sm text-gray-800" x-text="groupName || @js($chatName)"></span>
                    @if($conversation->type === 'private' && $others->isNotEmpty())
                        <span x-show="onlineUsers.includes({{ $others->first()->user_id }})"
                              class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0" title="Online"></span>
                    @endif
                </div>
                @if($conversation->type === 'group')
                    <p class="text-xs text-gray-400">
                        <span x-text="memberCount"></span> members
                        &mdash; <span x-text="onlineUsers.filter(id => @json($participantIds).includes(id)).length"></span> online
                    </p>
                @else
                    <p class="text-xs text-gray-400"
                       x-text="onlineUsers.includes({{ $others->first()?->user_id ?? 0 }}) ? 'Online' : 'Offline'"></p>
                @endif
            </div>

            {{-- Header actions --}}
            <div class="flex items-center gap-1 flex-shrink-0">
                {{-- Search --}}
                <button @click="toggleSearch()" :class="showSearch ? 'bg-gray-100 text-blue-600' : 'text-gray-500'"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition" title="Search messages">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                {{-- Mute toggle --}}
                <button @click="toggleMute()" :title="isMuted ? 'Unmute notifications' : 'Mute notifications'"
                        class="p-1.5 rounded-lg hover:bg-gray-100 transition"
                        :class="isMuted ? 'text-gray-400' : 'text-gray-500'">
                    <template x-if="isMuted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15zM17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                    </template>
                    <template x-if="!isMuted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M12 6v12m0 0l-3.536-3.536M12 18l3.536-3.536M5.636 5.636a9 9 0 000 12.728"/></svg>
                    </template>
                </button>

                @if($conversation->type === 'group')
                    {{-- Group management --}}
                    <button @click="openGroupPanel()" title="Group settings"
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                    {{-- Leave group --}}
                    <button @click="leaveGroup()" title="Leave group"
                            class="p-1.5 rounded-lg hover:bg-red-50 text-gray-500 hover:text-red-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
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

        {{-- Search panel --}}
        <div x-show="showSearch" x-cloak class="flex-1 flex flex-col min-h-0 border-t border-gray-100">
            <div class="px-4 py-2.5 border-b border-gray-100 flex items-center gap-2 flex-shrink-0 bg-gray-50">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-ref="searchInput" type="text" x-model="searchQuery"
                       @input="runSearch()" @keydown.escape="closeSearch()"
                       placeholder="Search in this conversation…"
                       class="flex-1 text-sm bg-transparent focus:outline-none text-gray-700 placeholder-gray-400">
                <button @click="closeSearch()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <template x-if="searching">
                    <div class="flex justify-center py-8"><div class="w-5 h-5 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div></div>
                </template>
                <template x-if="!searching && searchQuery.length < 2">
                    <p class="text-xs text-gray-400 text-center py-8">Type at least 2 characters to search</p>
                </template>
                <template x-if="!searching && searchQuery.length >= 2 && searchResults.length === 0">
                    <p class="text-xs text-gray-400 text-center py-8">No messages found for "<span x-text="searchQuery"></span>"</p>
                </template>
                <template x-for="result in searchResults" :key="result.id">
                    <button @click="goToMessage(result.id)"
                            class="w-full text-left px-3 py-2.5 rounded-xl hover:bg-gray-50 border border-gray-100 transition block">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-700" x-text="result.sender_name"></span>
                            <span class="text-[10px] text-gray-400" x-text="formatTime(result.created_at)"></span>
                        </div>
                        <p class="text-sm text-gray-600 truncate" x-text="result.body"></p>
                    </button>
                </template>
            </div>
        </div>

        {{-- Message list --}}
        <div x-ref="messageList" x-show="!showSearch" class="flex-1 overflow-y-auto px-3 md:px-4 py-4 space-y-2 relative">

            {{-- Load more --}}
            <div class="flex justify-center pb-2">
                <template x-if="nextCursor">
                    <button @click="loadMore()" :disabled="loadingMore"
                            class="text-xs text-blue-600 hover:underline disabled:opacity-50 flex items-center gap-1">
                        <template x-if="loadingMore">
                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </template>
                        <span x-text="loadingMore ? 'Loading…' : 'Load earlier messages'"></span>
                    </button>
                </template>
            </div>

            <template x-if="loadingMessages">
                <div class="flex justify-center py-8">
                    <div class="w-6 h-6 rounded-full border-2 border-blue-600 border-t-transparent animate-spin"></div>
                </div>
            </template>

            <template x-for="msg in messages" :key="msg.id ?? msg._tempId">
                <div class="flex items-end gap-2 group/msg"
                     :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">

                    {{-- Avatar (others only) --}}
                    <template x-if="msg.sender_id != currentUserId">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600 flex-shrink-0 mb-1"
                             x-text="msg.sender_initial || '?'"></div>
                    </template>

                    {{-- Bubble + actions --}}
                    <div class="flex items-end gap-1" :class="msg.sender_id == currentUserId ? 'flex-row-reverse' : 'flex-row'">

                        {{-- Action buttons: always visible on mobile, fade-in on hover on desktop --}}
                        <template x-if="!msg.deleted && !msg.pending">
                            <div class="flex flex-col gap-0.5 mb-1 transition-opacity duration-150
                                        md:opacity-0 md:pointer-events-none
                                        group-hover/msg:opacity-100 group-hover/msg:pointer-events-auto">
                                {{-- Reply --}}
                                <button @click="setReply(msg)" title="Reply"
                                        class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 shadow-sm">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                </button>
                                {{-- Edit (own messages only) --}}
                                <template x-if="msg.sender_id == currentUserId">
                                    <button @click="startEdit(msg)" title="Edit"
                                            class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-gray-50 shadow-sm">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </template>
                                {{-- Delete (own messages only) --}}
                                <template x-if="msg.sender_id == currentUserId">
                                    <button @click="deleteMessage(msg)" title="Delete"
                                            class="w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:bg-red-50 shadow-sm">
                                        <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Retry button for failed messages --}}
                        <template x-if="msg.failed">
                            <button @click="retryMessage(msg)" title="Retry"
                                    class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center hover:bg-red-200 mb-1">
                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                        </template>

                        <div class="flex flex-col max-w-[85%] sm:max-w-[75%] md:max-w-[68%]"
                             :class="msg.sender_id == currentUserId ? 'items-end' : 'items-start'"
                             :data-msg-id="msg.id">

                            {{-- Sender name (group) --}}
                            @if($conversation->type === 'group')
                                <template x-if="msg.sender_id != currentUserId">
                                    <span class="text-[10px] font-medium text-gray-500 mb-0.5 px-1" x-text="msg.sender_name"></span>
                                </template>
                            @endif

                            {{-- Edit mode --}}
                            <template x-if="editingId === msg.id">
                                <div class="w-full">
                                    <textarea x-model="editBody"
                                              @keydown.enter.prevent="if(!$event.shiftKey) submitEdit()"
                                              @keydown.escape="cancelEdit()"
                                              class="w-full border border-blue-400 rounded-xl px-3 py-2 text-sm focus:outline-none resize-none"
                                              rows="2"></textarea>
                                    <div class="flex gap-1 mt-1 justify-end">
                                        <button @click="cancelEdit()" class="text-xs text-gray-500 px-2 py-1 rounded hover:bg-gray-100">Cancel</button>
                                        <button @click="submitEdit()" class="text-xs text-white bg-blue-600 px-2 py-1 rounded hover:bg-blue-700">Save</button>
                                    </div>
                                </div>
                            </template>

                            {{-- Normal bubble --}}
                            <template x-if="editingId !== msg.id">
                                <div class="rounded-2xl px-3.5 py-2 text-sm leading-relaxed break-words w-full"
                                     :class="{
                                         'bg-blue-600 text-white rounded-br-sm': msg.sender_id == currentUserId && !msg.deleted,
                                         'bg-gray-100 text-gray-800 rounded-bl-sm': msg.sender_id != currentUserId && !msg.deleted,
                                         'bg-gray-100 text-gray-400 italic': msg.deleted,
                                         'opacity-60': msg.pending
                                     }">
                                    {{-- Reply quote --}}
                                    <template x-if="msg.reply_to">
                                        <div class="mb-1.5 px-2 py-1 rounded-lg border-l-2 cursor-pointer"
                                             :class="msg.sender_id == currentUserId ? 'bg-blue-500 border-blue-300' : 'bg-gray-200 border-gray-400'"
                                             @click="scrollToMessage(msg.reply_to.id)">
                                            <p class="text-[10px] font-semibold opacity-80" x-text="msg.reply_to.sender_name"></p>
                                            <template x-if="msg.reply_to.deleted">
                                                <p class="text-xs opacity-70 italic">Deleted message</p>
                                            </template>
                                            <template x-if="!msg.reply_to.deleted && msg.reply_to.type === 'image'">
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <img :src="msg.reply_to.metadata?.url" class="w-10 h-10 rounded object-cover flex-shrink-0">
                                                    <span class="text-xs opacity-70">Photo</span>
                                                </div>
                                            </template>
                                            <template x-if="!msg.reply_to.deleted && msg.reply_to.type === 'file'">
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <svg class="w-4 h-4 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="text-xs opacity-70 truncate" x-text="msg.reply_to.metadata?.filename || 'File'"></span>
                                                </div>
                                            </template>
                                            <template x-if="!msg.reply_to.deleted && msg.reply_to.type !== 'image' && msg.reply_to.type !== 'file'">
                                                <p class="text-xs opacity-70 truncate" x-text="msg.reply_to.body"></p>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="msg.deleted">
                                        <span class="italic">This message was deleted</span>
                                    </template>
                                    <template x-if="!msg.deleted && msg.type === 'image'">
                                        <img :src="msg.metadata?.url" class="max-w-full rounded-lg max-h-56 object-cover cursor-pointer block" @click="window.open(msg.metadata.url, '_blank')">
                                    </template>
                                    <template x-if="!msg.deleted && msg.type === 'file'">
                                        <a :href="msg.metadata?.url" target="_blank" rel="noopener"
                                           class="flex items-center gap-2.5 hover:opacity-75 transition min-w-0 no-underline">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                                 :class="msg.sender_id == currentUserId ? 'bg-blue-500' : 'bg-gray-200'">
                                                <svg class="w-5 h-5" :class="msg.sender_id == currentUserId ? 'text-white' : 'text-gray-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium truncate" x-text="msg.metadata?.filename"></p>
                                                <p class="text-[10px] opacity-70" x-text="msg.metadata ? formatFileSize(msg.metadata.size) : ''"></p>
                                            </div>
                                        </a>
                                    </template>
                                    <template x-if="!msg.deleted && msg.type !== 'image' && msg.type !== 'file'">
                                        <span x-text="msg.body"></span>
                                    </template>
                                </div>
                            </template>

                            {{-- Timestamp + edited + read receipt --}}
                            <template x-if="editingId !== msg.id">
                                <div class="flex items-center gap-1 mt-0.5 px-1"
                                     :class="msg.sender_id == currentUserId ? 'flex-row-reverse' : ''">
                                    <span class="text-[10px] text-gray-400" x-text="formatMsgTime(msg.created_at)"></span>
                                    <template x-if="msg.edited_at">
                                        <span class="text-[10px] text-gray-400 italic">edited</span>
                                    </template>
                                    <template x-if="msg.sender_id == currentUserId && !msg.pending && !msg.deleted">
                                        <span class="text-[10px]"
                                              :class="isRead(msg) ? 'text-blue-500' : 'text-gray-400'"
                                              :title="isRead(msg) ? 'Read' : 'Delivered'">
                                            <template x-if="isRead(msg)"><span>✓✓</span></template>
                                            <template x-if="!isRead(msg)"><span>✓</span></template>
                                        </span>
                                    </template>
                                    <template x-if="msg.pending">
                                        <span class="text-[10px] text-gray-300">⏳</span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <template x-if="typingUsers.length > 0">
                <div class="flex items-center gap-2 px-1">
                    <div class="flex gap-0.5 items-center bg-gray-100 rounded-full px-3 py-1.5">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                    <span class="text-xs text-gray-400" x-text="typingLabel()"></span>
                </div>
            </template>
        </div>

        {{-- Error banner --}}
        <div x-show="errorMsg" x-cloak
             class="px-4 py-2 bg-red-50 border-t border-red-200 flex items-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="text-xs text-red-700 flex-1" x-text="errorMsg"></span>
            <button @click="errorMsg = null" class="text-red-400 hover:text-red-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Reply preview bar --}}
        <template x-if="replyTo">
            <div class="px-4 py-2 border-t border-gray-100 bg-blue-50 flex items-center gap-2 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-blue-700" x-text="'Replying to ' + replyTo.sender_name"></p>
                    <template x-if="!replyTo.deleted && replyTo.type === 'image'">
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <img :src="replyTo.metadata?.url" class="w-8 h-8 rounded object-cover flex-shrink-0">
                            <span class="text-xs text-gray-500">Photo</span>
                        </div>
                    </template>
                    <template x-if="!replyTo.deleted && replyTo.type === 'file'">
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="text-xs text-gray-500 truncate" x-text="replyTo.metadata?.filename || 'File'"></span>
                        </div>
                    </template>
                    <template x-if="replyTo.deleted || (replyTo.type !== 'image' && replyTo.type !== 'file')">
                        <p class="text-xs text-gray-500 truncate" x-text="replyTo.deleted ? 'Deleted message' : replyTo.body"></p>
                    </template>
                </div>
                <button @click="cancelReply()" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>

        {{-- File preview bar --}}
        <template x-if="selectedFile">
            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 flex items-center gap-3 flex-shrink-0">
                <template x-if="filePreviewUrl">
                    <img :src="filePreviewUrl" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                </template>
                <template x-if="!filePreviewUrl">
                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </template>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate" x-text="selectedFile.name"></p>
                    <p class="text-xs text-gray-400" x-text="formatFileSize(selectedFile.size)"></p>
                </div>
                <button @click="sendFile()" :disabled="uploading"
                        class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50 flex items-center gap-1.5">
                    <template x-if="uploading">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </template>
                    <span x-text="uploading ? 'Uploading…' : 'Send'"></span>
                </button>
                <button @click="clearFile()" :disabled="uploading" class="text-gray-400 hover:text-gray-600 disabled:opacity-40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>

        {{-- Compose area --}}
        <div class="px-3 md:px-4 py-3 border-t border-gray-100 flex-shrink-0">
            <div class="flex items-end gap-1.5 md:gap-2">
                {{-- File attachment --}}
                <button @click="pickFile()" title="Attach file"
                        class="w-9 h-9 md:w-9 md:h-9 rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center flex-shrink-0 transition touch-manipulation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                </button>
                <input x-ref="fileInput" type="file" class="hidden"
                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                       @change="onFileSelected($event)">
                <textarea
                    x-model="newMessage"
                    @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                    @input="onTyping(); $el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 128) + 'px'"
                    placeholder="Type a message… (Enter to send, Shift+Enter for new line)"
                    rows="1"
                    class="flex-1 border border-gray-300 rounded-2xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none leading-5 max-h-32 overflow-y-auto"
                    style="min-height: 42px;"
                ></textarea>
                <button @click="sendMessage()" :disabled="!newMessage.trim()"
                        class="w-11 h-11 md:w-10 md:h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center flex-shrink-0 transition disabled:opacity-40 disabled:cursor-not-allowed touch-manipulation">
                    <svg class="w-5 h-5 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── New Chat Modal ─────────────────────────────────────────────────── --}}
    <div x-show="showNewChat" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @keydown.escape.window="showNewChat = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-2 sm:mx-4 max-h-[90svh] flex flex-col" @click.outside="showNewChat = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">New Conversation</h3>
                <button @click="showNewChat = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div class="flex gap-2">
                    <button @click="newChatType = 'private'; selectedUsers = []"
                            :class="newChatType === 'private' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="flex-1 py-2 text-sm font-medium rounded-lg transition">Private</button>
                    <button @click="newChatType = 'group'; selectedUsers = []"
                            :class="newChatType === 'group' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="flex-1 py-2 text-sm font-medium rounded-lg transition">Group</button>
                </div>
                <div x-show="newChatType === 'group'">
                    <input type="text" x-model="newChatGroupName" placeholder="Group name"
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
                                <input type="checkbox" :checked="selectedUsers.includes(user.id)"
                                       @change="toggleUser(user.id)" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            </label>
                        </template>
                    </div>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button @click="showNewChat = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button @click="createConversation()" :disabled="selectedUsers.length === 0 || creating"
                        class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!creating">Start Chat</span>
                    <span x-show="creating">Creating…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Group Management Modal ─────────────────────────────────────────── --}}
    @if($conversation->type === 'group')
    <div x-show="showGroupPanel" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @keydown.escape.window="showGroupPanel = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-2 sm:mx-4 max-h-[90svh] md:max-h-[80vh] flex flex-col" @click.outside="showGroupPanel = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 class="font-semibold text-gray-800">Group Settings</h3>
                <button @click="showGroupPanel = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 p-5 space-y-5">
                {{-- Rename --}}
                @if(in_array($myRole, ['owner', 'admin']))
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Rename Group</p>
                    <div class="flex gap-2">
                        <input type="text" x-model="groupNewName" placeholder="Group name"
                               @keydown.enter="renameGroup()"
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <button @click="renameGroup()" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Save</button>
                    </div>
                </div>
                @endif

                {{-- Members --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Members (<span x-text="groupMembers.length"></span>)</p>
                    <div class="space-y-1 max-h-48 overflow-y-auto">
                        <template x-for="m in groupMembers" :key="m.user_id">
                            <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-50">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-sm font-semibold text-blue-700 flex-shrink-0"
                                     x-text="m.name.charAt(0).toUpperCase()"></div>
                                <span class="text-sm text-gray-700 flex-1" x-text="m.name"></span>
                                <span class="text-[10px] text-gray-400 capitalize" x-text="m.role"></span>
                                @if(in_array($myRole, ['owner', 'admin']))
                                <template x-if="m.user_id != currentUserId">
                                    <button @click="removeMember(m.user_id)" class="text-red-400 hover:text-red-600 ml-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </template>
                                @endif
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Add members --}}
                @if(in_array($myRole, ['owner', 'admin']))
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Add Members</p>
                    <div x-show="loadingGroupUsers" class="py-2 text-center text-sm text-gray-400">Loading…</div>
                    <div class="space-y-1 max-h-40 overflow-y-auto" x-show="!loadingGroupUsers">
                        <template x-for="user in addableUsers" :key="user.id">
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 flex-shrink-0"
                                     x-text="user.name.charAt(0).toUpperCase()"></div>
                                <span class="text-sm text-gray-700 flex-1" x-text="user.name"></span>
                                <input type="checkbox" :checked="newMemberIds.includes(user.id)"
                                       @change="toggleNewMember(user.id)" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            </label>
                        </template>
                        <template x-if="addableUsers.length === 0">
                            <p class="text-xs text-gray-400 px-2">All users are already members</p>
                        </template>
                    </div>
                    <template x-if="newMemberIds.length > 0">
                        <button @click="addMembers()" class="mt-2 w-full py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                            Add <span x-text="newMemberIds.length"></span> member(s)
                        </button>
                    </template>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

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

$initialMembers = $conversation->participants->whereNull('left_at')->map(fn($p) => [
    'user_id' => $p->user_id,
    'name'    => $p->user->name ?? 'Unknown',
    'role'    => $p->role,
]);
@endphp
<script>
function chatApp() {
    const CONV_ID         = {{ $conversation->id }};
    const CURRENT_USER    = {{ auth()->id() }};
    const CURRENT_INITIAL = @json(strtoupper(substr(auth()->user()->name, 0, 1)));
    const CACHE_KEY       = 'chat_msg_' + CONV_ID;
    const PENDING_KEY     = 'chat_pend_' + CONV_ID;
    const INITIAL_CONVS   = @json($initialConvs);
    const IS_GROUP        = {{ $conversation->type === 'group' ? 'true' : 'false' }};
    const MY_ROLE         = @json($myRole);

    return {
        // Core state
        activeConvId:   CONV_ID,
        currentUserId:  CURRENT_USER,
        messages:       [],
        conversations:  INITIAL_CONVS,
        onlineUsers:    [],
        readStatus:     {},
        newMessage:     '',
        loadingMessages: true,
        loadingConvs:   false,
        isOnline:       navigator.onLine,
        isConnected:    window.Echo?.connector?.pusher?.connection?.state === 'connected',

        // Pagination
        nextCursor:     null,
        loadingMore:    false,

        // Reply
        replyTo:        null,

        // Edit
        editingId:      null,
        editBody:       '',

        // Typing
        typingUsers:    [],
        _typingDebounce: null,
        _typingTimers:  {},

        // Mute
        isMuted:        @json($isMuted),

        // Group
        groupName:      @json($conversation->name),
        memberCount:    {{ $conversation->participants->whereNull('left_at')->count() }},
        groupMembers:   @json($initialMembers->values()),
        showGroupPanel: false,
        groupNewName:   @json($conversation->name ?? ''),
        addableUsers:   [],
        newMemberIds:   [],
        loadingGroupUsers: false,

        // Error
        errorMsg:       null,
        _errorTimer:    null,

        // Search
        showSearch:     false,
        searchQuery:    '',
        searchResults:  [],
        searching:      false,
        _searchDebounce: null,

        // File upload
        selectedFile:   null,
        filePreviewUrl: null,
        uploading:      false,

        // New chat modal
        showNewChat:    false,
        newChatType:    'private',
        newChatGroupName: '',
        modalUsers:     [],
        selectedUsers:  [],
        loadingUsers:   false,
        creating:       false,

        // ─── Lifecycle ───────────────────────────────────────────────────────

        init() {
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                try {
                    this.messages = JSON.parse(cached);
                    this.loadingMessages = false;
                    this.scrollBottom();
                } catch(e) {}
            }
            this.loadMessages();
            this.subscribeEcho();
            window.addEventListener('online',  () => { this.isOnline = true;  this.flushQueue(); });
            window.addEventListener('offline', () => { this.isOnline = false; });
        },

        // ─── Messages ────────────────────────────────────────────────────────

        async loadMessages() {
            try {
                const res = await axios.get('/chat/' + CONV_ID + '/messages');
                this.messages   = res.data.data;
                this.nextCursor = res.data.next_cursor;
                if (res.data.read_status) Object.assign(this.readStatus, res.data.read_status);
                this.loadingMessages = false;
                this.cacheMessages();
                this.markRead();
                this.scrollBottom();
            } catch(e) { this.loadingMessages = false; }
        },

        async loadMore() {
            if (!this.nextCursor || this.loadingMore) return;
            this.loadingMore = true;
            const prevScrollHeight = this.$refs.messageList.scrollHeight;
            try {
                const res = await axios.get('/chat/' + CONV_ID + '/messages?cursor=' + this.nextCursor);
                this.messages   = [...res.data.data, ...this.messages];
                this.nextCursor = res.data.next_cursor;
                this.$nextTick(() => {
                    // Keep scroll position after prepend
                    const el = this.$refs.messageList;
                    el.scrollTop = el.scrollHeight - prevScrollHeight;
                });
            } catch(e) {}
            this.loadingMore = false;
        },

        async sendMessage() {
            const body = this.newMessage.trim();
            if (!body) return;
            this.newMessage = '';

            const tempId    = '_' + Date.now();
            const replySnap = this.replyTo ? { ...this.replyTo } : null;
            this.cancelReply();

            this.messages.push({
                _tempId: tempId, sender_id: CURRENT_USER,
                sender_initial: CURRENT_INITIAL, body,
                created_at: new Date().toISOString(), type: 'text',
                pending: !this.isOnline, reply_to: replySnap,
            });

            const conv = this.conversations.find(c => c.id === CONV_ID);
            if (conv) { conv.last_message = body; conv.last_message_at = new Date().toISOString(); }

            this.scrollBottom();

            if (!this.isOnline) {
                const queue = JSON.parse(localStorage.getItem(PENDING_KEY) || '[]');
                queue.push({ body, tempId, reply_to_id: replySnap?.id ?? null });
                localStorage.setItem(PENDING_KEY, JSON.stringify(queue));
                return;
            }

            try {
                const res = await axios.post('/chat/' + CONV_ID + '/messages', {
                    body,
                    reply_to_id: replySnap?.id ?? null,
                });
                const idx = this.messages.findIndex(m => m._tempId === tempId);
                if (idx !== -1) {
                    this.messages.splice(idx, 1, { ...res.data, sender_initial: CURRENT_INITIAL, pending: false });
                    if (conv) conv.last_message_at = res.data.created_at;
                    this.cacheMessages();
                }
            } catch(e) {
                const idx = this.messages.findIndex(m => m._tempId === tempId);
                if (idx !== -1) this.messages[idx].failed = true;
                this.newMessage = body;
                this.showError('Message failed to send. Click the retry button to try again.');
            }
        },

        async retryMessage(msg) {
            const body       = msg.body;
            const replyToId  = msg.reply_to?.id ?? null;
            const idx        = this.messages.findIndex(m => m._tempId === msg._tempId);
            if (idx !== -1) { this.messages[idx].failed = false; this.messages[idx].pending = true; }
            try {
                const res = await axios.post('/chat/' + CONV_ID + '/messages', { body, reply_to_id: replyToId });
                if (idx !== -1) {
                    this.messages.splice(idx, 1, { ...res.data, sender_initial: CURRENT_INITIAL, pending: false });
                    this.cacheMessages();
                }
            } catch(e) {
                if (idx !== -1) this.messages[idx].failed = true;
            }
        },

        async deleteMessage(msg) {
            if (!confirm('Delete this message?')) return;
            try {
                await axios.delete('/chat/' + CONV_ID + '/messages/' + msg.id);
                const idx = this.messages.findIndex(m => m.id === msg.id);
                if (idx !== -1) { this.messages[idx].deleted = true; this.messages[idx].body = null; }
            } catch(e) { this.showError('Failed to delete message.'); }
        },

        startEdit(msg) {
            this.editingId = msg.id;
            this.editBody  = msg.body;
        },

        cancelEdit() { this.editingId = null; this.editBody = ''; },

        async submitEdit() {
            const body = this.editBody.trim();
            if (!body || !this.editingId) return;
            try {
                const res = await axios.patch('/chat/' + CONV_ID + '/messages/' + this.editingId, { body });
                const idx = this.messages.findIndex(m => m.id === this.editingId);
                if (idx !== -1) {
                    this.messages[idx].body      = res.data.body;
                    this.messages[idx].edited_at = res.data.edited_at;
                }
                this.cancelEdit();
            } catch(e) { this.showError('Failed to save edit.'); }
        },

        // ─── Reply ───────────────────────────────────────────────────────────

        setReply(msg) {
            this.replyTo = {
                id:          msg.id,
                type:        msg.type,
                body:        msg.body,
                metadata:    msg.metadata,
                sender_name: msg.sender_name || 'You',
                deleted:     msg.deleted,
            };
            this.$nextTick(() => document.querySelector('textarea').focus());
        },

        cancelReply() { this.replyTo = null; },

        scrollToMessage(id) {
            const el = document.querySelector(`[data-msg-id="${id}"]`);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        },

        // ─── Typing ──────────────────────────────────────────────────────────

        onTyping() {
            if (!this.isOnline || !this.newMessage.trim()) return;
            clearTimeout(this._typingDebounce);
            this._typingDebounce = setTimeout(() => {
                axios.post('/chat/' + CONV_ID + '/typing').catch(() => {});
            }, 400);
        },

        typingLabel() {
            if (this.typingUsers.length === 1) return this.typingUsers[0] + ' is typing…';
            if (this.typingUsers.length === 2) return this.typingUsers.join(' and ') + ' are typing…';
            return 'Several people are typing…';
        },

        // ─── Conversation management ─────────────────────────────────────────

        async toggleMute() {
            try {
                const res = await axios.patch('/chat/' + CONV_ID + '/mute');
                this.isMuted = res.data.muted;
            } catch(e) {}
        },

        async leaveGroup() {
            if (!confirm('Leave this group?')) return;
            try {
                await axios.post('/chat/' + CONV_ID + '/leave');
                window.location.href = '/chat';
            } catch(e) {}
        },

        async openGroupPanel() {
            this.showGroupPanel   = true;
            this.loadingGroupUsers = true;
            this.newMemberIds     = [];
            try {
                const res = await axios.get('/chat/users');
                const memberIds = this.groupMembers.map(m => m.user_id);
                this.addableUsers = res.data.filter(u => !memberIds.includes(u.id));
            } catch(e) {}
            this.loadingGroupUsers = false;
        },

        toggleNewMember(id) {
            if (this.newMemberIds.includes(id)) this.newMemberIds = this.newMemberIds.filter(i => i !== id);
            else this.newMemberIds.push(id);
        },

        async addMembers() {
            if (!this.newMemberIds.length) return;
            try {
                await axios.post('/chat/' + CONV_ID + '/members', { user_ids: this.newMemberIds });
                // Reload member list from addable users
                const added = this.addableUsers.filter(u => this.newMemberIds.includes(u.id));
                added.forEach(u => this.groupMembers.push({ user_id: u.id, name: u.name, role: 'member' }));
                this.addableUsers  = this.addableUsers.filter(u => !this.newMemberIds.includes(u.id));
                this.newMemberIds  = [];
                this.memberCount   = this.groupMembers.length;
            } catch(e) {}
        },

        async removeMember(userId) {
            if (!confirm('Remove this member?')) return;
            try {
                await axios.delete('/chat/' + CONV_ID + '/members/' + userId);
                this.groupMembers = this.groupMembers.filter(m => m.user_id !== userId);
                this.memberCount  = this.groupMembers.length;
            } catch(e) {}
        },

        async renameGroup() {
            const name = this.groupNewName.trim();
            if (!name) return;
            try {
                const res = await axios.patch('/chat/' + CONV_ID + '/name', { name });
                this.groupName = res.data.name;
                const conv = this.conversations.find(c => c.id === CONV_ID);
                if (conv) conv.name = res.data.name;
            } catch(e) {}
        },

        // ─── Echo subscriptions ───────────────────────────────────────────────

        subscribeEcho() {
            window.Echo.private('conversation.' + CONV_ID)
                .listen('.message.sent', (e) => {
                    if (e.sender_id === CURRENT_USER) return;
                    if (this.messages.find(m => m.id === e.id)) return;
                    this.messages.push(e);
                    this.cacheMessages();
                    const conv = this.conversations.find(c => c.id === e.conversation_id);
                    if (conv) { conv.last_message = e.body; conv.last_message_at = e.created_at; conv.unread_count = (conv.unread_count || 0) + 1; }
                    this.markRead();
                    this.scrollBottom();
                })
                .listen('.message.read', (e) => {
                    if (e.user_id !== CURRENT_USER) this.readStatus[e.user_id] = e.read_at;
                    const conv = this.conversations.find(c => c.id === e.conversation_id);
                    if (conv && e.user_id === CURRENT_USER) conv.unread_count = 0;
                })
                .listen('.message.deleted', (e) => {
                    const idx = this.messages.findIndex(m => m.id === e.id);
                    if (idx !== -1) { this.messages[idx].deleted = true; this.messages[idx].body = null; }
                })
                .listen('.message.edited', (e) => {
                    const idx = this.messages.findIndex(m => m.id === e.id);
                    if (idx !== -1) { this.messages[idx].body = e.body; this.messages[idx].edited_at = e.edited_at; }
                })
                .listen('.user.typing', (e) => {
                    if (e.user_id === CURRENT_USER) return;
                    if (!this.typingUsers.includes(e.user_name)) this.typingUsers.push(e.user_name);
                    // Clear after 3s of no new events
                    clearTimeout(this._typingTimers[e.user_id]);
                    this._typingTimers[e.user_id] = setTimeout(() => {
                        this.typingUsers = this.typingUsers.filter(n => n !== e.user_name);
                    }, 3000);
                    this.scrollBottom();
                })
                .listen('.group.updated', (e) => {
                    if (e.type === 'renamed') {
                        this.groupName = e.name;
                        this.groupNewName = e.name;
                        const conv = this.conversations.find(c => c.id === CONV_ID);
                        if (conv) conv.name = e.name;
                    } else if (e.type === 'members_added') {
                        e.users.forEach(u => {
                            if (!this.groupMembers.find(m => m.user_id === u.user_id)) {
                                this.groupMembers.push(u);
                                this.memberCount++;
                            }
                        });
                    } else if (e.type === 'member_removed') {
                        this.groupMembers = this.groupMembers.filter(m => m.user_id !== e.user_id);
                        this.memberCount  = this.groupMembers.length;
                        // If the current user was removed, redirect out
                        if (e.user_id === CURRENT_USER) window.location.href = '/chat';
                    }
                });

            window.Echo.connector.pusher.connection.bind('connected',    () => { this.isConnected = true;  });
            window.Echo.connector.pusher.connection.bind('disconnected', () => { this.isConnected = false; });
            window.Echo.connector.pusher.connection.bind('unavailable',  () => { this.isConnected = false; });

            window.Echo.join('online')
                .here((users)  => { this.onlineUsers = users.map(u => u.id); })
                .joining((user) => { if (!this.onlineUsers.includes(user.id)) this.onlineUsers.push(user.id); })
                .leaving((user) => { this.onlineUsers = this.onlineUsers.filter(id => id !== user.id); });
        },

        onExternalMessage(e) {
            if (e.conversation_id === CONV_ID) return;
            const conv = this.conversations.find(c => c.id === e.conversation_id);
            if (conv) { conv.last_message = e.body; conv.last_message_at = new Date().toISOString(); conv.unread_count = (conv.unread_count || 0) + 1; }
        },

        // ─── Offline queue ────────────────────────────────────────────────────

        async flushQueue() {
            const queue = JSON.parse(localStorage.getItem(PENDING_KEY) || '[]');
            if (!queue.length) return;
            for (const item of queue) {
                const body      = typeof item === 'string' ? item : item.body;
                const tempId    = typeof item === 'string' ? null : item.tempId;
                const replyToId = typeof item === 'string' ? null : item.reply_to_id;
                try {
                    const res = await axios.post('/chat/' + CONV_ID + '/messages', { body, reply_to_id: replyToId });
                    if (tempId) {
                        const idx = this.messages.findIndex(m => m._tempId === tempId);
                        if (idx !== -1) this.messages.splice(idx, 1, { ...res.data, sender_initial: CURRENT_INITIAL });
                    }
                } catch(e) {}
            }
            localStorage.removeItem(PENDING_KEY);
            this.messages = this.messages.filter(m => !m.pending);
        },

        // ─── Utilities ────────────────────────────────────────────────────────

        markRead() {
            axios.post('/chat/' + CONV_ID + '/read').catch(() => {});
            const conv = this.conversations.find(c => c.id === CONV_ID);
            if (conv) conv.unread_count = 0;
        },

        cacheMessages() {
            try { localStorage.setItem(CACHE_KEY, JSON.stringify(this.messages.slice(-100))); } catch(e) {}
        },

        isRead(msg) {
            if (!msg.created_at) return false;
            const t = new Date(msg.created_at).getTime();
            return Object.entries(this.readStatus).some(([uid, readAt]) =>
                parseInt(uid) !== CURRENT_USER && new Date(readAt).getTime() >= t
            );
        },

        scrollBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messageList;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso), now = new Date();
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

        formatFileSize(bytes) {
            if (!bytes) return '';
            if (bytes < 1024)        return bytes + ' B';
            if (bytes < 1048576)     return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        },

        // ─── Error toast ──────────────────────────────────────────────────────

        showError(msg) {
            this.errorMsg = msg;
            clearTimeout(this._errorTimer);
            this._errorTimer = setTimeout(() => { this.errorMsg = null; }, 5000);
        },

        // ─── Search ───────────────────────────────────────────────────────────

        toggleSearch() {
            this.showSearch = !this.showSearch;
            if (this.showSearch) {
                this.searchQuery = '';
                this.searchResults = [];
                this.$nextTick(() => this.$refs.searchInput?.focus());
            }
        },

        closeSearch() {
            this.showSearch = false;
            this.searchQuery = '';
            this.searchResults = [];
        },

        runSearch() {
            clearTimeout(this._searchDebounce);
            if (this.searchQuery.length < 2) { this.searchResults = []; return; }
            this.searching = true;
            this._searchDebounce = setTimeout(async () => {
                try {
                    const res = await axios.get('/chat/' + CONV_ID + '/search', { params: { q: this.searchQuery } });
                    this.searchResults = res.data;
                } catch(e) {}
                this.searching = false;
            }, 350);
        },

        goToMessage(id) {
            this.closeSearch();
            this.$nextTick(() => {
                const el = document.querySelector('[data-msg-id="' + id + '"]');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        },

        // ─── File upload ──────────────────────────────────────────────────────

        pickFile() {
            this.$refs.fileInput.value = '';
            this.$refs.fileInput.click();
        },

        onFileSelected(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.selectedFile = file;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { this.filePreviewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.filePreviewUrl = null;
            }
        },

        clearFile() {
            this.selectedFile = null;
            this.filePreviewUrl = null;
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        async sendFile() {
            if (!this.selectedFile || this.uploading) return;
            this.uploading = true;
            const fd = new FormData();
            fd.append('file', this.selectedFile);
            try {
                const res = await axios.post('/chat/' + CONV_ID + '/upload', fd, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                this.messages.push({ ...res.data, pending: false });
                this.cacheMessages();
                this.clearFile();
                this.scrollBottom();
                const conv = this.conversations.find(c => c.id === CONV_ID);
                if (conv) {
                    conv.last_message    = res.data.type === 'image' ? '📷 Image' : '📎 ' + res.data.metadata?.filename;
                    conv.last_message_at = res.data.created_at;
                }
            } catch(e) {
                const msg = e.response?.data?.message || 'Failed to upload. Max 10 MB; allowed: images, PDF, Office, txt, zip.';
                this.showError(msg);
            }
            this.uploading = false;
        },

        // ─── New chat modal ───────────────────────────────────────────────────

        async openNewChat() {
            this.showNewChat   = true;
            this.selectedUsers = [];
            this.newChatGroupName = '';
            this.newChatType   = 'private';
            if (this.modalUsers.length === 0) {
                this.loadingUsers = true;
                try { const res = await axios.get('/chat/users'); this.modalUsers = res.data; }
                catch(e) { console.error(e); }
                finally { this.loadingUsers = false; }
            }
        },

        toggleUser(id) {
            if (this.selectedUsers.includes(id)) this.selectedUsers = this.selectedUsers.filter(u => u !== id);
            else {
                if (this.newChatType === 'private') this.selectedUsers = [id];
                else this.selectedUsers.push(id);
            }
        },

        async createConversation() {
            if (!this.selectedUsers.length) return;
            this.creating = true;
            try {
                const res = await axios.post('/chat/start', {
                    type: this.newChatType, user_ids: this.selectedUsers,
                    name: this.newChatType === 'group' ? this.newChatGroupName : null,
                });
                window.location.href = '/chat/' + res.data.id;
            } catch(e) { this.creating = false; }
        },
    };
}
</script>
@endpush
