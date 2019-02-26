<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use \App\User;
use Mail, Log, Exception, View, Session, Auth, Hash, DB, Throwable;
use App\Helpers\UserHelper;
use App\PasswordReset;
use \Carbon\Carbon;
use App\StripeDetails;
use App\Traits\StripeTrait;
use App\Traits\EmailTrait;

class AccountController extends Controller
{
	use StripeTrait, EmailTrait;

	public function failedSubscription() {
		$user = Auth::user();
		$stripeDetails = StripeDetails::first();
		return view('new_version.auth.profile.clear-payment', ['user' => $user, 'stripeDetails' => $stripeDetails]);
	}

	public function failedSubscriptionPost(Request $request) {
		try {
			DB::beginTransaction();
			$user = Auth::user();
			$stripeDetails = StripeDetails::first();
			$ret = $this->clearFailedInvoice($request, $user);
			if($ret['status']) {
				DB::commit();
				return redirect('profie')->with('success', 'You have successfully cleared out your pending payments.');
			}
			DB::rollback();
			return redirect()->back()->with('fail', 'Your attempt to clear out your previous payments have failed. '.$res['message']);
		} catch(Throwable $e) {
			DB::rollback();
			return redirect()->back()->with('fail', 'Error : '.$e->getMessage());
		}
	}

	public function cancelMembership() {
		$data['user'] = Auth::user();
		$data['title'] = 'Cancel membership';
		return view('new_version.auth.profile.cancel-membership', $data);
	}

	public function cancelMembershipPost(Request $request) {
		try {

			$user = Auth::user();
			if(strlen(trim($user->affiliate_id)) > 0 && $user->user_type <= $user->base_type) {
				return response()->json([
					'status'	=> false,
					'message' 	=> 'You can not directly cancel your membership as you are reffered from an affiliate chanel. In case you want to cancel your membership contact your service provider.'
				]);
			}

			if(strlen(trim($user->stripe_subscription_id)) <= 0) {
				return response()->json([
					'status' 	=> false,
					'message' 	=> 'Oops. We are unable to find your subscription id. Please contact your support with this issue.'
				]);
			}
			Log::info('level 1 cleared in cancel membership');
			$stripeDetails = StripeDetails::first();
			$response = $this->cancelSubscription($stripeDetails, $user);
			if($response['response']['status'] == 'canceled') {
				$user->left_because 		=	trim($request->reason);
				$user->is_subscribed 		= 	config('settings.SUBSCRIPTION.'.$response['response']['status']);
				Auth::logout();
				$user->delete();
				Log::info('level 2 cleared in cancel membership : success');
				// $user->delete();
				return response()->json([
					'status' 	=> true,
					'message' 	=> 'Subscription cancelled successfully'
				]);
			} else {
				return response()->json([
					'status' 	=> false,
					'message' 	=> 'Sorry we cannot cancel your subscription now. Please try again later.'
				]);
			}

		} catch(Throwable $e) {
			Log::info('level 3 cleared in cancel membership : failed');
			return response()->json([
				'status' 	=> false,
				'message' 	=> 'Error : '.$e->getMessage()
			]);
		}
	}

	public function updateCardDetails(Request $request) {
		try {
			$user = Auth::user();
			$responseArray 	= $this->updateCard($request);
			$card = $this->getCustomerDetails($user, true);
			$card = count($card) > 0 && isset($card['card']) ? $card['card'] : [];
			$responseArray['html'] = View::make('new_version.shared.embeded-card', ['user' => $user, 'card' => $card])->render();
			return response()->json($responseArray);
		} catch(Throwable $e) {
			return [
				'status' 	=> false,
				'message' 	=> 'Error : '.$e->getMessage(),
			];
		}
	}

	public function updateCardDetailsAndSubscribe(Request $request) {
		try {
			$user = Auth::user();
			$responseArray = $this->upgradeOrDowngrade($request);
			return response()->json($responseArray);
		} catch(Throwable $e) {
			return response()->json([
				'status' => false,
				'message' => $e->getMessage(),
			]);
		}
	}

