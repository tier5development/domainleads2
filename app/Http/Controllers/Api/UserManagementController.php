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
        return UserHelper::deleteUser($request);
    }

    /**
     * suspend user endpoint
     * @param : email
     * @return json 
     */
    public function suspendUser(Request $request) {
        return UserHelper::suspendOrUnsuspendUser($request);
    }

    /**
     * unsuspend user endpoint
     * @param : email
     * @return json 
     */
    public function unsuspendUser(Request $request) {
        return UserHelper::suspendOrUnsuspendUser($request);
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
