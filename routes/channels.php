<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private chat conversation — only active participants may subscribe
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return $user->conversations()
        ->where('conversation_id', $conversationId)
        ->wherePivotNull('left_at')
        ->exists();
});

// Personal notification channel — each user subscribes to their own
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Global presence channel for online indicators
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