	public function upgradeOrDowngradePlan(Request $request) {
		try {
			$user = Auth::user();
			$card = $this->getCustomerDetails($user, true);
			$card = count($card) > 0 && isset($card['card']) ? $card['card'] : [];
			Log::info('upgradeOrDowngradePlan : step 1');
			if($user->card_updated && count($card) > 0) {
				Log::info('upgradeOrDowngradePlan : step 2');
				$responseArray = $this->upgradeOrDowngrade($request);
				return response()->json($responseArray);
			} else {
				return response()->json([
					'status' => true,
					'cardUpdated' => false,
					'allowFurther' =>  true,
					'message' => 'Card is not updated'
				]);
			}
		} catch(Throwable $e) {
			return response()->json([
				'status' => false,
				'message' => $e->getMessage().' LINE : '.$e->getLine(),
			]);
		}
	}

	public function paymentInformation() {
		$user = Auth::user();
		$card = $this->getCustomerDetails($user, false);
		$card = count($card) > 0 && isset($card['card']) ? $card['card'] : [];
		$data = [
			'user' => $user,
			'stripeDetails' => StripeDetails::first(),
			'card' => $card
		];
		// dd($data);
		return view('new_version.auth.profile.payment-information', $data);
	}

	public function updatePaymentKeys() {
		return view('new_version.auth.profile.update-payment-keys', ['user' => Auth::user(), 'stripeDetails' => StripeDetails::first()]);
	}

	public function updatePaymentKeysPost(Request $request) {
		try {
			$publicKey = $request->public_key;
			$privateKey = $request->private_key;
			$stripeDetails = StripeDetails::first();
			if(!$stripeDetails) {
				$stripeDetails = new StripeDetails();
			}
			$stripeDetails->public_key = $publicKey;
			$stripeDetails->private_key = $privateKey;
			$stripeDetails->save();
			$obj = $this->createChargeHook($stripeDetails);
			if($obj['status']) {
				return redirect()->back()->with('success', 'Stripe Keys set and webhooks setup successfully.');
			} else {
				return redirect()->back()->with('success', $obj['message']);
			}
		} catch(Throwable $e) {
			return redirect()->back()->with('fail', 'Error : '.$e->getMessage());
		}
	}

	public function showMembershipPage() {
		try {
			
			$user 			=	Auth::user();
			$stripeDetails 	= 	StripeDetails::first();
			$plansArr 		= 	config('settings.PLAN.NAMEMAP');
			return view('new_version.auth.profile.membership', ['user' => $user, 'stripeDetails' => $stripeDetails]);

		} catch(Exception $e) {
			// dd($e);
			return redirect()->back()->with('Error : '.$e->getMessage());
		}
	}

	public function changePassword() {
		if(Auth::check()) {
			$user = Auth::user();
			return view('new_version.auth.change-password', compact('user'));
			// return view('change-password', compact('user'));
		}
		return redirect('home');
	}

	public function updateUserInfo(Request $request) {
		try {

			if(!Auth::check()) {
				return redirect()->back()->with('fail', 'Session expired. Please log in again.');	
			}
			$name = $request->fname . ' '. $request->lname;
			if(strlen($request->fname) == 0 || strlen($name) == 0) {
				return redirect()->back()->with('fail', 'Please enter a valid name.');
			}

			$user = Auth::user();
			$user->name = $name;
			$user->save();
			return redirect()->back()->with('success', 'Password updated successfully!');

		} catch(Exception $e) {
			return redirect()->back()->with('fail', 'ERROR : '.$e->getMessages().' LINE : '.$e->getLine());
		}
	}

	public function changePasswordPost(Request $request) {
		try {
			
			if(!Auth::check()) {
				return redirect()->back()->with('fail', 'Session expired. Please log in again.');	
			}

			$user = Auth::user();
			// $email 	= $request->email;
			$opass 	= $request->o_pass;
			$pass 	= $request->pass;
			$cpass 	= $request->c_pass;

			$oldPass  = $user->password;
			
			// if($email !== $user->email) {
			// 	return redirect()->back()->with('fail', 'Please enter your own email correctly!');
			// }

			if (!Hash::check($opass, $oldPass)) {
				return redirect()->back()->with('fail', 'Sorry your old password did not match!');
			}
			
			if($pass !== $cpass) {
				return redirect()->back()->with('fail', 'New password and confirm password should match!');
			}

			if($pass === $opass) {
				return redirect()->back()->with('fail', 'Current Password and Old Password should not match!');
			}
			
			if(strlen($pass) < 6) {
				return redirect()->back()->with('fail', 'Password should have minimum 6 characters.');
			}

			// Ready to update password
			$user->password = bcrypt($pass);
			$user->save();
			return redirect()->back()->with('success', 'Password updated successfully!');

		} catch(Exception $e) {
			return redirect()->back()->with('fail', 'ERROR : '.$e->getMessages().' LINE : '.$e->getLine());
		}
	}


