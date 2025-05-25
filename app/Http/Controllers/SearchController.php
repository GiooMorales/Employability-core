<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::where('nome', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('profissao', 'like', "%{$query}%")
            ->orWhere('cidade', 'like', "%{$query}%")
            ->orWhere('estado', 'like', "%{$query}%")
            ->orWhere('pais', 'like', "%{$query}%")
            ->get();

        return response()->json([
            'users' => $users
        ]);
    }
} 