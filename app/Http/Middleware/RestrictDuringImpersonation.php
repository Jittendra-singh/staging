<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictDuringImpersonation
{
    /**
     * These route names are blocked while impersonating
     */
    protected array $blockedRoutes = [
        'save.front-profile',
        'save.front.organisation.details',
        'front-stripe-connect',
        'front-disconnect-stripe-connect',
        'profile.update',
        'password.update',
        'email.update',
        'billing.*',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (app('impersonate')->isImpersonating()) {
            foreach ($this->blockedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'This action is not available during impersonation.'
                        ], 403);
                    }

                    return redirect()->back()
                        ->with('error', 'This action is not permitted while impersonating a user.');
                }
            }
        }

        return $next($request);
    }
}
