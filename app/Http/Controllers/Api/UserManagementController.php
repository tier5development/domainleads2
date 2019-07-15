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
        $request['is_hooked'] = '1';
        return $this->responseJsonDecoder(UserHelper::createUser($request));
    }

    public function editUser(Request $request) {
        return $this->responseJsonDecoder(UserHelper::editUser($request));
    }

    /**
     * delete user endpoint
     * @param : email
     * @return json 
     */
    public function deleteUser(Request $request) {
        return $this->responseJsonDecoder(UserHelper::deleteUser($request));
    }

    /**
     * suspend user endpoint
     * @param : email
     * @return json 
     */
    public function suspendUser(Request $request) {
        return $this->responseJsonDecoder(UserHelper::suspendUser($request));
    }

    /**
     * unsuspend user endpoint
     * @param : email
     * @return json 
     */
    public function unsuspendUser(Request $request) {
        return $this->responseJsonDecoder(UserHelper::unsuspendUser($request));
    }

    /**
     * gets list of all-suspended-user
     * @param : email
     * @return json 
     */
    public function allSuspendedUser() {
        $users = User::where('suspended', '1')->select('name','email')->get();
        return response()->json(['status' => true, 'total' => $users->count() ,'users' => $users], 200);
    }

    private function responseJsonDecoder($responseJson) {
        return response()->json(["data" => json_decode($responseJson->content(), true)]);
    }

    /**
     * affiliates gives all the domainleads users listed emails here
     * we give as a response all the users with their users plan they ar currently in
     */
    public function usersData(Request $request) {
        try {
            $data = $request->all();
            $arr = [];
            $idMap = [];
            foreach ($data as $key => $value) {
                $arr[] = $value['email'];
                if(!isset($idMap[$value['email']])) {
                    $idMap[$value['email']] = $value["_id"];
                }
            }
            $returnData = User::select("email", "user_type", "suspended")->whereIn("email", $arr)->get();
            $retArr = [];
            foreach($returnData as $key => $val) {
                $retArr[] = [
                    "_id" => isset($idMap[$val->email]) ? $idMap[$val->email] : null,
                    "email" => $val->email,
                    "user_type" => $val->user_type
                ];
            }
            return \Response::json(['status' => true, 'data' => $returnData, "err" => null]);
        } catch(\Exception $e) {
            \Log::info("error in usersData [from affiliates] : ", $e->getMessage());
            return \Response::json(['status' => true, 'data' => null, "err" => $e->getMessage()]);
        }
    }
}
