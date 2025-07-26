<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommentLike;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    // Listar likes de um comentário
    public function index($comentario_id)
    {
        $likes = CommentLike::where('comentario_id', $comentario_id)->get();
        return response()->json($likes);
    }

    // Curtir/descurtir comentário (toggle)
    public function toggle($comentario_id)
    {
        if (!Auth::check()) {
            abort(403, 'Acesso negado');
        }

        $user_id = Auth::id();
        $like = CommentLike::where('user_id', $user_id)
                          ->where('comentario_id', $comentario_id)
                          ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            CommentLike::create([
                'user_id' => $user_id, 
                'comentario_id' => $comentario_id
            ]);
            $liked = true;
        }

        $count = CommentLike::where('comentario_id', $comentario_id)->count();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'liked' => $liked, 
                'count' => $count
            ]);
        }

        return back();
    }
}
