<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RepositorioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $nickname = $user->github_username;
        $repos = [];
        if ($nickname) {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => 'token ' . $user->github_token,
            ])->get("https://api.github.com/users/{$nickname}/repos");
            if ($response->ok()) {
                $repos = $response->json();
            }
        }
        return view('repositorios', compact('repos'));
    }
} 