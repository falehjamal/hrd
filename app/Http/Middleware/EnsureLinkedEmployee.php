<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLinkedEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = $request->user()?->employee;

        if (! $employee || $employee->status !== 'active') {
            abort(403, 'Akun Anda tidak terhubung ke data karyawan aktif.');
        }

        return $next($request);
    }
}
