<?php
namespace App\Traits;
use App\User;
use App\Helpers\StripeHelper;
use Illuminate\Http\Request;
use App\StripeDetails;
use Log, Hash, Auth, Session, Exception, Throwable, DB, View;
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

        public function getCustomerDetails($user, $refresh = false) {
            $stripeDetails = StripeDetails::first();
            if(!$user) return ['card' => []];
            if($refresh == true && $stripeDetails) {
                $details = json_decode(json_encode(StripeHelper::retriveCustomer($user->stripe_customer_id, $stripeDetails), true), true);
            } else {
                $details = json_decode($user->stripe_customer_obj, true);
            }
            
            $sourceData = $details['sources']['data'];
            $data['card'] = [];
            if($sourceData) {
                foreach($sourceData as $key => $eachCard) {
                    if ($eachCard['object'] == 'card') {
                        $data['card'] = $this->getCardData($eachCard);
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
            
            if(isset($arr['source']) && $arr['source'] != null) {
                Log::info('in source if');
                $detailsArr['source'] = $arr['source'];
            } else {
                Log::info('in source else');
                unset($detailsArr['source']);
            }
            Log::info('final array : ', $detailsArr);
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
            // Check if this customer already exists
            $stripeCustomerDetails = StripeHelper::fetchUserWithEmail($stripeDetails, $user->email);
            
            $stripeCustomerId = $user->stripe_customer_id;
            if(count($stripeCustomerDetails['customersArr']['data']) == 0) {
                // Customer does not exist
                return $this->createFreshStripeCustomer($user, $customerDetails, $stripeDetails);

            } else {
                $stripeCustomerId = $stripeCustomerDetails['customers']->data[0]->id;
            }
            $customerDetailsResponse = StripeHelper::updateCustomer($stripeCustomerId, $customerDetails , $stripeDetails);
            $user->stripe_customer_obj  =   json_encode($customerDetailsResponse, true);
            $user->card_updated         =   $customerDetailsResponse->sources !== null && count($customerDetailsResponse->sources->data) > 0 ? 1 : 0;
            $user->stripe_customer_id   =   $customerDetailsResponse->id;
            $user->save();
            return [
                'message'				=> 'Success',
                'userUpdated'		    => $user,
                'status' 				=> true
            ];
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

        public function updateCard(Request $request, $user = null) {
            try {
                DB::beginTransaction();
                $user           = $user == null ? Auth::user() : $user;
                $stripeToken    = $request->stripe_token;
                $stripeDetails  = StripeDetails::first();
                $params = [
                    'email'     	=> 	$user->email,
                    'source'    	=> 	$stripeToken,
                    'description'	=>	'Card updated from platform '.config('settings.APPLICATION-DOMAIN'),
                    'metadata'      =>  [
                        'email' =>  $user->email,
                        'name'  =>  $user->name,
                    ]
                ];
                $res = $this->createOrUpdateStripeCustomer($user, $this->prepareCustomerDetailsArray($params), $stripeDetails);
                if($res['status']) {
                    DB::commit();
                    return [
                        'status' 	=> true,
                        'card'		=> $this->getCustomerDetails(Auth::user())['card'],
                        'allowFurther' =>  true,
                        'message' 	=> 'Card updated successfully',
                        'user'      =>  $user
                    ];
                } else {
                    DB::commit();
                    return [
                        'status' 	=> false,
                        'allowFurther' =>  false,
                        'message' 	=> 'Card upddate failed.',
                        'user'      =>  $user
                    ];
                }
            } catch(Throwable $e) {
                DB::rollback();
                throw $e;
            }
        }

        
        public function upgradeOrDowngrade(Request $request, $user = null) {
            try {
                Log::info('subscribe -- came initial');
                $user 				= $user != null ? $user : Auth::user();
                $responseArray 		= $this->updateCard($request, $user);
                
                if($responseArray['status']) {
                    $plan 				    =   $request->plan;
                    $currentUserType 	    =   $user->user_type;
                    $baseUserType           =   $user->base_type;
                    $stripeDetails 		    =   StripeDetails::first();
                    $subscriptionId		    =   $user->stripe_subscription_id;
                    $existingSubscriptionId =   $user->stripe_subscription_id;
                    $userFeedback 		    =   $request->feedback;
                    $stripeCustomerId       =   $user->stripe_customer_id;
                    Log::info('subscribe -- going to condition');
                    if(strlen(trim($user->affiliate_id)) > 0) {
                        
                        // So this user came from a different platform like affiliates.
                        // This type of user should not be allowed to downgrade below the plan which their affiliate brought them into.
                        Log::info(' plan :::: '.$plan.' baseUserType :::: '.$baseUserType);
                        if($plan < $baseUserType) {
                            return [
                                'status'            =>  false,
                                'cardUpdated'       =>  $user->card_updated == 1 ? true : false,
                                'allowFurther'      =>  false,
                                'processComplete'   =>  false,
                                'newPlan'           =>  null,
                                'message'           =>  $plan < $baseUserType
                                    ? 'Since you are the member of affiliates programme you cannot downgrade directly beyond plan : '.getPlanName($baseUserType)
                                    : 'You already exist in the plan you want to upgrade to.',
                                'user'              =>  $user
                            ];
                        }
                    }

                    // We expect there is a subscription id accross this user, and this users card is already updated.
                    if(strlen(trim($subscriptionId)) == 0) {
                        // Create a new subscription.
                        $subscriptionData   =   StripeHelper::chargeSubscription($stripeDetails, $stripeCustomerId, getPlanName($plan));
                    } else {
                        // Upgrade user to a new subscription.
                        $subscriptionData	= 	StripeHelper::changeSubscription($stripeDetails, $subscriptionId, getPlanName($plan));
                    }

                    if(!is_object($subscriptionData)) {
                        return [
                            'status'            => false,
                            'cardUpdated'       => $user->card_updated == 1 ? true : false,
                            'allowFurther'      => false,
                            'processComplete'   => false,
                            'newPlan'           => null,
                            'message'           => 'Please check if your card has enough balance and try again.',
                            'user'              =>  $user
                        ];
                    }
                    $user->stripe_subscription_id = $subscriptionData->id;
                    $user->stripe_subscription_obj = json_encode($subscriptionData, true);
                    $user->user_type = $plan;
                    $user->is_subscribed = config('settings.SUBSCRIPTIONS.'.$subscriptionData->status);
                    // if($subscriptionData->status == 'cancelled' || $subscriptionData->status == 'unpaid' || $subscriptionData->status == 'past_due') {
                    //     $user->is_subscribed = 0;
                    // } else if($subscriptionData->status == 'trialing') {
                    //     $user->is_subscribed = 1;
                    // } else if($subscriptionData->status == 'active') {
                    //     $user->is_subscribed = 2;
                    // }
                    $user->save();
                    return [
                        'status'            =>  $user->is_subscribed == 1 || $user->is_subscribed == 2 ? true : false,
                        'cardUpdated'       =>  $user->card_updated == 1 ? true : false,
                        'processComplete'   =>  true,
                        'newPlan'           =>  $user->user_type,
                        'allowFurther'      =>  false,
                        'message'           =>  $user->is_subscribed == 1 || $user->is_subscribed == 2
                            ? 'Subscription changed to '.getPlanName($plan).' successfully!'
                            : 'Subscription failed, Please check with your card balance.',
                        'headerView'        =>  View::make('new_version.shared.reusable-user-panel-header', ['user' => $user])->render(),
                        'user'              =>  $user
                    ];

                } else {
                    return $responseArray;
                }
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

        public function createChargeHook($stripeDetails) {
            try {
                $strDetails = StripeDetails::first();
                $allWebhooks = StripeHelper::retriveAllWebhooks($strDetails->private_key);
                foreach($allWebhooks as $key => $each) {
                    StripeHelper::deleteWebhook($strDetails->private_key, $each->id);
                }
                $stripeArray = StripeHelper::createChargeWebhook($strDetails->private_key);
                if(isset($stripeArray['subscription']) && isset($stripeArray['invoice'])) {
                    return [
                        'status'			=>	true,
                        'message' 			=> 	'Webhooks configured successfully.',
                        'webhooks' 	        =>	$stripeArray
                    ];
                }
            } catch(Throwable $e) {
                return [
                    'status' 			=>  false,
                    'message'			=>  $e->getMessage(),
                    'webhooks'          =>  []
                ];
            }
        }

        public function makeProduct($keySecond, $name) {
            $product = StripeHelper::createProduct($keySecond, $name);
            $productArray = json_decode(json_encode($product, true), true);
            return ['product' => $product, 'productArray' => $productArray];
        }

        public function cancelSubscription($stripeCustomer, $user) {
            try {
                $response = StripeHelper::cancelSubscription($stripeCustomer, $user);
                return ['responseRaw' => $response, 'response' => json_decode(json_encode($response,true),true)];
            } catch(Throwable $e) {
                throw $e;
            }
        }
    }
?>


