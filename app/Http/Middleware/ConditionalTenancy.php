<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class ConditionalTenancy
{
    public function handle(Request $request, Closure $next)
    {
        $centralDomains = config('tenancy.central_domains', ['trilinux.com']);

        if (in_array($request->getHost(), $centralDomains)) {
            return $next($request);
        }

        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }
}