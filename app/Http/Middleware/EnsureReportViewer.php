<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureReportViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->canViewReports(), 403, 'Anda tidak memiliki akses ke laporan.');

        return $next($request);
    }
}
