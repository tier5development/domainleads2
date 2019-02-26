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
            $user = Auth::user();
            if($user->suspended == 1) {
                Auth::logout();
                return redirect()->route('loginPage')->with('fail', 'Your account has been suspended! Please contact with the administrator.');
            }
            else if($user->is_subscribed == 0) {
                return redirect()->route('showMembershipPage')->with('fail', 'Your subscription seems to have failed. Please select a subscription plan to continue!');
            }
            if(in_array(\Request::route()->getName(), config('routeSettings.cancelMembership'))) {
                if($user->allowedToCancelMembership()) {
                    return $next($request);        
                } else {
                    return redirect()->route('membership')
                    ->with('fail', 'You belong to an affiliate programme so cannot cancel your membership until you upgrade to next level plan. In case you are already in the highest plan please talk to your service provider.');
                }
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
