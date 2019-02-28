<?php

namespace App\Helpers;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\User;
use App\LeadUser;
use \Carbon\Carbon, Hash, Validator, Auth;
use App\Helpers\UserHelper;
class UserHelper {

    /**
     * Function called in to get usage matrix data
     * Dependency : This function is used to get the usage data for a particular user. -1 if it is unlimited.
     * @param : none
     * @return : 
     *  status : boolean,
     *  leadsUnlocked : leads unlocked today,
     *  allLeadsUnlocked : all leads unlocked till date,
     *  limit : limitation according to plan, (check settings for better understanding)
     *  session : boolean,
     *  message : Message for user
     */
    public static function getUsageMatrix() {
        try {
            if(Auth::check()) {
                $user = Auth::user();
                $domainsUnlockedToday = LeadUser::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
                $domainsUnlocked = LeadUser::where('user_id', $user->id)->count();
                $limit = -1;

                if($user->user_type <= config('settings.PLAN.L1')) {
                    $limit = config('settings.PLAN.'.$user->user_type)[0];
                }
                
                return ([
                    'status' => true,
                    'leadsUnlocked' => $limit > -1 && $domainsUnlockedToday > $limit ? $limit : $domainsUnlockedToday,
                    'allLeadsUnlocked' => $domainsUnlocked,
                    'limit' => $limit,
                    'session' => true,
                    'message' => 'Success'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'leadsUnlocked' => null,
                    'allLeadsUnlocked' => null,
                    'limit' => null,
                    'session' =>false,
                    'message' => 'Error : '.$e->getMesage().' Line : '.$e->getLine()
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'leadsUnlocked' => null,
                'allLeadsUnlocked' => null,
                'limit' => null,
                'session' => null,
                'message' => 'Error : '.$e->getMesage().' Line : '.$e->getLine()
            ]);
        }
    }

    /**
     * Function to edit user 
     * Dependency : Called by the api from affiliates as well as admin panel
     * @param : request 
     *  email : user email,
     *  user_type : plan type of user (check config/settings and users table for better understanding)
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     *  email : email of the user
     */
    
    public static function editUser(Request $request) {
        try {
            $email = $request->email;
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255|unique:users,id,'.$email,
                'user_type' => 'required|numeric|integer|between:1,'.(config('settings.PLAN.L1') + 1)
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
            // $user->base_type = $usertype;
            if($user->save()) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'User Updated successfully',
                    'email'     => $user->email
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
     * Function to create user 
     * Dependency : Called by the api from affiliates as well as admin panel
     * @param : request 
     *  email : user email,
     *  affiliate_id : affiliate who is responsible for this user
     *  name : user name
     *  user_type : optional default 1 (check config/settings and users table for better understanding)
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     *  email : email of the user
     */
    public static function createUser(Request $request) {
        try {
            $email          =   $request->email;
            $affiliateId    =   $request->affiliate_id;
            $name           =   $request->name;
            $validator      =   Validator::make($request->all(), ['email' => 'required|email|max:255']);

            if($validator->fails()) {
                return response()->json([
                    'status'    =>  false,
                    'message'   =>  $validator->errors()->first('email')
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
                if(!is_numeric($request->user_type) || $request->user_type < 1 || $request->user_type > config('settings.PLAN.L1') + 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid user type provided, user types should be in between 1 and '.(config('settings.PLAN.L1') + 1)
                    ], 200);
                } else {
                    $usertype = (int)$request->user_type;
                }
            }

            $newUser = new User();
            $newUser->name      = strlen(trim($name)) > 0 
                                ? $name
                                : explode('@',$email)[0];
            $newUser->email     = $email;
            $newUser->password  = Hash::make(123456);
            $newUser->user_type = $usertype;
            if(strlen(trim($affiliateId)) > 0) {
                $newUser->affiliate_id = $affiliateId;
                $newUser->user_type = $usertype;
            }
            $newUser->membership_status = 1;
            if(strlen($affiliateId) > 0) {
                $newUser->base_type = $usertype;
            }
            $newUser->affiliate_id = $affiliateId;

            if($newUser->save()) {
                return response()->json([
                    'status'    =>  true,
                    'message'   =>  'User Created successfully',
                    'email'     =>  $newUser->email
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
     * Function to delete user 
     * Dependency : Called by the api from affiliates as well as admin panel
     * @param : request 
     *  id : user id (only if comming from our admin panel),
     *  email : if request comes from affiliates platform
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     */
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

    /**
     * Function to suspend user 
     * Dependency : Called by the api from affiliates as well as admin panel
     * @param : request 
     *  email : user email
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     *  email : user email suspended
     */
    public static function suspendUser(Request $request) {
        try {
            
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'message'   => $validator->errors()->first('email'),
                    'email'     => $email
                ], 200);
            }
            
            $user = User::where('email', $email)->first();
            if(!$user) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'This user may have been deleted!',
                    'email'     => $email
                ], 200);
            }
            if($user->suspended == '1') {
                return response()->json([
                    'status'    => false,
                    'message'   => 'This user is already suspended!',
                    'email'     => $user->email
                ], 200);
            }
            $user->suspended = '1';
            $user->save();
            return response()->json([
                'status'    => true,
                'message'   => 'User suspended successfully!',
                'email'     => $user->email
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * Function to unsuspend user 
     * Dependency : Called by the api from affiliates as well as admin panel
     * @param : request 
     *  email : user email
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     *  email : user email unsuspended
     */
    public static function unsuspendUser(Request $request) {
        try {
            
            $email = $request->email;
            $validator = Validator::make($request->all(), ['email' => 'required|email|max:255']);
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('email'),
                    'email' => $email
                ], 200);
            }
            
            $user = User::where('email', $email)->first();
            if(!$user) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'This user is not present or may have been deleted!',
                    'email'     => $email
                ], 200);
            }
            if($user->suspended == '0') {
                return response()->json([
                    'status'    => false,
                    'message'   => 'This user is already unsuspended!',
                    'email'     =>  $email
                ], 200);
            }
            $user->suspended = '0';
            $user->save();
            return response()->json([
                'status'    => true,
                'message'   => 'User unsuspended successfully!',
                'email'     => $user->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }

    /**
     * Hybrid function which is called from our platform to suspend unsespended users and vice versa.
     * @param : request 
     *  email : user email
     * @return
     *  status : boolean, 
     *  message : user friendly message,
     *  email : user email unsuspended
     */
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
                $user = User::where('email', $email)->first();
                if(!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No such user or user is already suspended!'
                    ], 200);
                }
            }

            $user->suspended = (string)abs(1-$user->suspended);
            $user->save();
            return response()->json([
                'status'    => true,
                'message'   => $user->suspended == 1 ? 'User suspended successfully!' : 'User unsuspended successfully!',
                'email'     => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine()
            ], 200);
        }
    }
}

?>