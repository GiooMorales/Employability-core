<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class ConversationController extends Controller
{
    // Lista as conversas do usuário logado
    public function index()
    {
        $userId = Auth::id();
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => function($q) { $q->latest()->limit(1); }])
            ->get()
            ->map(function($conv) use ($userId) {
                $other = $conv->user_one_id == $userId ? $conv->userTwo : $conv->userOne;
                $lastMessage = $conv->messages->first();
                return [
                    'id' => $conv->id,
                    'name' => $other->nome,
                    'avatar' => $other->url_foto,
                    'last_message' => $lastMessage ? $lastMessage->content : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                    'unread_count' => Message::where('conversation_id', $conv->id)->where('sender_id', '!=', $userId)->where('is_read', false)->count(),
                ];
            });
        return response()->json(['conversations' => $conversations]);
    }

    // Lista as mensagens de uma conversa
    public function messages($id)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        $other = $conversation->user_one_id == $userId ? $conversation->userTwo : $conversation->userOne;
        $messages = Message::where('conversation_id', $id)->orderBy('created_at')->get();
        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'name' => $other->nome,
                'avatar' => $other->url_foto,
                'is_online' => false // implementar status online depois
            ],
            'messages' => $messages
        ]);
    }

    // Envia uma mensagem
    public function sendMessage(Request $request, $id)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        // Só permite se existe conexão aceita
        $otherId = $conversation->user_one_id == $userId ? $conversation->user_two_id : $conversation->user_one_id;
        $isConnected = Connection::where(function($q) use ($userId, $otherId) {
            $q->where('user_id', $userId)->where('connected_user_id', $otherId);
        })->orWhere(function($q) use ($userId, $otherId) {
            $q->where('user_id', $otherId)->where('connected_user_id', $userId);
        })->where('status', 'aceita')->exists();
        if (!$isConnected) {
            return response()->json(['error' => 'Vocês não estão conectados!'], 403);
        }
        $msg = Message::create([
            'conversation_id' => $id,
            'sender_id' => $userId,
            'content' => $request->input('content'),
            'content_type' => $request->input('content_type', 'text'),
            'is_read' => false
        ]);
        // Aqui pode disparar evento Pusher
        // Event::dispatch(new \App\Events\NewMessage($msg));
        return response()->json(['message' => $msg]);
    }

    // Marca mensagens como lidas
    public function markAsRead($id)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }
        Message::where('conversation_id', $id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // Lista contatos conectados para iniciar conversa
    public function contacts()
    {
        $userId = Auth::id();
        $connections = Connection::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('connected_user_id', $userId);
        })->where('status', 'aceita')->get();
        $contactIds = $connections->map(function($c) use ($userId) {
            return $c->user_id == $userId ? $c->connected_user_id : $c->user_id;
        });
        $contacts = User::whereIn('id_usuarios', $contactIds)->get()->map(function($u) {
            return [
                'id' => $u->id_usuarios,
                'name' => $u->nome,
                'avatar' => $u->url_foto,
                'title' => $u->titulo
            ];
        });
        return response()->json(['contacts' => $contacts]);
    }

    // Inicia uma conversa (ou retorna existente)
    public function startConversation(Request $request)
    {
        $userId = Auth::id();
        $contactId = $request->input('contact_id');
        if ($userId == $contactId) {
            return response()->json(['error' => 'Não é possível conversar consigo mesmo'], 400);
        }
        // Só permite se existe conexão aceita
        $isConnected = Connection::where(function($q) use ($userId, $contactId) {
            $q->where('user_id', $userId)->where('connected_user_id', $contactId);
        })->orWhere(function($q) use ($userId, $contactId) {
            $q->where('user_id', $contactId)->where('connected_user_id', $userId);
        })->where('status', 'aceita')->exists();
        if (!$isConnected) {
            return response()->json(['error' => 'Vocês não estão conectados!'], 403);
        }
        // Busca conversa existente
        $conv = Conversation::where(function($q) use ($userId, $contactId) {
            $q->where('user_one_id', $userId)->where('user_two_id', $contactId);
        })->orWhere(function($q) use ($userId, $contactId) {
            $q->where('user_one_id', $contactId)->where('user_two_id', $userId);
        })->first();
        $isNew = false;
        if (!$conv) {
            $conv = Conversation::create([
                'user_one_id' => $userId,
                'user_two_id' => $contactId
            ]);
            $isNew = true;
        }
        return response()->json([
            'is_new' => $isNew,
            'conversation' => [
                'id' => $conv->id,
                'name' => $conv->user_one_id == $userId ? $conv->userTwo->nome : $conv->userOne->nome,
                'avatar' => $conv->user_one_id == $userId ? $conv->userTwo->url_foto : $conv->userOne->url_foto
            ]
        ]);
    }
} 