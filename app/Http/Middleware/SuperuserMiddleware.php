<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperuserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth()->user();

        // Se não pode acessar o Filament, redireciona para dashboard apropriado
        if (!$user->canAccessFilament()) {
            return redirect($user->getDefaultDashboard())
                ->with('error', 'Você não tem permissão para acessar esta área.');
        }

        // Se está acessando painel admin
        if ($request->is('admin*')) {
            // Superusers podem acessar - continuar
            if ($user->isSuperuser()) {
                return $next($request);
            }

            // Admins/managers de tenant devem ir para seu painel
            if ($user->hasRole(['admin', 'manager'])) {
                return redirect('/tenant')
                    ->with('info', 'Redirecionado para o painel do seu tenant.');
            }

            // Outros usuários não podem acessar
            return redirect($user->getDefaultDashboard())
                ->with('error', 'Apenas superusuários podem acessar o painel administrativo global.');
        }

        return $next($request);
    }
}