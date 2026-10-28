<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário autenticado é admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
