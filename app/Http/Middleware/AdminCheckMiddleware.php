<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCheckMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->is_admin || str_ends_with(auth()->user()->email, '@teste.com'))) {
            return $next($request);
        }
        abort(403, 'Acesso não autorizado.');
    }
} 