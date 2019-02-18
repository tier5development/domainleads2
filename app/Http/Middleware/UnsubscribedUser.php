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
            if(strlen(trim(Auth::user()->stripe_failed_invoice_id)) > 0) {
                if(in_array(\Request::route()->getName(), config('routeSettings.pendingSubscription'))) {
                    return $next($request);
                } else {
                    return redirect()->route('failedSubscription')->with('fail', 'Your subscription seems to have failed. Please repay your last invoice to continue with your current plan.');        
                }
            }
        }
        return redirect()->route('loginPage')->with('fail', 'Session expired. Please log in again!');
    }
}
