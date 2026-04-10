<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        $userRole = $request->user()->role;

        if ($role === 'admin' && ! in_array($userRole, ['super_admin', 'admin'])) {
            abort(403, 'Admin access required.');
        }

        if ($role === 'finance' && ! in_array($userRole, ['super_admin', 'admin', 'finance'])) {
            abort(403, 'Finance access required.');
        }

        if ($role === 'manager' && ! in_array($userRole, ['super_admin', 'admin', 'finance', 'manager'])) {
            abort(403, 'Manager access required.');
        }

        return $next($request);
    }
}
