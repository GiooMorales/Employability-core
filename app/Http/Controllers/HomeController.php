<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('home', [
            'nome' => $user->nome,
            'picture' => $user->picture ?? null,
            'trab_atual' => $user->trab_atual ?? '',
        ]);
    }
}