	public function resetPasswordExternalPost($e_token, Request $request) {
		$pass = $request->password;
		$c_pass = $request->c_password;
		$email = $request->email;

		if(strlen($pass) < 6 || strlen($c_pass) < 6) {
			return redirect()->back()->with('error', 'Password and confirm password should be min 6 characters.');
		}
		
		if($pass !== $c_pass) { 
			return redirect()->back()->with('error', 'Password and confirm password should be same.');
		}

		$resetPass = PasswordReset::where('token', $e_token)->first();
		if(!$resetPass) {
			return redirect()->back()->with('error', 'Token provided is invalid.');
		}
		
		$user = User::where('email', $email)->first();
		if($user) {
			$user->password = bcrypt($pass);
			if($user->save()) {
				$resetPass->delete();
				return redirect()->route('loginPage')->with('success', 'Password updated successfully.');
			} else {
				return redirect()->back()->with('error', 'Cannot save record. Database connectivity issue.');	
			}
		} else {
			return redirect()->back()->with('error', 'This user does not exists.');
		}
	}

	public function resetPasswordExternalPage($e_token) {
		
		$errMsg = '';
		$user = $resetPass = null;
		$resetPass = PasswordReset::where('token', $e_token)->first();
		if(!$resetPass) {
			$errMsg = 'Oops! This link does not exist. Reset password links are valid for only '.config('settings.RESET-PASSWORD-LIFE').' hours!';
		}

		if($resetPass) {
			$user = User::where('email', $resetPass->email)->first();
			if(!$user) {
				$errMsg = 'User not found';
			}
		}

		$now = Carbon::now();
		if($resetPass && $user) {
			if($now->diffInDays($resetPass->created_at) > 3) {
				$errMsg = 'Oops! This link has expired. Link was valid for '.config('settings.RESET-PASSWORD-LIFE').' hours only!';
			}
		}
		return view('new_version.auth.reset-password-external', compact('errMsg', 'resetPass', 'user', 'e_token'));
		// return view('reset-password-external', compact('errMsg', 'resetPass', 'user', 'e_token'));
	}

	public function forgotPasswordPost(Request $request) {
		// dd($request->all());
		$email = $request->email;
		if(Auth::check()) {
			return redirect()->back()->with('error', 'You are already logged in. You can change your password from change password panel in dashboard!');
		}
		$user = User::where('email', $email)->first();
		if(!$user) {
			return redirect()->back()->with('error', 'Please recheck your email and try again.');
		}
		
		PasswordReset::where('email', $email)->delete();

		$passwordReset = new PasswordReset();
		$passwordReset->email = $email;
		$passwordReset->token = str_random(20).time().uniqid();
		$passwordReset->save();


		$admin_users_email="work@tier5.us";
		$user_name=$user->name;
		$title="Password reset link from DOMAINLEADS.";
		$subject="DomainLeads Password Reset";
		$content="Please reset your password.";
		$link = config('settings.APPLICATION-DOMAIN').'/reset-password'.'/'.$passwordReset->token;
		Mail::send('emails.forgot-password', ['title' => $title, 'content' => $content, 'user_name'=> $user_name, 'link' => $link], 
		function ($message) use ($admin_users_email,$email,$user_name,$subject)
		{
			$message->from($admin_users_email);
			$message->to($email,$user_name);
			$message->subject($subject);
		});
		if (Mail::failures()) {
			return redirect()->back()->with('error', 'Mail is not sent.');
			Log::info('Mail not sent successfully');
		} else {
			Log::info('Mail sent successfully');
		}
		
		return redirect()->back()->with('success', 'Email has been sent successfully');
	}

	public function forgotPassword() {
		if(\Auth::check()) {
			return redirect('search');
		}
		return view('new_version.auth.forgot-password');
		// return view('forgot-password');
	}

	public function loginPage() {
		if(\Auth::check()) {
			return redirect('search');
		}
		return view('new_version.auth.login');
		// return view('login');
	}

	public function signupPage() {
		
		if(\Auth::check()) {
			return redirect('search');
		}
		$stripeDetails = StripeDetails::first();
		return view('new_version.auth.register', ['stripeDetails' => $stripeDetails]);
		// return view('signup');
	}

	public function profile() {

		if(Auth::check()) {
			$user = Auth::user();
			// return view('profile', compact('user'));
			return view('new_version.auth.profile', compact('user'));
		}
		return redirect()->route('loginPage');
	}

