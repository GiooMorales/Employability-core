<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postagem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Connection;

class PostagemController extends Controller
{
    // Listar todas as postagens (público)
    public function index()
    {
        $postagens = Postagem::with('user')->latest()->paginate(10);
        return view('postagens.index', compact('postagens'));
    }

    // Visualizar uma postagem específica (público)
    public function show($id)
    {
        $postagem = Postagem::with(['user', 'comentarios.user', 'comentarios.likes', 'likes'])->findOrFail($id);
        return view('postagens.show', compact('postagem'));
    }

    // Formulário de criação (apenas admin ou usuários com email @teste.com)
    public function create()
    {
        $user = Auth::user();
        
        // Verificar se é admin ou tem email @teste.com
        if (!$user || (!$user->is_admin && !str_ends_with($user->email, '@teste.com'))) {
            abort(403, 'Acesso negado. Apenas administradores ou usuários com email @teste.com podem criar postagens.');
        }
        return view('postagens.create');
    }

    // Salvar nova postagem (apenas admin ou usuários com email @teste.com)
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Verificar se é admin ou tem email @teste.com
        if (!$user || (!$user->is_admin && !str_ends_with($user->email, '@teste.com'))) {
            abort(403, 'Acesso negado. Apenas administradores ou usuários com email @teste.com podem criar postagens.');
        }
        
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'imagem' => 'nullable|image|max:2048',
        ]);
        $data['user_id'] = Auth::id();
        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('postagens', 'public');
        }
        Postagem::create($data);
        return redirect()->route('postagens.index')->with('success', 'Postagem criada com sucesso!');
    }

    // Formulário de edição (apenas admin)
    public function edit($id)
    {
        $postagem = Postagem::findOrFail($id);
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Acesso negado. Apenas administradores podem editar postagens.');
        }
        return view('postagens.edit', compact('postagem'));
    }

    // Atualizar postagem (apenas admin)
    public function update(Request $request, $id)
    {
        $postagem = Postagem::findOrFail($id);
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Acesso negado. Apenas administradores podem editar postagens.');
        }
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'imagem' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('postagens', 'public');
        }
        $postagem->update($data);
        return redirect()->route('postagens.index')->with('success', 'Postagem atualizada com sucesso!');
    }

    // Excluir postagem (apenas admin)
    public function destroy($id)
    {
        $postagem = Postagem::findOrFail($id);
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Acesso negado. Apenas administradores podem excluir postagens.');
        }
        $postagem->delete();
        return redirect()->route('postagens.index')->with('success', 'Postagem excluída com sucesso!');
    }

    // Compartilhar postagem no chat com uma conexão
    public function share(Request $request, $id)
    {
        $request->validate([
            'connection_user_id' => 'required|integer|exists:usuarios,id_usuarios',
        ]);
        $postagem = Postagem::findOrFail($id);
        $user = Auth::user();
        $destinatarioId = $request->input('connection_user_id');

        // Buscar ou criar conversa entre os dois usuários
        $conversation = Conversation::where(function($q) use ($user, $destinatarioId) {
            $q->where('user_one_id', $user->id_usuarios)
              ->where('user_two_id', $destinatarioId);
        })->orWhere(function($q) use ($user, $destinatarioId) {
            $q->where('user_one_id', $destinatarioId)
              ->where('user_two_id', $user->id_usuarios);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $user->id_usuarios,
                'user_two_id' => $destinatarioId,
            ]);
        }

        // Enviar mensagem especial de compartilhamento de postagem
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id_usuarios,
            'content' => $postagem->id, // Apenas o ID da postagem
            'content_type' => 'post_share',
            'is_read' => false,
        ]);

        // Opcional: disparar evento para websocket
        event(new \App\Events\MessageSent($message));

        // Retornar JSON se for requisição AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Postagem compartilhada no chat!']);
        }

        return back()->with('success', 'Postagem compartilhada no chat!');
    }
}
