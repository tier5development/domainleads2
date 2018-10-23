<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/search_api','SearchController@search_api');
Route::post('/oldest_registration_date','SearchController@getOldestDate');

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function() {
    Route::any('create-user', ['uses' => 'UserManagementController@createUser', 'as' => 'createUser']);
    Route::any('delete-user', ['uses' => 'UserManagementController@deleteUser', 'as' => 'deleteUser']);
    Route::any('suspend-user', ['uses' => 'UserManagementController@suspendUser', 'as' => 'suspendUser']);
    Route::any('unsuspend-user', ['uses' => 'UserManagementController@unsuspendUser', 'as' => 'unsuspendUser']);
    Route::get('all-suspended-users', ['uses' => 'UserManagementController@allSuspendedUser', 'as' => 'allSuspendedUser']);
});