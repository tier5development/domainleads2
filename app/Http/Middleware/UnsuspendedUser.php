<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
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
        //dd(1);
        
        if(Auth::check()) {
            if(Auth::user()->suspended == 1) {
                Auth::logout();
                return redirect()->route('loginPage')->with('fail', 'Your account has been suspended! Please contact with the administrator.');
            }
            else if(Auth::user()->is_subscribed == 0) {
                return redirect()->route('showMembershipPage')->with('fail', 'Your subscription seems to have failed. Please select a subscription plan to continue!');
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