	public function verifyEmail(Request $request) {
		$id = $request->id;
		$paramUser = User::find($id);
		if(!$paramUser) {
			return redirect('home');
		}
		if(Auth::check()) {
			$user = Auth::user();
			if($user->id !== $id) {
				return redirect('home');
			}
			if($user->email_verified == 0) {
				$user->email_verified = '1';
				$user->save();
				return view('new_version.verification.email-verification', ['user' => $user]);
			} else {
				return redirect('home');
			}
		} else {
			if($paramUser->email_verified == 0) {
				$paramUser->email_verified = '1';
				$paramUser->save();
				return view('new_version.verification.email-verification', ['user' => $paramUser]);
			} else {
				return redirect('home');
			}
		}
	}

	// public function signupPost(Request $request) {
		
	//   $first_name=$request->first_name;
	//   $last_name=$request->last_name;
	//   $email=$request->email;
	//   $password=$request->password;
	//   $remember_token=$request->_token;
	//   $date=date('Y-m-d H:i:s');
	  
	//   $validator=Validator::make(
	//    $request->all(),
	//    array(
	//    'first_name'=>'required',
	// 	'last_name'=>'required',
	// 	 'email'=>'required|email',
	// 	  'password'=>'required',
	// 	   'c_password'=>'required | same:password'
	//    ), [
	// 	   'c_password.required' => 'Confirm password is required.',
	// 	   'c_password.same' => 'Confirm password should be same as password.'
	//    ]
	//   );
	  
	//   if($validator->fails()) {
	// 	  return redirect()->back()->withErrors($validator)->withInput();
	//   } 
	//   else {
		  
	// 	  $id_email = DB::table('users')->select('email')->where('email',$email)->get();
	// 	  if(count($id_email) ==0)
	// 	  {
 
	// 		  $u = new User();
	// 		  $u->name = $first_name." ".$last_name;
	// 		  $u->email = $email;
	// 		  $u->password = Hash::make($password);
	// 		  $u->remember_token = $remember_token;
	// 		  $u->user_type = 1;
			  
			  
	// 		  if($u->save()) {
	// 			//   \Session::put('emailset',$email);
	// 			//   \Session::put('passset',$password);
	// 			//   $admin_users_email="work@tier5.us";
	// 			//   $user_name=$u->name;
	// 			//   $title="Thanks for sign up with DomainLeads";
	// 			//  $subject="DomainLeads Signup";
	// 			//  $content="Thanks for sign up with DomainLeads";
	// 			//  \Mail::send('emails.thankyou', ['title' => $title, 'content' => $content,'user_name'=>$user_name], function ($message)use ($admin_users_email,$email,$user_name,$subject)
	// 			//  {
	// 			// 	 $message->from($admin_users_email);
	// 			// 	 $message->to($email,$user_name);
	// 			// 	 $message->subject($subject);
	// 			//  });
	// 			//  \Mail::send('emails.accback', ['title' => $title, 'content' => $content,'user_name'=>$user_name,'email'=>$email], function ($message)use ($admin_users_email,$email,$user_name,$subject)
	// 			//  {
	// 			// 	 $message->from($email,$user_name);
	// 			// 	 $message->to($admin_users_email);
	// 			// 	 $message->subject($subject);
	// 			//  });
	// 			$userdata = ['email' => $email, 'password' => $password];
	// 			if (Auth::validate($userdata)) {
	// 				if (Auth::attempt($userdata)) {
	// 					return redirect('search');
	// 				} else {
	// 					return redirect()->back()->with('error', 'PLEASE CHECK YOUR EMAIL AND PASSWORD! ');
	// 				}
	// 			  } else {
	// 				return redirect()->back()->with('error', 'PLEASE CHECK YOUR EMAIL AND PASSWORD! ');
	// 			  }
	// 			return \Response::json(array("msg"=>"success" , "user_id"=>$u->id));
	// 		  }
	// 		  else {
	// 			 return \Response::json(array("msg"=>"error2" , "user_id"=>null));
	// 		 }
	// 	  }   
	// 	  return \Response::json(array("msg"=>"error3" , "user_id"=>null));
		  
	//   }
	// }



