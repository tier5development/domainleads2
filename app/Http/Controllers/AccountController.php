<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use \App\User;
use DB;
use Hash;
use Auth;
use Session;

class AccountController extends Controller
{
	public function home()
	{
		return view('home');
	}

    public function login(Request $request)
    {
    // Getting all post data
    //$data = Input::all();
    //$data = $request->request->toArray();
    //dd($request->all());
	  
    // Applying validation rules.
    $rules = array(
		'email' => 'required|email',
		'password' => 'required|min:6'
	     );
    $userdata = array(
		    'email' => $request->email,
		    'password' => $request->password
		  );
    //$validator = Validator::make($request->request, $rules);

    $validator = Validator::make($userdata , $rules);

    if ($validator->fails()){
    return "error2";
     // return Redirect::to('/login')->withInput(Input::except('password'))->withErrors($validator);
    }
    else 
    {
    //   $userdata = array(
		  //   'email' => $request->email,
		  //   'password' => $request->password
		  // );
      // doing login.
      if (Auth::validate($userdata)) {
        if (Auth::attempt($userdata)) {
          return "success";
        }
      } 
      else 
      {
        return "error1";
      }
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
			 	return \Response::json(array("msg"=>"success" , "user_id"=>$u->id));
			 }
			 else {
				return \Response::json(array("msg"=>"error2" , "user_id"=>null));
			}
	     }   
	     return \Response::json(array("msg"=>"error3" , "user_id"=>null));
		 
	 }
	   
	}


}
