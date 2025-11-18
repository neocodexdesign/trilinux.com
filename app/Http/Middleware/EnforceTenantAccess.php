<?php
// app/Http/Middleware/EnforceTenantAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnforceTenantAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Log inicial da requisição
        Log::info('EnforceTenantAccess: Iniciando verificação', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $user = Auth::user();

        if (!$user) {
            Log::warning('EnforceTenantAccess: Usuário não autenticado', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);
            return redirect()->route('login');
        }

        // Log do usuário autenticado
        Log::info('EnforceTenantAccess: Usuário autenticado', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name
        ]);

        // Tenancy v3 expõe o tenant atual via helper tenant()
        $currentTenantId = tenant('id'); // string (ex.: 'neocodexlabs' ou 'uuid')

        if (!$currentTenantId) {
            // Nada de tenant: deixa seguir (provável que esteja no central)
            Log::info('EnforceTenantAccess: Sem tenant definido (central domain)', [
                'user_id' => $user->id,
                'domain' => $request->getHost()
            ]);
            return $next($request);
        }

        // Log do tenant identificado
        Log::info('EnforceTenantAccess: Tenant identificado', [
            'tenant_id' => $currentTenantId,
            'user_id' => $user->id,
            'domain' => $request->getHost()
        ]);

        // Superuser entra em todos
        if ($user->isSuperuser()) {
            Log::info('EnforceTenantAccess: Acesso liberado (Superuser)', [
                'user_id' => $user->id,
                'tenant_id' => $currentTenantId,
                'is_superuser' => true
            ]);
            return $next($request);
        }

        Log::debug('EnforceTenantAccess: Usuário não é superuser, verificando vínculo', [
            'user_id' => $user->id,
            'tenant_id' => $currentTenantId
        ]);

        // Checa vínculo do usuário com o tenant atual
        $canAccess = $user->canAccessTenant($currentTenantId);

        Log::info('EnforceTenantAccess: Resultado da verificação de acesso', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'tenant_id' => $currentTenantId,
            'can_access' => $canAccess,
            'user_tenants' => $user->tenants->pluck('id')->toArray() ?? []
        ]);

        if ($canAccess) {
            Log::info('EnforceTenantAccess: Acesso autorizado', [
                'user_id' => $user->id,
                'tenant_id' => $currentTenantId,
                'url' => $request->fullUrl()
            ]);
            return $next($request);
        }

        // Bloqueia acesso
        Log::error('EnforceTenantAccess: Acesso negado - Usuário sem permissão', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'tenant_id' => $currentTenantId,
            'domain' => $request->getHost(),
            'url' => $request->fullUrl(),
            'user_tenants' => $user->tenants->pluck('id')->toArray() ?? [],
            'ip' => $request->ip()
        ]);

        return redirect('/')
            ->with('error', 'Você não tem permissão para acessar este tenant.');
    }
}