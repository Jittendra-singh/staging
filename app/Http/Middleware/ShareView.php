<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ShareView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            $current_user = auth()->user();
            $current_parent_menu = Menu::where('menu_href', request()->path())->first();

            View::share([
                'parentMenus' => $current_user->role_id == 1 ? Menu::where('menu_parent', 0)->where('is_active' , 1)->orderBy('order')->get() : $current_user->role->parentMenus,
                'currentParentMenu' => $current_parent_menu,
            ]);
        }

        return $next($request);
    }
}