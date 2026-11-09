<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Permite acesso somente a administradores (ou super admin). */
class AdminApenas
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        return $next($request);
    }
}
