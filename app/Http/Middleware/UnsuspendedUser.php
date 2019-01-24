<?php

namespace App\Http\Middleware;

use Closure;

class UnsuspendedUser
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
            if(\Auth::user()->suspended == 1) {
                \Auth::logout();
                return redirect()->route('loginPage')->with('fail', 'Your account has been suspended! Please contact with the administrator.');
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
