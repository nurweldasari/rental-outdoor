<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {

        // cek login
        if (!Auth::check()) {
            abort(401);
        }

        // cek role/status user
        if (!in_array(Auth::user()->status, $roles)) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}