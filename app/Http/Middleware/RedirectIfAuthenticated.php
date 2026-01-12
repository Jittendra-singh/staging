<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        $route = $request->route();
        /* $action = $route->getAction();
        //echo $action;die();
        echo $action['middleware'][1];die(); */
        //print_r($guards);die();
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if($guard=='customers'){
                    return redirect(RouteServiceProvider::HOME_CUSTOMER);
                } else if($guard=='referrers'){
                    return redirect(RouteServiceProvider::HOME_REFERRER);
                }
                return redirect(RouteServiceProvider::HOME);
            } else {
                if($guard=='referrers' && Auth::guard('customers')->check()){
                    return redirect(RouteServiceProvider::HOME_CUSTOMER);
                }
                if($guard=='customers' && Auth::guard('referrers')->check()){
                    return redirect(RouteServiceProvider::HOME_REFERRER);
                }
            }
        }

        return $next($request);
    }
}
