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
            if($user->is_subscribed == config('settings.SUBSCRIPTIONS.past_due') && $user->is_subscribed == config('settings.SUBSCRIPTIONS.unpaid')) {
                if(strlen(trim($user->stripe_failed_invoice_id)) > 0) {
                    if(\Request::route()->getName() == 'failedSubscription' || \Request::route()->getName() == 'failedSubscriptionPost') {
                        return $next($request);
                    }
                    return redirect()->route('failedSubscription')->with('fail', 'Please clear your pending payments to continue.');;
                }
            } else if($user->is_subscribed == config('settings.SUBSCRIPTIONS.active') && $user->is_subscribed == config('settings.SUBSCRIPTIONS.trailing')) {
                if(\Request::route()->getName() == 'failedSubscription' || \Request::route()->getName() == 'failedSubscriptionPost') {
                    return redirect()->route('profile');
                } else {
                    return $next($request);
                }
            } else if($user->is_subscribed == config('settings.SUBSCRIPTIONS.canceled')) {
                // work+sukojumi@tier5.us
                if(\Request::route()->getName() == 'showMembershipPage' || \Request::route()->getName() == 'updateCardDetailsAndSubscribe' || \Request::route()->getName() == 'upgradeOrDowngradePlan') {
                    return $next($request);
                } else {
                    return redirect()->route('showMembershipPage')->with('fail', 'Oops! Your subscription seems to have been canceled. Please choose a subscription to continue.');
                }
            }
            return $next($request);
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
