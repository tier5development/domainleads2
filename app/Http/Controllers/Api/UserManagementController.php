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
        return UserHelper::createUser($request);
    }

    public function editUser(Request $request) {
        return UserHelper::editUser($request);
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
