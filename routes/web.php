<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// use App\LeadUser;
// use Zipper as Zipper;
// use App\Helpers\StripeHelper;
// use App\StripeDetails;
use App\User;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

Route::get('tstrt', function() {
    event(new App\Events\UsageInfo());
    dd('emitted');
    // $plan = 1;
    // dd(config('settings.PLAN.PUBLISHABLE.'.$plan)[1]);
    // \Stripe\Stripe::setApiKey("sk_test_DNWnAEwDLv6BD7Z6E2X1sWBc");

    // $x = \Stripe\Plan::retrieve('dl-tier1');
    // dd($x);

    // dd(\Carbon\Carbon::now()->format('Y-m-d'));
    // $user = User::where('stripe_customer_id', 'cus_EdxoKkhcLgdqWp')->first();
    // dd($user);
    $client = new Client(); //GuzzleHttp\Client
    $result = $client->post('http://192.168.1.33:8080/hooks/sales', [
        'form_params' => [
                "product_name" 		 		 => "DL",
                "ammount"  	 		 	 	 => 100, 
                "payment_type" 		 		 => 1,
                "trial_period" 		 		 => 0,
                "date_registered"  			 => "2018-03-01",
                "affiliateId" 		    	 => "1005199996",
                "email"  					 => "sail41@email.com"
        ]
    ])->getBody()->getContents();
    $res = str_replace("\n", "", $result);
    $res = json_decode($res);
    // dd($res);
    dd($res->payload->saleId);
    dd(json_decode(json_encode(str_replace("\n", "", $result), true), true));
});


Route::get('support', ['uses' => 'PublicContentController@supportPage', 'as' => 'supportPage']);
Route::get('privacy-policy', ['uses' => 'PublicContentController@privacyPolicy', 'as' => 'privacyPolicy']);
Route::get('terms-of-use', ['uses' => 'PublicContentController@termsOfUse', 'as' => 'termsOfUse']);


Route::get('/login', ['uses' => 'AccountController@loginPage', 'as' => 'loginPage']);
Route::get('/signup', ['uses' => 'AccountController@signupPage', 'as' => 'signupPage']);
Route::post('/signup', ['uses' => 'AccountController@signupPost', 'as' => 'signupPost']);
Route::get('/update_metadata_today/{date}',['uses'=>'SearchController@update_metadata_today']);
Route::get('/forgot-password', ['uses' => 'AccountController@forgotPassword', 'as' => 'forgotPassword']);
Route::post('forgot-password', ['uses' => 'AccountController@forgotPasswordPost', 'as' => 'forgotPasswordPost']);
Route::get('/reset-password/{e_token}', ['uses' => 'AccountController@resetPasswordExternalPage', 'as' => 'resetPasswordExternalPage']);
Route::post('/reset-password/{e_token}', ['uses' => 'AccountController@resetPasswordExternalPost', 'as' => 'resetPasswordExternalPost']);
Route::get('/verify-email', ['uses' => 'AccountController@verifyEmail', 'as' => 'verifyEmail']);

