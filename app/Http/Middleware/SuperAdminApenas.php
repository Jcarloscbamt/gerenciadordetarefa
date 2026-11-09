<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Permite acesso somente ao super administrador. */
class SuperAdminApenas
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isSuperAdmin()) {
            abort(403, 'Acesso restrito ao super administrador.');
        }

        return $next($request);
    }
}
