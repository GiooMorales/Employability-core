<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanidoCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Verificar se o usuário está logado e está banido
        if ($user && $user->banido) {
            $motivo = $user->motivo;
            
            return response()->view('banido', [
                'motivo' => $motivo,
            ]);
        }
        
        return $next($request);
    }
}
