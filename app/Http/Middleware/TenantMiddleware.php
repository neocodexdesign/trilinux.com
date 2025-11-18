<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost(); // Ex: neocodexlabs.online ou app.com
        $firstSegment = $request->segment(1); // Ex: "acme" em app.com/acme/admin

        $tenant = null;

        // 1️⃣ Tenta resolver por domínio dedicado
        $tenant = Tenant::where('domain', $host)->first();

        // 2️⃣ Se não encontrou, tenta resolver por slug no path
        if (!$tenant && $firstSegment) {
            $tenant = Tenant::where('slug', $firstSegment)->first();
        }

        // 3️⃣ Se não achou, então estamos no painel global
        if (!$tenant) {
            // Não seta tenancy, apenas segue
            return $next($request);
        }

        // 🔑 Agora seta o tenant no container da aplicação
        app()->instance('current_tenant', $tenant);

        // Compartilha com as views
        View::share('currentTenant', $tenant);

        // Adiciona Global Scope em todos os models relevantes
        $this->addTenantScopes($tenant->id);

        return $next($request);
    }

    private function addTenantScopes(int $tenantId): void
    {
        $modelsWithTenantScope = [
            \App\Models\Project::class,
            \App\Models\Stage::class,
            \App\Models\Task::class,
            \App\Models\Review::class,
            \App\Models\Template::class,
        ];

        foreach ($modelsWithTenantScope as $model) {
            $model::addGlobalScope('tenant', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }

        // Escopo para Activity Log
        \Spatie\Activitylog\Models\Activity::addGlobalScope('tenant', function ($builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });

        // Escopo para Audits (se instalado)
        if (class_exists(\OwenIt\Auditing\Models\Audit::class)) {
            \OwenIt\Auditing\Models\Audit::addGlobalScope('tenant', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }
    }
}