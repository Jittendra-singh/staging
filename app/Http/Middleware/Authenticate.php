<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $route = $request->route();
        $action = $route->getAction();
        
        if($action['middleware'][1]=='guest:front' || $action['middleware'][1]=='auth:front'){
            if(!Auth::guard('front')->check()){
                return route('front-login');
            } else {
                return route('front-dashboard');
            }
        }else if($action['middleware'][1]=='guest:referrers' || $action['middleware'][1]=='auth:referrers'){
            if(!Auth::guard('referrers')->check()){
                return route('ref_login');
            } else {
                return route('ref_dashboard');
            }
        }  else if($action['middleware'][1]=='guest' || $action['middleware'][1]=='auth:web'){
            if(!Auth::guard('web')->check()){
                if(Auth::guard('front')->check()){
                    return route('front-dashboard');
                } else {
                    return route('login');
                }
            } else {
                return route('dashboard');
            }
        } else {
            return route('login');
        }

        
    }
}
