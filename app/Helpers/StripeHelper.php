<?php
namespace App\Helpers;
use App\StripeDetails;
use Stripe, Plan, Log, Throwable;


class StripeHelper {
    
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @customerDetails is an array given to stripe to retrive the new created customer
     *      -- email is essential from our side
     *      -- other fields according to stripe can be added as well as necessary [eg: description]
     * @adminId is mandatory field
     * @returns stripe response customer in an array format, null if error or not created
     */
    public static function createCustomer($customerDetails, $stripeDetails) {
        try {
            $apiSecretKey = $stripeDetails->private_key;
            if (strlen($apiSecretKey) > 0 && array_key_exists('email', $customerDetails) && strlen(trim($customerDetails['email'])) > 0) {
                \Stripe\Stripe::setApiKey($apiSecretKey);
                $newCustomer = \Stripe\Customer::create($customerDetails);
                if(is_object($newCustomer)) {
                    return $newCustomer;
                }
                return null;
            } else {
                return null;
            }
        } catch(Throwable $e) {
            throw $e;
        }
    }

    /**
     * @stripeCustomerId - required [provided by stripe]
     * @adminId is mandatory field
     * @returns stripe response customer in an array format, null if error or not deleted
     */
    public static function deleteCustomer($adminId, $stripeCustomerId) {
        // try {
        //     $customerToDelete = self::retriveCustomer($adminId, $stripeCustomerId);
        //     if($customerToDelete) {
        //         $customerToDelete = $customerToDelete->delete();
        //         return json_decode(json_encode($customerToDelete, true), true);
        //     }
        //     return null;
        // } catch(\Exception $e) {
        //     \Log::info('Stripe Helper - deleteCustomer : '.$e->getMessage());
        //     return null;
        // } 
    }

    /**
     * @stripeCustomerId - required [provided by stripe]
     * @adminId is mandatory field
     * @returns stripe response customer in an array format, null if error or not retrived
     */
    public static function retriveCustomer($stripeCustomerId, $stripeDetails) {
        try {
            $apiSecretKey = $stripeDetails->private_key;
            if (strlen($apiSecretKey) > 0 && strlen(trim($stripeCustomerId)) > 0) {
                \Stripe\Stripe::setApiKey($apiSecretKey);
                $retrievedCustomer = \Stripe\Customer::retrieve($stripeCustomerId);
                return $retrievedCustomer;
            }
        } catch(Thowable $e) {
            throw $e;
        }
    }

    /**
     * @adminId is mandatory field
     * @stripeCustomerId is a mandatory field
     * $customer details represents a customer array without the id attribute
     *      -- all fields as in stripe to update the customer details
     *      -- example : account_balance, coupon, description, email, metadata, etc..
     * @returns stripe customer list in an array format, null if error or not retrived
     */
    public static function updateCustomer($stripeCustomerId, $customerDetails, $stripeDetails) {
        try {
            $customer = self::retriveCustomer($stripeCustomerId, $stripeDetails);
            if($customer) {
                if(count($customerDetails) > 0) {
                    foreach($customerDetails as $key => $val) {
                        $customer->$key = $val;
                    }
                    Log::info('in updateCustomer :: going to update customer in stripe');
                    $updatedCustomer = $customer->save();
                    return $updatedCustomer;
                } else {
                    Log::info('-- no customer found --');
                    return null;
                }
            }
        } catch(Throwable $e) {
            throw $e;
        }
    }

