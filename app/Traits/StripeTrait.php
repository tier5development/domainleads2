<?php
namespace App\Traits;
use App\User;
use App\Helpers\StripeHelper;
use Illuminate\Http\Request;
use App\StripeDetails;
use Log;
use Hash;
use Auth;
use Session;
use Exception;
use App\Helpers\UserHelper;
use \Carbon\Carbon;


    trait StripeTrait {
        /**
            * this function returns card logo for frontend i.e for a type of card it returns a css class
            * @params
            * brand
            * @response
            * card name in form of a string
        */
        private function returnCardLogo($brand) {
            switch($brand) {
                case 'visa'             : return 'visa';
                case 'visa2'            : return 'visa2';
                case 'discover'         : return 'discover';
                case 'mastercard'       : return 'master';
                case 'diners club'      : return 'diners';
                case 'diners2'          : return 'diners2';
                case 'jcb'              : return 'jcb';
                case 'american express' : return 'amex';
                default                 : return 'unknown';
            }
        }

        private function getCardData($card) {
            $temp = [];
            $temp['last4'] = $card['last4'];
            $temp['brand'] = $card['brand'];
            $temp['object'] = $card['object'];
            $temp['exp_month'] = $card['exp_month'];
            $temp['exp_year'] = $card['exp_year'];
            $temp['id'] = $card['id'];
            $temp['name'] = $card['name'];
            $temp['fingerprint'] = $card['fingerprint'];
            $temp['funding'] = $card['funding'];
            $temp['type'] = $card['brand'].' '.$card['funding'].' '.$card['object'];
            $temp['country'] = $card['country'];
            $temp['cvc_check'] = $card['cvc_check'];
            $temp['countryFlag'] = strtolower($card['country']) == 'us' ? url('/').'/public/img/usaFlag.png' : '';
            $temp['cardLogoClass'] = $this->returnCardLogo(strtolower($card['brand']));
            return $temp;
        }

        public function getCustomerDetails($user, $refresh = false, $stripeDetails = null) {
            if(!$user) return ['cards' => []];
            if($refresh == true && $stripeDetails) {
                $details = json_decode(json_encode(StripeHelper::retriveCustomer($user->stripe_customer_id, $stripeDetails), true), true);
            } else {
                $details = json_decode($user->stripe_customer_obj, true);
            }
            
            $sourceData = $details['sources']['data'];
            $data['cards'] = [];
            if($sourceData) {
                foreach($sourceData as $key => $eachCard) {
                    if ($eachCard['object'] == 'card') {
                        $data['cards'] = $this->getCardData($eachCard);
                        break; // To check this is the default card or not.
                    }
                }
            }
            return $data;
        }

        /**
            * Purpose : Builds a proper customer array for stripe
            * Author : Tier5 LLC [akash@tier5.in]
            * Info : For more info on stripe customers check https://stripe.com/docs/api#create_customer
            * @params
            * 	1> arr : contains all customer details as key value format
            * 				`email` is the only necessary field
            *  2> adminDetails : admin object to which customer is attached
            * 
            * @request response array containing `message`, `stripeCustomer`, `status`
            */
        private static function prepareCustomerDetailsArray($arr) {
            
            $detailsArr =  [
                'email'         => trim($arr['email']),
                'description'   => isset($arr['description']) 
                                    ? $arr['description'] 
                                    : 'Customer from domainleads.',
                'metadata'      => isset($arr['metadata']) 
                                    ? $arr['metadata'] 
                                    : [ 
                                        'email' => trim($arr['email'] ),
                                        'name'  => isset($arr['name']) && strlen(trim($arr['name'])) > 0
                                            ? trim($arr['name']) 
                                            : explode('@', trim($arr['email']))[0]
                                    ]
            ];
            
            if(isset($arr['source'])) {
                $detailsArr['source'] = $arr['source'];
            }
            return $detailsArr;
        }

        /**
         * Purpose : To create or update a stripe customer
         * Author : Tier5 LLC [akash@tier5.in]
         * Info : For more info on stripe customers check https://stripe.com/docs/api#create_customer
         * @params
         * 	1> arr : contains all customer details as key value format
         * 				`email` is the only necessary field
         *  2> adminDetails : admin object to which customer is attached
         * 
         * @request response array containing `message`, `stripeCustomer`, `status`
         */
        public function createOrUpdateStripeCustomer($user, $arr, $stripeDetails) {
            // returning error if email field is absent and adminDetails is absent
            if(!isset($arr['email']) || $user == null) {
                return [
                    'message' => 'Inappropriate parameters',
                    'userUpdated' => null,
                    'status' => false
                ];
            }
                
            //if this customer already exists update customer
            $customerDetails = self::prepareCustomerDetailsArray($arr);
            $customerDetailsResponse = StripeHelper::updateCustomer($user->stripe_customer_id, $arr, $stripeDetails);
            
            // dd(json_decode($customerDetailsResponse, true));
            if(!is_object($customerDetailsResponse)) {
                // customer present in db but not in stripe
                return $this->createFreshStripeCustomer($user, $customerDetails, $stripeDetails);
            } else {
                //update completed successfully
                Log::info('updated customer : going to store response in db');
                $user->stripe_customer_obj  =   json_encode($customerDetailsResponse, true);
                $user->card_updated         =   count($customerDetailsResponse->sources->data) > 0 ? 1 : 0;
                $user->save();
                return [
                    'message'				=> 'Success',
                    'userUpdated'		    => $user,
                    'status' 				=> true
                ];
            }
        }
        
        public function createFreshStripeCustomer($user, $arr, $stripeDetails) {
            
            try {
                Log::info('in createFreshStripeCustomer : create fresh stripe customer');
                if(!array_key_exists('email', $arr) || !isset($arr['email'])) {
                    return [
                        'message'       => 'Inappropriate parameters',
                        'userUpdated'	=> null,
                        'status'        => false
                    ];
                }
                $customerDetails = self::prepareCustomerDetailsArray($arr);
                $customerResponse = StripeHelper::createCustomer($customerDetails, $stripeDetails);
                if(!is_object($customerResponse)) {
                    return [
                        'message'       => 'Creating stripe customer failed! Please try again later',
                        'userUpdated'   => null,
                        'status'        => false
                    ];
                }
                $user->card_updated = isset($customerDetails['source']) ? 1 : 0;
                $user->stripe_customer_id = $customerResponse->id;
                $user->stripe_customer_obj = json_encode($customerResponse, true);
                $user->save();
                return [
                    'message'               =>  'Success',
                    'userUpdated'	        =>  $user,
                    'status'                =>  true
                ];
            } catch(Throwable $e) {
                throw $e;
            }
        }

        /**
         * Purpose : To update our db with stripe (public available functions)
         * Author : Tier5 LLC [akash@tier5.in]
         * @params
         * 	1> stripeCustomerId : stripe customer id as given by stripe for an user
         *  2> adminDetails : admin object to which customer is attached
         * 
         * @response boolean response
         */
        public static function updateOurDbWithStripe($adminDetails, $stripeCustomerId) {
            try {
                $stripeCustomer = json_encode(StripeHelper::retriveCustomer($adminDetails->id, $stripeCustomerId), true);
                $stripeObjArr 	= json_decode($stripeCustomer, true);
                if($stripeCustomer == 'null') {
                    return false;
                }
                $customer = StripeCustomer::where('admin_id', $adminDetails->id)->where('stripe_customer_id', $stripeCustomerId)->first();
                $customer->previous_object = $customer->recent_object;
                $customer->recent_object   = $stripeCustomer;
                $customer->card_updated    = count($stripeObjArr['sources']['data']) > 0 ? 1 : 0;
                $customer->save();
                self::createOrUpdateStripeCustomerDetails($customer->id);
                return true;
            } catch(\Exception $e) {
                \Log::info('error in updateOurDbWithStripe'.$e->getMessage());
                return false;
            }
        }

        public function retriveCustomerWithSource($customer, $stripeBankAccountToken) {
            $stripeBankData = $customer->sources->all(['limit' => 1, 'object' => 'bank_account'])->data;
            $source  		= null;
            if(count($stripeBankData) == 0) {
                $customer->sources->create(["source" => $stripeBankAccountToken]);
                $data = $customer->sources->all(['limit' => 1, 'object' => 'bank_account'])->data;
                return $data[0]->id;
                // $source = $btok_parsed['stripe_bank_account_token'];
            } else {
                return $stripeBankData[0]->id;
                // $source = $stripeBankData[0]->id;
            }
        }

        public function updateCard(Request $request) {
            try {
                DB::beginTransaction();
                $user           = Auth::user();
                $stripeToken    = $request->stripe_token;
                $stripeDetails  = StripeDetails::first();
                $params = [
                    'email'     	=> 	$user->email,
                    'source'    	=> 	$stripeToken,
                    'name'			=>	$user->name,
                    'description'	=>	'Card updated from platform '.config('settings.APPLICATION-DOMAIN')
                ];
                $res = $this->createOrUpdateStripeCustomer($user, $params, $stripeDetails);
                if($res['status']) {
                    DB::commit();
                    return [
                        'status' 	=> true,
                        'card'		=> $this->getCustomerDetails(Auth::user())['cards'],
                        'message' 	=> 'Card updated successfully',
                    ];
                } else {
                    DB::commit();
                    return [
                        'status' 	=> false,
                        'message' 	=> 'Card upddate failed.',
                    ];
                }
            } catch(Throwable $e) {
                DB::rollback();
                throw $e;
            }
        }

        public function upgradeUser(Request $request) {
            try {
                $plan = $request->plan;
                $stripeDetails = StripeDetails::first();
                
                // Check whether this plan exists in config or not.
                if(!config('settings.PLAN.NAMEMAP')[$plan]) {
                    return [
                        'status' => false,
                        'cardUpdated' => true,
                        'message' => 'This plan does not exist.'
                    ];
                }
    
                $user = Auth::user();
    
                $subscriptionId = $user->stripe_subscription_id;
                $lastPlanId = $user->stripe_plan_id;
                $wouldbePlanId = config('settings.PLAN.NAMEMAP'.$plan);
                $currentPlanId = $user->user_type;
    
                // check whether the user is having any subscription against him,
                if(strlen($subscriptionId) > 0) {
                    // Check if user is in the same subscription plan
                    if($lastPlanId == $plan || $currentPlanId == $wouldbePlanId) {
                        return response()->json([
                            'status' 		=> 	true,
                            'cardUpdated' 	=> 	true,
                            'message' 		=>	'You are already on the plan that you are trying to subscribe.'
                        ]);
                    }
    
                    // if yes upgrade user
                    StripeHelper::changeSubscription($stripeDetails, $subscriptionId, $plan);
                }
                
                // if no then create a new subscription for the user.
    
                // Check if the user has his card updated directly from stripe.
                $stripeCustomerId = $user->stripe_customer_id;
                $card = $this->getCustomerDetails($user, true, $stripeDetails)['cards'];
                if(count($card) > 0) {
                    // So the user has card info updated with his account.
                    // So create a new subscription in his name.
    
                    $newSubscription = StripeHelper::chargeSubscription($stripeDetails, $stripeCustomerId, $plan);
                    return [
                        'status' => true,
                        'cardUpdated' => true,
                        'message' => 'Success',
                        'newSubscription' => $newSubscription
                    ];
    
                } else {
                    return [
                        'status' => true,
                        'cardUpdated' => false,
                        'message' => 'No card found.',
                    ];
                }
                $stripeCustomer = StripeHelper::retriveCustomer($stripeCustomerId, $stripeDetails);
    
            } catch(Throwable $e) {
                throw $e;
            }
        }
	

        /**
         * Refer to the helper function in stripe helper
         */
        public function chargeCustomer($admin, $paramArr, $type = null) {
            $charge = StripeHelper::createCharge($admin, $paramArr, $type);
            self::updateOurDbWithStripe($admin, $paramArr['customer']);
            return $charge;
        }

        public function createChargeHook($admin) {
            try {
                $paymentKeys = $admin->payment_keys;
                if($paymentKeys && strlen($paymentKeys->key_second) > 0) {
                    $secondKey 	= $paymentKeys->key_second;
                    $webhook 	= StripeHelper::createChargeWebhook($secondKey);
                    return [
                        'status'			=>	true,
                        'message' 			=> 	'Webhooks configured successfully.',
                        'stripeWebhookObj' 	=>	$webhook,
                        'stripeWebhook' 	=> 	json_decode(json_encode($webhook, true), true)
                    ];
                } else {
                    return [
                        'status'			=>	false,
                        'message' 			=> 'No payment keys found for this admin.',
                        'stripeWebhookObj' 	=> null,
                        'stripeWebhook' 	=> null
                    ];
                }
            } catch(\Exception $e) {
                return [
                    'status' 			=> false,
                    'message'			=> $e->getMessage(),
                    'stripeWebhookObj' 	=> null,
                    'stripeWebhook' 	=> null,
                ];
            }
        }

        public function makeProduct($keySecond, $name) {
            $product = StripeHelper::createProduct($keySecond, $name);
            $productArray = json_decode(json_encode($product, true), true);
            return ['product' => $product, 'productArray' => $productArray];
        }
    }
?>


