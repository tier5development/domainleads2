<?php

namespace App\Http\Middleware;

use Closure;

class AdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(\Auth::check()) {
            if(!\Auth::user()->user_type == config('settings.ADMIN-NUM')) {
                \Auth::logout();
                return redirect()->route('loginPage')->with('fail', 'Suspicious activity noticed from your account. You need to log in again!');
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
