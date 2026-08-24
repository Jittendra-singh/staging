<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceSuperAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if (!$user || (int)$user->role_id !== 1) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
