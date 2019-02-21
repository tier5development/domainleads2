<?php 

namespace App\Http\Controllers\Stripe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator, Log, Throwable, DB;
use App\Helpers\UserHelper;


class StripeWebhooksController extends Controller 
{
    public function customerSubscriptionUpdated(Request $request) {
        try {
            DB::beginTransaction();
            $data               =   json_decode(json_encode($request->all(), true), true);
            $customerId         =   $data['data']['object']['customer'];
            $subscriptionId     =   $data['data']['object']['id'];
            $status             =   $data['data']['object']['status'];
            // $user               =   User::where('stripe_customer_id', $customerId)->first();
            $user               =   User::find(1241);
            Log::info('customerSubscriptionUpdated : user id '.$user->id.'cust id : '.$customerId.' subscriptionId : '.$subscriptionId.' status : '.$status);
            
            if($user) {
                if($status == 'active' || $status == 'trialing') {
                    $user->stripe_failed_invoice_id     =   null;
                    $user->stripe_failed_invoice_obj    =   null;
                }
                $user->stripe_subscription_id     =   $subscriptionId;
                $user->stripe_subscription_obj    =   json_encode($data, true);
                $user->is_subscribed              =   config('settings.SUBSCRIPTIONS.'.$status); 
                $user->save();
                Log::info('customerInvoicePaymentFailed : user : '.$user->id.' status : '.$status);
            }
            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            Log::info('subscription request ERROR :::: '.$e->getMessage());
        }
    }

    public function customerInvoicePaymentFailed(Request $request) {
        try {
            DB::beginTransaction();
            $data           =   json_decode(json_encode($request->all(), true), true);
            $customerId     =   $data['data']['object']['customer'];
            $invoiceId      =   $data['data']['object']['id'];
            $status         =   'unpaid';
            $user           =   User::where('stripe_customer_id', $customerId)->first();
            // $user           =   User::find(1241);
            if($user) {
                $user->stripe_failed_invoice_id     =   $invoiceId;
                $user->stripe_failed_invoice_obj    =   json_encode($data, true);
                $user->is_subscribed                =   config('settings.SUBSCRIPTIONS.unpaid'); 
                $user->save();
                Log::info('customerInvoicePaymentFailed :  user : '.$user->id.' customer id : '.$customerId.' invoice id '.$invoiceId.' status : '.$status);
            }
            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            Log::info('invoice request ERROR :::: '.$e->getMessage());
        }
    }
}
?>