<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Permite acesso se o usuário faz parte da conversa
    return \App\Models\Conversation::where('id', $conversationId)
        ->where(function($q) use ($user) {
            $q->where('user_one_id', $user->id_usuarios)
              ->orWhere('user_two_id', $user->id_usuarios);
        })->exists();
}); 