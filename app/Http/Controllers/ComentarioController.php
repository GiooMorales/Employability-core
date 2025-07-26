<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Postagem;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    // Listar comentários de uma postagem
    public function index($postagem_id)
    {
        $comentarios = Comentario::with('user')->where('postagem_id', $postagem_id)->latest()->get();
        return response()->json($comentarios);
    }

    // Adicionar comentário (autenticado)
    public function store(Request $request, $postagem_id)
    {
        if (!Auth::check()) {
            abort(403, 'Acesso negado');
        }
        $data = $request->validate([
            'conteudo' => 'required|string',
        ]);
        $data['user_id'] = Auth::id();
        $data['postagem_id'] = $postagem_id;
        $comentario = Comentario::create($data);
        
        // Carregar o usuário e likes para retornar dados completos
        $comentario->load('user', 'likes');
        
        return response()->json([
            'id' => $comentario->id,
            'conteudo' => $comentario->conteudo,
            'user' => $comentario->user,
            'created_at' => $comentario->created_at,
            'likes' => $comentario->likes
        ], 201);
    }
}
