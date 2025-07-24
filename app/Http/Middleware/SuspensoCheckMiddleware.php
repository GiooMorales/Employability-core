<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SuspensoCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->suspenso_ate && $user->suspenso_ate > now()) {
            $ate = Carbon::parse($user->suspenso_ate)->format('d/m/Y H:i');
            $motivo = $user->motivo;
            return response()->view('suspenso', [
                'ate' => $ate,
                'motivo' => $motivo,
            ]);
        }
        return $next($request);
    }
} 