Route::any('upload-old-leads', ['uses' => 'ImportExport@uploadOldLeads', 'as' =>'uploadOldLeads']);
Route::get('/sss','Maintainance@async_domain');
Route::get('/' , ['uses'=>'AccountController@home' , 'as'=>'home']);
Route::get('/checknum/{num}' , ['uses' => 'ImportExport@checknum']);
Route::get('/verify_domains','Maintainance@verify_domains');
//Route::get('/checkWordpressStatus','Maintainance@checkWordpressStatus');

    Route::get('/fill_csv_table_default',['uses'=>'ImportExport@fill_csv_table_default','as'=>'fill_csv_table_default']);
	Route::get('/importExeclfromCron/{date}',['uses'=>'ImportExport@importExeclfromCron', 'as'=>'importExeclfromCron']);
    Route::get('/autoImportExcelFile', 'ImportExport@autoImportExcelFile');
	Route::post('/signme','AccountController@signme' );
    Route::post('/download_csv_single_page',['uses' => 'SearchController@download_csv_single_page', 'as' => 'download_csv_single_page']);

    // Route::group(['middleware' => 'pendingSubscription'], function() {
    //     Route::group(['prefix' => 'failed-subscription'], function() {
    //         Route::get('/pay-now', ['uses' => 'AccountController@failedSubscription', 'as' => 'failedSubscription']);
    //         Route::post('/pay-now', ['uses' => 'AccountController@failedSubscription', 'as' => 'failedSubscriptionPost']);
    //     });
    // });

    Route::group(['middleware' => 'subscribedUserGroup'], function() {
        Route::group(['prefix' => 'failed-subscription'], function() {
            Route::get('/pay-now', ['uses' => 'AccountController@failedSubscription', 'as' => 'failedSubscription']);
            Route::post('/pay-now', ['uses' => 'AccountController@failedSubscriptionPost', 'as' => 'failedSubscriptionPost']);
        });
    });

    Route::group(['middleware' => ['unsuspendedUserGroup', 'subscribedUserGroup']], function() {
        // Route::get('welcome', ['uses' => 'AccountController@checkFirstVisit', 'as' => 'checkFirstVisit']);
        // Route::post('logout-user', ['uses'=>'AccountController@logoutUserPost', 'as' => 'logoutUserPost']);

        Route::group(['prefix' => 'profile'], function() {
            Route::get('/', ['uses' => 'AccountController@profile', 'as' => 'profile']);
            Route::get('change-password', ['uses' => 'AccountController@changePassword', 'as' => 'changePassword']);
            Route::post('change-password', ['uses' => 'AccountController@changePasswordPost', 'as' => 'changePasswordPost']);
            Route::get('payment-info', ['uses' => 'AccountController@paymentInformation', 'as' => 'paymentInformation']);
            Route::post('update-card-details', ['uses' => 'AccountController@updateCardDetails', 'as' => 'updateCardDetails']);
            
            Route::get('membership', ['uses' => 'AccountController@showMembershipPage', 'as' => 'showMembershipPage']);
            Route::post('update-card-details-pay', ['uses' => 'AccountController@updateCardDetailsAndSubscribe', 'as' => 'updateCardDetailsAndSubscribe']);
            Route::post('change-plan', ['uses' => 'AccountController@upgradeOrDowngradePlan', 'as'=>'upgradeOrDowngradePlan']);

            Route::get('cancel-membership', ['uses' => 'AccountController@cancelMembership', 'as'=>'cancelMembership']);
            Route::post('cancel-membership', ['uses' => 'AccountController@cancelMembershipPost', 'as'=>'cancelMembershipPost']);
            Route::group(['middleware' => 'adminGroup'], function() {
                Route::get('update-payment-keys', ['uses' => 'AccountController@updatePaymentKeys', 'as' => 'updatePaymentKeys']);
                Route::post('update-payment-keys', ['uses' => 'AccountController@updatePaymentKeysPost', 'as' => 'updatePaymentKeysPost']);
            });
        });

        Route::post('/uploadImage', ['uses' => 'UserController@uploadImage', 'as' => 'uploadImage']);
        Route::post('/ajax_search_paginated',['uses'=>'SearchController@ajax_search_paginated','as'=>'ajax_search_paginated']);
        Route::post('/ajax_search_paginated_subadmin', ['uses' => 'SearchController@ajax_search_paginated_subadmin', 'as' => 'ajax_search_paginated_subadmin']);
        Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains', 'as' => 'viewDomainsOfUnlockedLeed']);
        Route::post('unlockFromLeads', ['uses' => 'SearchController@unlockFromLeads', 'as' => 'unlockFromLeads']);
        Route::post('updateUserInfo', ['uses' => 'AccountController@updateUserInfo', 'as' => 'updateUserInfo']);
        Route::post('editUser', ['uses' => 'AccountController@editUser', 'as' => 'editUser']);
        Route::post('createUser', ['uses' => 'AccountController@createUser', 'as' => 'createUser']);
        Route::any('userlist',['uses'=>'AccountController@UserList','as'=>'UserList']);
        Route::post('delete-user', ['uses' => 'AccountController@deleteUser', 'as' => 'deleteUserPost']);
        Route::post('suspendOrUnsuspendUser', ['uses' => 'AccountController@suspendOrUnsuspendUser', 'as' => 'suspendOrUnsuspendUser']);
        Route::post('totalLeadsUnlockedToday', ['uses' => 'SearchController@totalLeadsUnlockedToday', 'as' => 'totalLeadsUnlockedToday']);
        Route::post('download-unlocked-leads', ['uses' => 'UserController@downloadUnlockedLeads', 'as' => 'downloadUnlockedLeads']);
        Route::get('unlocked-leads', ['uses' => 'UserController@myUnlockedLeads', 'as' => 'myUnlockedLeads']);
        Route::post('unlocked-leads', ['uses' => 'UserController@myUnlockedLeads', 'as' => 'myUnlockedLeadsPost']);
        Route::get('importExport', ['uses' => 'ImportExport@importExport', 'as' => 'importExport']);
        Route::post('importBulkZip', ['uses' => 'ImportExport@importBulkZip', 'as' => 'importBulkZip']);
    	Route::post('/importExcel', 'ImportExport@importExcel')->name('import_Excel'); // new version of import exel
        Route::get('downloadExcel', 'SearchController@downloadExcel');
        Route::get('downloadExcel2', 'SearchController@downloadExcel2');
    	Route::any('/search', ['uses'=>'SearchController@search','as'=>'search']);
        Route::post('createWordpressForDomain' , ['uses'=>'SearchController@createWordpressForDomain','as'=>'createWordpressForDomain']);
        Route::post('storechkboxvariable' , ['uses'=>'SearchController@storechkboxvariable','as'=>'storechkboxvariable']);
        Route::post('removeChkedEmailfromSession' , ['uses'=>'SearchController@removeChkedEmailfromSession','as'=>'removeChkedEmailfromSession']);
    	Route::post('/unlockleed' , ['uses'=>'SearchController@unlockleed','as'=>'unlockleed']);
        Route::get('/manage',['uses'=>'Maintainance@manage','as'=>'manage']);
        Route::post('search_paginated',['uses'=>'SearchController@search_paginated','as'=>'search_paginated']);
        Route::any('/import-excel', 'ImportExport@importExcelNew')->name('importExcelNew');
        Route::get('/downloads', ['uses'=>'UserController@showDownloadIndex','as'=>'userDownloads']);
 });

Route::post('login', ['uses' => 'AccountController@login', 'as' => 'loginPost']);
Route::get('logout', ['uses' => 'AccountController@logout', 'as' => 'logout']);

Route::get('/404',['uses'=>'Maintainance@notfound_404','as'=>'404']);
Route::get('/500',['uses'=>'Maintainance@notfound_500','as'=>'500']);

//=============================new import===================================
//importExcelNew

