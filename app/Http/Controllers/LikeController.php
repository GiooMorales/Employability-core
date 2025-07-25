<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Postagem;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Listar likes de uma postagem
    public function index($postagem_id)
    {
        $likes = Like::where('postagem_id', $postagem_id)->get();
        return response()->json($likes);
    }

    // Curtir/descurtir postagem (toggle)
    public function toggle($postagem_id)
    {
        if (!Auth::check()) {
            abort(403, 'Acesso negado');
        }
        $user_id = Auth::id();
        $like = Like::where('user_id', $user_id)->where('postagem_id', $postagem_id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $user_id, 'postagem_id' => $postagem_id]);
            $liked = true;
        }
        $count = Like::where('postagem_id', $postagem_id)->count();
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['liked' => $liked, 'count' => $count]);
        }
        return back();
    }
}
