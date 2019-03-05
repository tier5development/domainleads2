<?php 

namespace App\Http\Controllers\Stripe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator, Log, Throwable, DB;
use App\Helpers\UserHelper;
use App\Traits\AffiliatesTrait;

class StripeWebhooksController extends Controller 
{
    use AffiliatesTrait;

    public function customerSubscriptionUpdated(Request $request) {
        sleep(8);
        try {
            DB::beginTransaction();
            $data               =   json_decode(json_encode($request->all(), true), true);
            $customerId         =   trim($data['data']['object']['customer']);
            $subscriptionId     =   $data['data']['object']['id'];
            $status             =   $data['data']['object']['status'];
            $user               =   User::where('stripe_customer_id', $customerId)->first();
            $type               =   $data['type'];
            
            if($user != null) {
                Log::info('subscription hook : user id '.$user->id.'cust id : '.$customerId.' subscriptionId : '.$subscriptionId.' status : '.$status.' type : '.$type);
                if($status == 'active' || $status == 'trialing') {
                    $user->stripe_failed_invoice_id     =   null;
                    $user->stripe_failed_invoice_obj    =   null;
                }
                $user->stripe_subscription_id     =   $subscriptionId;
                $user->stripe_subscription_obj    =   json_encode($data, true);
                $user->is_subscribed              =   config('settings.SUBSCRIPTIONS.'.$status); 
                $user->save();
                $trial = $status == 'trialing' ? 1 : 0;
                if($status == 'trialing' || $status == 'active') {
                    if(strlen($user->sale_id) > 0) {
                        $this->registerSale('created', $user, $trial);
                    } else {
                        $this->registerSale('updated', $user, $trial);
                    }
                } else if($status == 'unpaid' || $status == 'canceled' || $status == 'past_due') {
                    $this->registerSale('inactive', $user, $trial);
                }
                Log::info('subscription hook : user : '.$user->id.' status : '.$status);

                if($status == 'canceled') {
                    $user->stripe_plan_id = null;
                    $user->stripe_subscription_id = null;
                    $user->stripe_subscription_obj = null;
                    $user->user_type = null;
                    $user->stripe_failed_invoice_id = null;
                    $user->stripe_failed_invoice_obj = null;
                    $user->card_updated = '0';
                    $user->is_subscribed = '5';
                    $user->save();
                }
            } else {
                Log::info('subscription hook : no user cust id : '.$customerId.' subscriptionId : '.$subscriptionId.' status : '.$status.' type : '.$type);
            }
            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            Log::info('subscription request ERROR :::: '.$e->getMessage().' line : '.$e->getLine());
        }
    }

    public function customerInvoicePaymentFailed(Request $request) {
        sleep(8);
        try {
            DB::beginTransaction();
            $data           =   json_decode(json_encode($request->all(), true), true);
            $customerId     =   trim($data['data']['object']['customer']);
            $invoiceId      =   $data['data']['object']['id'];
            $type           =   $data['type'];
            Log::info('invoice hook : customer id : '.$customerId.' invoice id '.$invoiceId.' type : '.$type);
            if($type == 'invoice.payment_failed') {
                $status         =   'unpaid';
                $user           =   User::where('stripe_customer_id', $customerId)->first();
                if($user) {
                    Log::info('user found in invoice hook');
                    $user->stripe_failed_invoice_id     =   $invoiceId;
                    $user->stripe_failed_invoice_obj    =   json_encode($data, true);
                    $user->is_subscribed                =   config('settings.SUBSCRIPTIONS.'.$status); 
                    $user->save();
                    $this->registerSale('inactive', $user);
                    Log::info('invoice hook :  user : '.$user->id.' customer id : '.$customerId.' invoice id '.$invoiceId.' status : '.$status);
                }
            } else if($type == 'invoice.payment_succeeded') {
                $status         =   'active';
                $user           =   User::where('stripe_customer_id', $customerId)->first();
                if($user) {
                    Log::info('user found in invoice hook');
                    $user->stripe_failed_invoice_id     =   null;
                    $user->stripe_failed_invoice_obj    =   null;
                    $user->is_subscribed                =   config('settings.SUBSCRIPTIONS.'.$status); 
                    $user->save();
                    $this->registerSale('active', $user);
                    Log::info('invoice hook :  user : '.$user->id.' customer id : '.$customerId.' invoice id '.$invoiceId.' status : '.$status);
                }
            }
            DB::commit();
        } catch(Throwable $e) {
            DB::rollback();
            Log::info('invoice request ERROR :::: '.$e->getMessage());
        }
    }

    public function customerDeleted(Request $request) {
        try {
            DB::beginTransaction();
            $data           =   json_decode(json_encode($request->all(), true), true);
            $customerId     =   $data['data']['object']['id'];
            $user = User::where('stripe_customer_id', $customerId)->first();
            if($user) {
                $user->delete();
                Log::info('User deleted successfully');    
            }
        } catch(Throwable $e) {
            DB::rollback();
            Log::info(' ERROR : '.$e->getMessage().' line : '.$e->getLine());
        }
    }
}
?>