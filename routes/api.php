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

Route::any('/search_api','SearchController@search_api');
Route::post('/oldest_registration_date','SearchController@getOldestDate');


Route::group(['prefix' => 'v1'], function() {
    Route::any('create-user', ['uses' => 'Api\UserManagementController@createUser', 'as' => 'createUser']);
    Route::any('edit-user', ['uses' => 'Api\UserManagementController@editUser', 'as' => 'editUser']);
    Route::any('delete-user', ['uses' => 'Api\UserManagementController@deleteUser', 'as' => 'deleteUser']);
    Route::any('suspend-user', ['uses' => 'Api\UserManagementController@suspendUser', 'as' => 'suspendUser']);
    Route::any('unsuspend-user', ['uses' => 'Api\UserManagementController@unsuspendUser', 'as' => 'unsuspendUser']);
    Route::get('all-suspended-users', ['uses' => 'Api\UserManagementController@allSuspendedUser', 'as' => 'allSuspendedUser']);

    // Fetch all users from affiliates and return with user_type
    // Strictly for affiliates
    Route::post('users-data', ['uses' => 'Api\UserManagementController@usersData']);
    Route::post('csv-download', ['uses' => 'SearchController@downloadCsv']);
});

Route::group(['prefix' => 'stripe', 'namespace' => 'Stripe'], function() {
    /**
     * Listens customer.subscription.updated
     * Listens customer.subscription.deleted
     * Listens customer.subscription.trial_will_end
     * from stripe webhooks
     */
    Route::any('customer-subscription-updated', ['uses' => 'StripeWebhooksController@customerSubscriptionUpdated', 'as' => 'customerSubscriptionDeleted']);

    /**
     * Listens customer.invoice.payment_failed from stripe
     * from stripe webhooks
     */
    Route::any('customer-invoice-payment_failed', ['uses' => 'StripeWebhooksController@customerInvoicePaymentFailed', 'as' => 'customerInvoicePaymentFailed']);

    /**
     * Listens customer.deleted from stripe
     * from stripe webhooks
     */
    Route::any('customer-deleted', ['uses' => 'StripeWebhooksController@customerDeleted', 'as' => 'customerDeleted']);
});