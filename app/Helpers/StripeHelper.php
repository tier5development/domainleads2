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
     * function to create charge
     * @params
     * admin -> admin object instance
     * paramArr -> array of parameters
     *          -> customer from stripe
     *          -> currency
     *          -> amount
     *          -> source
     * type -> ach or null for normal
     */
    public static function createCharge($admin, $paramArr, $type = null) {
        // if($type == 'ach' && !isset($paramArr['source'])) {
        //     return ['status' => false, 'charge' => null, 'message' => 'A valid bank source must contain in ach payments!'];
        // }
        // $paymentKeys = PaymentKeys::where('admin_id', $admin->id)->select('key_second')->first();
        // if($paymentKeys && strlen(trim($paymentKeys->key_second)) > 0) {
        //     \Stripe\Stripe::setApiKey($paymentKeys->key_second);
        //     \Stripe\Stripe::setApiVersion("2018-10-31");
        //     Log::info(' from Stripe Helper : createCharge : paramArr : ', $paramArr);
        //     $charge = \Stripe\Charge::create($paramArr);
        //     $chargeArray = json_decode(json_encode($charge, true), true);
        //     if($chargeArray['status'] == 'succeeded') {
        //         return ['status' => true, 'charge' => $charge, 'chargeArray' => $chargeArray, 'message' => 'Success'];
        //     }
        //     return ['status' => false, 'charge' => $charge, 'chargeArray' => $chargeArray, 'message' => $charge['status']];
        // }
    }

    /**
     * This function is more advancced and and uses only 1 api call to stripe
     * to create a subscription with multiple plans
     * 
     * Previously we were making n number of api requests for n number of plans to subscribe
     * But here with this approach we make 1 api request for n number of plans to subscribe
     */
    public static function chargeSingleSubscriptionWithPlans($keySecond, $subscriptionArray) {
        // try {
        //     \Stripe\Stripe::setApiKey($keySecond);
        //     $subscription = \Stripe\Subscription::create($subscriptionArray);
        //     $subscriptionResponseArr = json_decode(json_encode($subscription, true), true);
        //     $status = false;
        //     if($subscriptionResponseArr['status'] == 'active' || $subscriptionResponseArr['status'] == 'trialing') {
        //         $status = true;
        //     }
        //     return ['status' => $status,
        //     'message' => 'Success', 
        //     'subscription' => $subscription, 
        //     'subscriptionStatusArray' => $subscriptionResponseArr];
        // } catch (\Exception $e) {
        //     throw $e;
        //     // return ['status' => false, 
        //     // 'message' => 'Error : '.$e->getMessage().' Line : '.$e->getLine(), 
        //     // 'subscription' => isset($subscription) ? $subscription : null, 
        //     // 'subscriptionStatusArray' => isset($subscriptionResponseArr) ? $subscriptionResponseArr : []];
        // }
    }

    public static function fetchSubscriptionById($keySecond, $subscriptionId) {
        // if(strlen($keySecond) <= 0 || strlen($subscriptionId) <=0) {
        //     return ['status' => false, 'subscription' => null, 'message' => 'Incorrect parameters for fetchSubscriptionById.'];
        // } else {
        //     try {
        //         \Stripe\Stripe::setApiKey($keySecond);
        //         $subscription = \Stripe\Plan::retrieve($subscriptionId);
        //         $subscriptionArr = json_decode(json_encode($subscription, true), true);
        //         // $subscription = json_decode($subscription, true);
        //         if(count($subscriptionArr) > 0) {
        //             return ['status' => true, 'subscription' => $subscription, 'subscriptionArr' => $subscriptionArr, 'message' => 'No such plan.'];
        //         } else {
        //             return ['status' => false, 'subscription' => $subscription, 'subscriptionArr' => $subscriptionArr, 'message' => 'Success'];
        //         }
        //     } catch(\Exception $e) {
        //         return ['status' => false, 'subscription' => null, 'subscriptionArr' => [], 'message' => 'Error : '.$e->getMessage().' Line : '.$e->getLine()];
        //     }
        // }
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

    public static function createChargeWebhook($keySecond) {
        try {
            \Stripe\Stripe::setApiVersion("2018-10-31");
            \Stripe\Stripe::setApiKey($keySecond);
            $subscriptionObj = \Stripe\WebhookEndpoint::create([
                "url" => route('customerSubscriptionDeleted'),
                "enabled_events" => config('settings.WEBHOOKS.SUBSCRIPTION')
            ]);

            $invoiceObj = \Stripe\WebhookEndpoint::create([
                "url" => route('customerInvoicePaymentFailed'),
                "enabled_events" => config('settings.WEBHOOKS.INVOICE')
            ]);
            return [
                'subscription'  =>  $subscriptionObj,
                'invoice'       =>  $invoiceObj
            ];    
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public static function deleteWebhook($keySecond, $hookId) {
        \Stripe\Stripe::setApiVersion("2018-10-31");
        \Stripe\Stripe::setApiKey($keySecond);
        $endpoint = \Stripe\WebhookEndpoint::retrieve($hookId);
        $endpoint->delete();
        return $endpoint;
    }

    // param : array
    // id : hookId
    // enabled_events: array of events (like charge.success, charge.pending, charge.failed etc)
    // url : url endpoint
    public static function updateWebhook($keySecond, $param) {
        // \Stripe\Stripe::setApiKey($keySecond);
        // $endpoint = \Stripe\WebhookEndpoint::retrieve($param['id']);
        // $endpoint->enabled_events = $param['enabled_events'];
        // $endpoint->url = config('settings.APP_HOST')."/api/v1/ach/charge-live-status";
        // $endpoint->save();

        // return $endpoint;
    }

    public static function createProduct($keySecond, $name) {
        // try {
        //     \Stripe\Stripe::setApiKey($keySecond);
        //     $product = \Stripe\Product::create([
        //         "name" => $name,
        //         "type" => "service",
        //     ]);
        //     return $product;
        // } catch(Throwable $e) {
        //     throw $e;
        // }
    }

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
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * charge subscription for a customer
     * @params
     * admin -> admin instance
     * stripeCustomer -> stripeCustomer instance
     * subscriptionArr -> array of subscriptions
     *  -> plan (a valid plan id)
     *  -> optional trial_period_days (number of trial period days for this plan overriding)
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
            Log::info(' in chargeSubscription : ', $array);
            $subscription = \Stripe\Subscription::create($array);
            return $subscription;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public static function cancelSubscription($stripeDetails, $user) {
        try {
            \Stripe\Stripe::setApiKey($stripeDetails->private_key);
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
            return $subscription->cancel();
        } catch(\Exception $e) {
            throw $e;
        }
    }
}
?>