    /**
     * @attributes : array contains limit - optional and ending_before : last object id (for pagination)
     * @adminId is mandatory field
     * @returns stripe customer list in an array format, null if error or not retrived
     */
    public static function customerList($stripeDetails, $attributes = []) {
        try {
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $customers = \Stripe\Customer::all($attributes);
            $customersArr = json_decode(json_encode($customers, true), true);
            return [
                'customers' => $customers,
                'customersArr' => $customersArr
            ];
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public static function fetchUserWithEmail($stripeDetails, $email) {
        if(strlen(trim($email)) == 0) {
            return null;
        }
        try {
            return self::customerList($stripeDetails, ['email' => $email, 'limit' => 1]);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * params : 
     * keySecond : secret key
     * planArr :
     * amount : amount,
     * interval : day/week/month/year,
     * product : [
     *  "name" => some product name
     * ],
     * currency : 
     * id : plan_id
     * trial_period_days : days
     */

    // \Stripe\Plan::create([
    //     "amount" => 5000,
    //     "interval" => "month",
    //     "product" => [
    //       "name" => "Gold special"
    //     ],
    //     "currency" => "usd",
    //     "id" => "gold-special",
    //     "trial_period_days" => 10
    //   ]);

    public static function createPlan($stripeDetails, $planArr) {
        try {
            $privateKey = $stripeDetails->private_key;
            \Stripe\Stripe::setApiVersion("2018-10-31");
            \Stripe\Stripe::setApiKey($privateKey);
            $plan = \Stripe\Plan::create($planArr);
            $planArr = json_decode(json_encode($plan, true), true);
            if(count($planArr) == 0) {
                return ['status' => false, 'plan' => $plan, 'planArr' => $planArr, 'message' => 'Cannot create stripe plans.'];
            } else {
                return ['status' => true, 'plan' => $plan, 'planArr' => $planArr, 'message' => 'Success.'];
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrive a acharge object
     */
    public static function retriveChargeObject($keySecond, $chargeId) {
        \Stripe\Stripe::setApiKey($keySecond);
        $ch     = \Stripe\Charge::retrieve($chargeId);
        return $ch;
    }

    /**
     * Function to fetch all webhooks in stripe
     */
    public static function retriveAllWebhooks($keySecond) {
        \Stripe\Stripe::setApiVersion("2018-10-31");
        \Stripe\Stripe::setApiKey($keySecond);
        $webhooks = \Stripe\WebhookEndpoint::all(["limit" => 30]);
        return $webhooks;
    }

    public static function retriveWebhook($keySecond, $hookId) {
        \Stripe\Stripe::setApiVersion("2018-10-31");
        \Stripe\Stripe::setApiKey($keySecond);
        $webhook = \Stripe\WebhookEndpoint::retrieve($hookId);
        return $webhook;
    }

    /**
     * Creates a charge webhook to know about runtime stripe information about the user.
     * @param
     * keySecond : stripe secret key for admin
     * @return array of subscription and invoice
     */
    public static function createChargeWebhook($keySecond) {
        try {
            \Stripe\Stripe::setApiVersion("2018-10-31");
            \Stripe\Stripe::setApiKey($keySecond);
            $subscriptionObj = \Stripe\WebhookEndpoint::create([
                "url" =>   'http://fa2c6a6b.ngrok.io/api/stripe/customer-subscription-updated', // route('customerSubscriptionDeleted'), // 'http://fa2c6a6b.ngrok.io/api/stripe/customer-subscription-updated' 
                "enabled_events" => config('settings.WEBHOOKS.SUBSCRIPTION')
            ]);

            $invoiceObj = \Stripe\WebhookEndpoint::create([
                "url" =>   'http://fa2c6a6b.ngrok.io/api/stripe/customer-invoice-payment_failed', // route('customerInvoicePaymentFailed'), // 'http://fa2c6a6b.ngrok.io/api/stripe/customer-invoice-payment_failed',
                "enabled_events" => config('settings.WEBHOOKS.INVOICE')
            ]);

            $customerObj = \Stripe\WebhookEndpoint::create([
                "url" =>   'http://fa2c6a6b.ngrok.io/api/stripe/customer-deleted', // route('customerDeleted'), // 'http://fa2c6a6b.ngrok.io/api/stripe/customer-invoice-payment_failed',
                "enabled_events" => config('settings.WEBHOOKS.CUSTOMER')
            ]);
            return [
                'subscription'  =>  $subscriptionObj,
                'invoice'       =>  $invoiceObj,
                'customer'      =>  $customerObj
            ];
        } catch(Throwable $e) {
            throw $e;
        }
    }


    /**
     * charge a failed invoice again
     * @param
     * stripeDetails : StripeDetails table instance
     * invoiceId : stripe invoice id
     * @return invoice instance
     */
    public static function chargeFailedInvoice($stripeDetails, $invoiceId) {
        try {
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $invoice = \Stripe\Invoice::retrieve($invoiceId);
            $invoice->pay();
            return $invoice;
        } catch(Throwable $e) {
            throw $e;
        }
    }

    /**
     * delete webhook
     * @param
     * keySecond : secret key
     * hookId : hookId 
     * 
     * @return deleted endpoint instance
     */
    public static function deleteWebhook($keySecond, $hookId) {
        \Stripe\Stripe::setApiVersion("2018-10-31");
        \Stripe\Stripe::setApiKey($keySecond);
        $endpoint = \Stripe\WebhookEndpoint::retrieve($hookId);
        $endpoint->delete();
        return $endpoint;
    }

    /**
     * change subscription for a customer
     * @params
     * admin -> admin instance
     * stripeCustomer -> stripeCustomer instance
     * subscriptionArr -> array of subscriptions
     *  -> plan (a valid plan id)
     *  -> optional trial_period_days (number of trial period days for this plan overriding)
     */
    public static function changeSubscription($stripeDetails, $subscriptionId, $planId) {
        try {
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
            $obj = \Stripe\Subscription::update($subscriptionId, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $planId,
                    ],
                ],
            ]);
            return $obj;
        } catch(Throwable $e) {
            throw $e;
        }
    }

    /**
     * charge subscription for a customer
     * @param
     * stripeDetails : StripeDetaiil table instance
     * customerId : customer id from stripe
     * planId : plan ID from stripe
     * trialPeriod : trial period (optional)
     * @return
     * subscription object from stripe
     */
    public static function chargeSubscription($stripeDetails, $customerId, $planId, $trialPeriod = null) {
        try {
            $failedFlag = false;
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $array = [
                'customer' => $customerId,
                'items' => [[
                    'plan' => $planId
                ]]
            ];

            if($trialPeriod != null) {
                $array['trial_period_days'] = $trialPeriod;
            }
            Log::info('in chargeSubscription : ', $array);
            $subscription = \Stripe\Subscription::create($array);
            return $subscription;
        } catch(Throwable $e) {
            throw $e;
        }
    }

    /**
     * cancel subscription for a customer
     * @param
     * stripeDetails : StripeDetaiil table instance
     * user : user table instance
     * @return
     * subscription canceled instance
     */
    public static function cancelSubscription($stripeDetails, $user) {
        try {
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
            return $subscription->cancel();
        } catch(Throwable $e) {
            throw $e;
        }
    }
}
?>