<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Verifica se o usuário está logado e se a coluna "admin" é true
        if (Auth::check() && Auth::user()->admin) {
            return $next($request);
        }

        // Se não for admin, volta para o dashboard com mensagem de erro
        return redirect()->route('dashboard')->with('error', 'Acesso restrito para administradores.');
    }
}
