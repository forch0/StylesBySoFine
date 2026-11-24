<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->isStaff()) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