	public function signupPost(Request $request) {
		
		try {

			DB::beginTransaction();
			$fullName 		= 	$request->full_name;
			$email			=	$request->email;
			$password		=	$request->password;
			$remember_token	=	$request->_token;
			$date			=	date('Y-m-d H:i:s');
			$affiliateId	=	$request->affiliate_id;
			$plan			=	$request->plan;
			$stripeToken	=	$request->stripe_token;
			
			$validator=Validator::make($request->all(), [
				'full_name'	=>'required',
				'email'		=>'required|email',
				'password'	=>'required',
				'cpassword'=>'required | same:password'
			], [
				'cpassword.required' => 'Confirm password is required.',
				'cpassword.same' => 'Confirm password should be same as password.'
			]);
			
			if($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			} else {	
				$id_email = User::where('email', $email)->select('email')->first();
				
				if(!$id_email) {
					$newUser 					= 	new User();
					$newUser->name 				= 	$fullName;
					$newUser->email 			= 	$email;
					$newUser->password 			=	bcrypt($password);
					$newUser->remember_token 	= 	$remember_token;
					$newUser->user_type 		= 	$plan;
					if(strlen(trim($affiliateId)) > 0) {
						$newUser->affiliate_id		= 	$affiliateId;
					}
					$newUser->is_hooked 		=	'0';
					$newUser->email_verified	= 	'0';
					$newUser->save();
					$return 					= 	$this->upgradeOrDowngrade($request, $newUser);
					$newUser 					= 	$return['user'];

					if($return['status'] == true) {

						// The user got subscribed successfully
						$userdata = ['email' => $email, 'password' => $password];
						if (Auth::validate($userdata)) {
							if (Auth::attempt($userdata)) {
								DB::commit();
								return redirect('search')->with('first_visit', 'Yes');
							} else {
								DB::rollback();
								return redirect()->back()->with('error', 'Please check your email and password!')->withErrors($validator)->withInput();
							}
						} else {
							DB::rollback();
							return redirect()->back()->with('error', 'Please check your email and password!');
						}
						
					} else {
						DB::rollback();
						// Subscription failure
						return redirect()->back()->with('error', 'Your subscription is not successful! Please check if your card has enough balance.');
					}
				}
				DB::rollback();
				return redirect()->back()->with('error', 'This email id already exists. Please try again');
			}
		} catch(Throwable $e) {
			DB::rollback();
			return redirect()->back()->with('error', $e->getMessage());
		}
	}

	// public function checkFirstVisit() {
	// 	$user = Auth::user();
	// 	if($user->first_visit == 0) {
	// 		return redirect('search');
	// 	}
	// 	$user->first_visit = 1;
	// 	$user->save();
	// 	return view('new_version.welcome');
	// }

	public function home() {
		$stripeDetails 	= 	StripeDetails::first();
		$user = Auth::check() ? Auth::user() : null;
		return view('home', ['user' => $user, 'stripeDetails' => $stripeDetails]);
	}

    public function login(Request $request) {
	try {
		
		$rules = array(
			'email' => 'required|email',
			'password' => 'required|min:6'
		);
		$userdata = array(
				'email' => $request->email,
				'password' => $request->password
		);
		$validator = Validator::make($userdata , $rules);
	
		if($validator->fails()) {
			return redirect()->back()->with('error', 'Invalid login credentials provided! ');
		}

		else 
		{
		  if (Auth::validate($userdata)) {
			if (Auth::attempt($userdata)) {

				$user = Auth::user();
				if($user->suspended == 1) {
					Auth::logout();
					return redirect()->back()->with('error', 'Your account has been suspended! Please contact with the administrator.');
				}
				return redirect('search');
			} else {
				return redirect()->back()->with('error', 'Please check your email and password!');
			}
		  } else {
			return redirect()->back()->with('error', 'Please check your email and password!');
		  }
		}
	} catch(\Throwable $e) {
		return redirect()->back()->with('error', 'ERROR : '.$e->getMessage());
	}
  }
  public function regredirect(){
		
		$userdata = array(
		'email' => \Session::get('emailset'),
		'password' => \Session::get('passset')
		);
		
			if (Auth::validate($userdata)) {
				if (Auth::attempt($userdata)) {
					return redirect()->route('search');
				}
			} 
			else 
			{
				return "error1";
			}
		
  }

  public function logout() 
  {
  	Auth::logout(); // logout user
  	return redirect()->route('home');//redirect back to login
  }


