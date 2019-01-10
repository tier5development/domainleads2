<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use \App\User;
use DB;
use Hash;
use Auth;
use Session;
use Mail, Log, Exception;
use App\Helpers\UserHelper;
use App\PasswordReset;
use \Carbon\Carbon;

class AccountController extends Controller
{

	public function changePassword() {
		if(Auth::check()) {
			$user = Auth::user();
			return view('change-password', compact('user'));
		}
		return redirect('home');
	}

	public function changePasswordPost(Request $request) {
		try {
			
			if(!Auth::check()) {
				return redirect()->back()->with('fail', 'Session expired. Please log in again.');	
			}

			$user = Auth::user();
			$email 	= $request->email;
			$opass 	= $request->o_pass;
			$pass 	= $request->pass;
			$cpass 	= $request->c_pass;

			$oldPass  = $user->password;
			
			if($email !== $user->email) {
				return redirect()->back()->with('fail', 'Please enter your own email correctly!');
			}

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
		$resetPass = PasswordReset::where('token', $e_token)->first();
		if(!$resetPass) {
			$errMsg = 'This link has expired.';
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
				$errMsg = 'This link has expired. Link was valid for 72 hours only!';
			}
		}
		return view('reset-password-external', compact('errMsg', 'resetPass', 'user', 'e_token'));
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
		return view('forgot-password');
	}

	public function loginPage() {
		if(\Auth::check()) {
			return redirect('search');
		}
		return view('login');
	}

	public function home()
	{
		if(\Auth::check()) {
			return redirect('search');
		}
		return redirect('login');
		//return view('home');
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
	
		if ($validator->fails()){
			return redirect()->back()->with('error', 'Invalid Login credentials!');
		}
		else 
		{
		  if (Auth::validate($userdata)) {
			if (Auth::attempt($userdata)) {
				return redirect('search');
			} else {
				return redirect()->back()->with('error', 'Cannot authenticate! Please try agin with valid credentials');
			}
		  } else {
			return redirect()->back()->with('error', 'Cannot authenticate. Please try agin with valid credentials!');
		  }
		}

	} catch(\Exception $e) {
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
			if(\Auth::user()->user_type != 4) {
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
