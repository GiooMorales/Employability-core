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
        $userId = Auth::user()->id_usuarios;
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->get();

        return view('mensagens', compact('conversations'));
    }

    // Mostra uma conversa específica
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $userId = Auth::user()->id_usuarios;
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->get();
            
        $messages = $conversation->messages()->orderBy('created_at')->get();

        return view('mensagens', [
            'conversations' => $conversations,
            'selectedConversation' => $conversation,
            'messages' => $messages
        ]);
    }

    // Envia uma mensagem
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('reply', $conversation);

        $request->validate([
            'content' => 'required|string',
        ]);

        $userId = Auth::user()->id_usuarios;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'content' => $request->input('content'),
            'content_type' => 'text',
            'is_read' => false
        ]);

        return response()->json(['message' => $message]);
    }

    // Marca mensagens como lidas
    public function markAsRead($id)
    {
        $userId = Auth::user()->id_usuarios;
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
        $userId = Auth::user()->id_usuarios;
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
        $userId = Auth::user()->id_usuarios;
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