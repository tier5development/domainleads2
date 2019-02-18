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
        if(Auth::check()) {
            /**
             * 1 -> trialing
             * 2 -> active
             * 3 -> past_due
             * 4 -> unpaid
             * 5 -> canceled
             */
            $user = Auth::user();
            if($user->is_subscribed != config('settings.SUBSCRIPTIONS.active') && $user->is_subscribed != config('settings.SUBSCRIPTIONS.trailing')) {
                if(in_array(\Request::route()->getName(), config('routeSettings.subscribedUserGroup'))) {
                    return $next($request);
                } else {
                    return redirect()->route('showMembershipPage')->with('fail', 'Your subscription seems to have failed. Please select a subscription plan to continue.');
                }
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
