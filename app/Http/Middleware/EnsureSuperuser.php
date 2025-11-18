<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperuser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não está autenticado, redireciona para login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        // Se não é superuser, faz logout e redireciona para login
        if (!$user->isSuperuser()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->withErrors([
                'email' => 'Acesso negado. Apenas superusers podem acessar o domínio central.',
            ]);
        }

        return $next($request);
    }
}
