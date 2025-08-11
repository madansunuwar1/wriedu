<?php

use Illuminate\Support\Facades\Broadcast;

// Authorize a user to listen on their own private channel for messages
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    // Only allow the authenticated user to listen on their own channel
    return (int) $user->id === (int) $userId;
});

// Authorize users for the presence channel
Broadcast::channel('presence-chat', function ($user) {
    // Any authenticated user can join the presence channel.
    // You can return an array of data about the user here.
    if ($user) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});