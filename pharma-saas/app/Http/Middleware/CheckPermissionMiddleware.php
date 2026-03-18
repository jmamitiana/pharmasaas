<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasPermissionTo($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized - Missing permission: ' . $permission], 403);
            }
            
            abort(403, 'Unauthorized - Missing permission: ' . $permission);
        }

        return $next($request);
    }
}
