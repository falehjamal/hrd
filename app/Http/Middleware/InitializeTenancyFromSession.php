<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('tenant_id') && ! tenancy()->initialized) {
            $tenant = Tenant::find($request->session()->get('tenant_id'));

            if ($tenant) {
                tenancy()->initialize($tenant);
            }
        }

        return $next($request);
    }
}
