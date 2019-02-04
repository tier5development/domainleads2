<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class UnsubscribedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(Auth::check()) {
            $user = Auth::user();
            // subscription is canceled
            if($user->is_subscribed == 5) {
                Auth::logout();
                return redirect()->route('loginPage')->with('fail', 'Your subscription is canceled!');        
            }

            if(strlen(trim($user->stripe_failed_invoice_id)) > 0 && $user->is_subscribed == 4) {
                if(in_array(\Request::route()->getName(), config('routeSettings.pendingSubscription'))) {
                    return $next($request);
                } else {
                    return redirect()->route('failedSubscription')->with('fail', 'Your subscription seems to have failed. Please repay your last invoice to continue with your current plan.');        
                }
            } else {
                Auth::logout();
                // return redirect()->route('loginPage')->with('fail', 'Your subscription is canceled!');
            }
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
