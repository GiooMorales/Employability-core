<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        $usuarios = \App\Models\Usuario::all();
        return view('admin.users', compact('usuarios'));
    }

    public function promoteToAdmin($id)
    {
        $usuario = \App\Models\Usuario::findOrFail($id);
        $usuario->is_admin = true;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário promovido a admin com sucesso!');
    }

    public function demoteFromAdmin($id)
    {
        $usuario = \App\Models\Usuario::findOrFail($id);
        $usuario->is_admin = false;
        $usuario->save();
        return redirect()->route('admin.users')->with('success', 'Usuário rebaixado de admin com sucesso!');
    }
} 