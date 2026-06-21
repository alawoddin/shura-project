<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->canManageAccess()) {
            abort(403, 'Only Admin or Super Admin can manage roles and permissions.');
        }

        return $next($request);
    }
}
