<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()?->hasRole(...$roles)) {
            abort(403, 'Forbidden'); // prijavljen, ali nema ulogu (401 Unauthorized je za neprijavljene)
        }

        return $next($request);
    }
}