  public function signme(Request $request)
	{

	 // dd('here');
	  //print_r($request->all()); dd();
	 $first_name=$request->first_name;
	 $last_name=$request->last_name;
	 $email=$request->email;
	 $password=$request->password;
	 $remember_token=$request->_token;
	 $date=date('Y-m-d H:i:s');
	 
	 $validator=Validator::make(
	  array(
	  'first_name'=>$request->first_name,
	   'last_name'=>$request->last_name,
	    'email'=>$request->email,
		 'password'=>$request->password,
		  'c_password'=>$request->c_password
	  ),
	  array(
	  'first_name'=>'required',
	   'last_name'=>'required',
	    'email'=>'required|email',
		 'password'=>'required',
		  'c_password'=>'required | same:password'
	  )
	 );
	 
	 if($validator->fails()) {
	 //return redirect('signup')->withErrors($validator)->withInput();
	 	return "error1";
	 } 
	 else {
	     
		 $id_email = DB::table('users')->select('email')->where('email',$email)->get();
		 if(count($id_email) ==0)
		 {

		 	$u = new User();
		 	$u->name = $first_name." ".$last_name;
		 	$u->email = $email;
		 	$u->password = Hash::make($password);
		 	$u->remember_token = $remember_token;
		 	$u->user_type = 1;
		 	
		 	
			 if($u->save()) {
			 	\Session::put('emailset',$email);
			 	\Session::put('passset',$password);
			 	$admin_users_email="work@tier5.us";
			 	$user_name=$u->name;
			 	$title="Thanks for sign up with DomainLeads";
                $subject="DomainLeads Signup";
                $content="Thanks for sign up with DomainLeads";
                \Mail::send('emails.thankyou', ['title' => $title, 'content' => $content,'user_name'=>$user_name], function ($message)use ($admin_users_email,$email,$user_name,$subject)
                {
                    $message->from($admin_users_email);
                    $message->to($email,$user_name);
                    $message->subject($subject);
                });
                \Mail::send('emails.accback', ['title' => $title, 'content' => $content,'user_name'=>$user_name,'email'=>$email], function ($message)use ($admin_users_email,$email,$user_name,$subject)
                {
                    $message->from($email,$user_name);
                    $message->to($admin_users_email);
                    $message->subject($subject);
                });
			 	return \Response::json(array("msg"=>"success" , "user_id"=>$u->id));
			 }
			 else {
				return \Response::json(array("msg"=>"error2" , "user_id"=>null));
			}
	     }   
	     return \Response::json(array("msg"=>"error3" , "user_id"=>null));
		 
	 }
	   
	}

	public function UserList(Request $request) {
		try {
			if(!\Auth::check()) {
				return redirect()->back()->with('error', 'Session expired. Please Log In Again!');
			}
			if(\Auth::user()->user_type != config('settings.ADMIN-NUM')) {
				return redirect()->back()->with('error', 'Access denied.');
			}

			$userTypes = ['Select Users', 'all-users', 'suspended-users'];
			$search = $request->search;
			if(strlen($search) > 0) {
				$users = User::where('name', 'LIKE', '%'.$search.'%')->orWhere('email','LIKE','%'.$search.'%');
			} else {
				$users = User::where('user_type','!=',config('settings.ADMIN-NUM'));
			}
			if($request->has('usertype') && strlen($request->usertype) > 0) {
				$usertype = $request->usertype;
				if($request->usertype == $userTypes[2]) {
					$users->where('email','LIKE','%_suspended');
				}
			}
			$perpageset = ['per-page',20,50,100];
			$perpage = $request->has('perpage') && is_numeric($request->perpage) ? $request->perpage : $perpageset[1];
			
			$request->perpage = !$request->perpage ? $request->perpage : $perpageset[1];
			$users = $users->orderBy('id', 'DESC')->paginate($perpage);
			return view('home.userList',compact('users','userTypes','perpageset'));
		} catch(\Exception $e) {
			return redirect()->back()->with('error', 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine());
		}
	}

	public function deleteUser(Request $request) {
		return UserHelper::deleteUser($request);
	}

	public function suspendOrUnsuspendUser(Request $request) {
		return UserHelper::suspendOrUnsuspendUser($request);
	}

	public function createUser(Request $request) {
		$response = UserHelper::createUser($request);
		$responseArray = json_decode($response->content(), true);
		if($responseArray['status'] == false) {
			return redirect()->back()->with('error', $responseArray['message']);
		}
		return redirect()->back()->with('success', $responseArray['message']);
	}

	public function editUser(Request $request) {
		$response = UserHelper::editUser($request);
		$responseArray = json_decode($response->content(), true);
		if($responseArray['status'] == false) {
			return redirect()->back()->with('error', $responseArray['message']);
		}
		return redirect()->back()->with('success', $responseArray['message']);
	}
}
