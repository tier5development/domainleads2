<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator;

class UserManagementController extends Controller
{

    /**
     * create user endpoint
     * @param : email
     * @return json 
     */
    public function createUser(Request $request) {
        try {
            
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email')
                ], 200);
            }
            if(User::where('email', $email)->first()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This email is already registered!'
                ], 200);
            }

            // Check for suspended user
            if(User::where('email', $email.'_suspended')->first()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This email is already registered but suspended!'
                ], 200);
            }

            $newUser = new User();
            $newUser->name = $request->has('name') && strlen(trim($request->name)) > 0 
                                ? $request->name
                                : explode('@',$request->email)[0];
            $newUser->email = $request->email;
            $newUser->password = Hash::make(123456);
            $newUser->user_type = 1;
            $newUser->membership_status = 1;
            if($newUser->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User Created successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot connect to database, Try again later'
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage(). ' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * delete user endpoint
     * @param : email
     * @return json 
     */
    public function deleteUser(Request $request) {
        try {
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email')
                ], 200);
            }

            $deleteInfo = User::where('email', $email)->orWhere('email', $email.'_suspended')->delete();
            
            if($deleteInfo > 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'User Deleted successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No such user or user alredy deleted!'
                ], 200);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * suspend user endpoint
     * @param : email
     * @return json 
     */
    public function suspendUser(Request $request) {
        try {
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email')
                ], 200);
            }

            $user = User::where('email', $email)->first();
            if(!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No such user or user is already suspended!'
                ], 200);
            }

            $user->email = $email.'_suspended';
            
            if($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User suspended successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Database connectivity error! Please try again later'
                ], 200);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * unsuspend user endpoint
     * @param : email
     * @return json 
     */
    public function unsuspendUser(Request $request) {
        try {

            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email')
                ], 200);
            }

            $user = User::where('email', $email.'_suspended')->first();
            if(!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'No such user or user is already un-suspended!'
                ], 200);
            }
            $user->email = $email;
            if($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User un-suspended successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Database connectivity error! Please try again later'
                ], 200);
            }

        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * gets list of all-suspended-user
     * @param : email
     * @return json 
     */
    public function allSuspendedUser() {
        $users = User::where('email', 'LIKE', '%_suspended%')->select('name','email')->get();
        return response()->json(['status' => true, 'total' => $users->count() ,'users' => $users], 200);
    }
}
