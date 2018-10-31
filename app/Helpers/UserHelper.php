<?php

namespace App\Helpers;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\User;
use \Carbon\Carbon, Hash, Validator;
use App\Helpers\UserHelper;
class UserHelper {

    public static function editUser(Request $request) {
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

    public static function createUser(Request $request) {
        try {
            //dd('here');
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
                if(!is_numeric($request->user_type) || $request->user_type < 1 || $request->user_type > 3) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid user type provided, user types should be in between 1 and 3'
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

    public static function suspendUser(Request $request) {
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

            $user->email .= '_suspended';
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'User suspended successfully!',
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    public static function unsuspendUser(Request $request) {
        try {
            
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'required|email|max:255']);
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
                    'message' => 'No such user or user is already unsuspended!'
                ], 200);
            }

            $user->email = $email;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'User unsuspended successfully!',
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    public static function suspendOrUnsuspendUser(Request $request, $strict = false) {
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