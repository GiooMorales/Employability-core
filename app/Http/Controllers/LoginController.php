<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    // Exibe o formulário de login
    public function showLoginForm()
    {
        return view('login');
    }

    // Realiza o login do usuário
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        // Buscar o usuário pelo email
        $user = User::where('email', $credentials['email'])->first();

        // Verificar se o usuário existe e se a senha está correta
        if ($user && Hash::check($credentials['senha'], $user->senha)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('home');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

