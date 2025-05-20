<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Exibe o formulário de registro
    public function showRegisterForm()
    {
        return view('registrar');
    }

    // Realiza o registro de um novo usuário
    public function registrar(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'userpassword' => 'required|min:6',
        ]);

        User::create([
            'nome' => $request->username,
            'email' => $request->email,
            'senha' => Hash::make($request->userpassword),
            'data_criacao' => now()
        ]);

        return redirect()->route('login.page')->with('success', 'Conta criada com sucesso!');
    }
}
