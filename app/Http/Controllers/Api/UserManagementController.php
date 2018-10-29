<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator;
use App\Helpers\UserHelper;
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
            
            $usertype = 1;
            if($request->has('user_type')) {
                if(!is_numeric($request->user_type) || $request->user_type < 1 || $request->user_type > 2) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid user type provided, user types should be in between 1 and 2'
                    ], 200);
                } else {
                    $usertype = (int)$request->user_type;
                }
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
            $newUser->user_type = $usertype;
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

    public function editUser(Request $request) {
        try {
            $email = $request->email;
            $validator = Validator::make($request->all(), 
            [
                'email' => 'required|email|max:255|unique:users,id,'.$email,
                'user_type' => 'required|numeric|integer|between:1,3'
            ]);
            
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email').' '.$validator->errors()->first('user_type')
                ], 200);
            }

            $user = User::where('email', $email)->first();
            if(!$user) {
                //This user is not present. Got to check if the unsuspended flag is raised or not
                $user = User::where('email', $email.'_suspended')->first();
                if(!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This user does not exist.'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'This user is suspended. Please unsuspend this user to proceed with further actions!'
                    ], 200);
                }
            }
            
            $usertype = $request->user_type;
            $user->user_type = $usertype;
            if($user->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'User Updated successfully'
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
        return UserHelper::deleteUser($request);
    }

    /**
     * suspend user endpoint
     * @param : email
     * @return json 
     */
    public function suspendUser(Request $request) {
        return UserHelper::suspendUser($request);
    }

    /**
     * unsuspend user endpoint
     * @param : email
     * @return json 
     */
    public function unsuspendUser(Request $request) {
        return UserHelper::unsuspendUser($request);
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
