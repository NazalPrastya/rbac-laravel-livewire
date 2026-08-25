<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureMenuPermission
{
    public function handle(Request $request, Closure $next, string $permissionKey, string $ability = 'read'): Response
    {
        abort_unless(
            $request->user()?->hasMenuPermission($permissionKey, $ability),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
