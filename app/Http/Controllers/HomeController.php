<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Postagem;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $estatisticas = [
            'conexoes' => $user->quantidade_conn ?? 0,
            'projetos' => $user->projects()->count(),
            'contribuicoes' => 0 // TODO: Implementar contagem de contribuições
        ];

        $postagens = Postagem::with('user')->latest()->paginate(10);

        return view('home', [
            'nome' => $user->nome,
            'picture' => $user->picture ?? null,
            'trab_atual' => $user->trab_atual ?? '',
            'estatisticas' => $estatisticas,
            'postagens' => $postagens
        ]);
    }
}
