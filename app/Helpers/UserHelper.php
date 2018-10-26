<?php

namespace App\Helpers;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator;
use App\Helpers\UserHelper;
class UserHelper {

    public static function deleteUser(Request $request) {
        try {
            
            if($request->has('id')) {
                if(!\Auth::check()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are not authenticated!'
                    ], 200);
                }

                $id = $request->id;
                $validator = Validator::make($request->all(), ['id' => 'integer']);
                if($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first('id')
                    ], 200);
                }
                $deleteInfo = User::find($id)->delete();
            } else {
                $email = $request->email;
                $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
                if($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first('email')
                    ], 200);
                }
                $deleteInfo = User::where('email', $email)->orWhere('email', $email.'_suspended')->delete();    
            }
            
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

    public static function suspendOrUnsuspendUser(Request $request) {
        try {
            if($request->has('id')) {
                if(!\Auth::check()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are not authenticated!'
                    ], 200);
                }
                $id = $request->id;
                $validator = Validator::make($request->all(), ['id' => 'unique:users,id,'.$request->id]);
                if($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first('id')
                    ], 200);
                }
                $user = User::find($id);
            } else {
                $email = $request->email;
                $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
                if($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first('email')
                    ], 200);
                }
                $user = User::where('email', $email)->orWhere('email', $email.'_suspended')->first();
                if(!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No such user or user is already suspended!'
                    ], 200);
                }
            }

            if(strpos($user->email, '_suspended') === false) {
				$user->email .= '_suspended';
				$user->save();
				return response()->json([
					'status' => true,
					'message' => 'User suspended successfully!',
					'email' => $user->email
				]);
			} else {
				$user->email = str_replace('_suspended', '', $user->email);
				$user->save();
				return response()->json([
					'status' => true,
					'message' => 'User unsuspended successfully!',
					'email' => $user->email
				]);
			}

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }
}

?>