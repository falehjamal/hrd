<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('tenant_id')) {
            return $next($request);
        }

        $tenant = Tenant::query()->find($request->session()->get('tenant_id'));

        if (! $tenant || $tenant->isActive()) {
            return $next($request);
        }

        Auth::guard('web')->logout();

        if (tenancy()->initialized) {
            tenancy()->end();
        }

        $request->session()->forget('tenant_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors(['login' => 'Akun perusahaan ini sedang dinonaktifkan. Hubungi administrator platform.']);
    }
}
