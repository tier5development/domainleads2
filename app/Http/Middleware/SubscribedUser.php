<?php

namespace App\Http\Middleware;
use \Illuminate\Http\Request;
use Closure;
use Auth;
class SubscribedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //dd(1);
        
        if(Auth::check()) {
            
            if(Auth::user()->is_subscribed == 0) {
                if(\Request::route()->getName() != 'showMembershipPage') {
                    return redirect()->route('showMembershipPage')->with('fail', 'Your subscription seems to have failed. Please select a subscription plan to continue.');
                } else {
                    return $next($request);        
                }
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
