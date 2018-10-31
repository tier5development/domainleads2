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

use App\LeadUser;

Route::get('/login', ['uses' => 'AccountController@loginPage', 'as' => 'loginPage']);

Route::get('/update_metadata_today/{date}',['uses'=>'SearchController@update_metadata_today']);

Route::post('/ajax_search_paginated',['uses'=>'SearchController@ajax_search_paginated','as'=>'ajax_search_paginated']);

Route::post('/ajax_search_paginated_subadmin', ['uses' => 'SearchController@ajax_search_paginated_subadmin', 'as' => 'ajax_search_paginated_subadmin']);

Route::get('/aaa',function(){
    // $v = custom_curl_errors();
    // dd($v);

    // $x = LeadUser::updateOrCreate([
    //         ['registrant_email' => 'support@dropcatch.comop', 'domain_name' => 'afinarte.com'],
    //         ['registrant_fname' => 'adsj', 'registrant_country' => 'Some Country']
    // ]);
    // dd($x);
});

Route::get('/aaa',function(){
    dd(strpos('zim','kazimierzbar.com'));
    dd(generateDateRange(null,null));
});

Route::get('/sss','Maintainance@async_domain');

Route::get('/' , ['uses'=>'AccountController@home' , 'as'=>'home']);

Route::get('/checknum/{num}' , ['uses' => 'ImportExport@checknum']);

Route::get('/abc' , ['uses'=>'ImportExport@validate_ph_no']);

Route::get('/verify_domains','Maintainance@verify_domains');
Route::get('/tstt','Maintainance@each_domain_verification');


Route::get('/wb',function(){
    $x = \App\Wordpress_env::all();
});

Route::get('/aa',function(){
    $csv_exists = \App\CSV::where('file_name',"2017-02-11_whois-proxies-removed.csv");
    if($csv_exists->first() !== null)
        echo('adiug iug h');
});

//Route::get('/checkWordpressStatus','Maintainance@checkWordpressStatus');

    Route::get('/fill_csv_table_default',['uses'=>'ImportExport@fill_csv_table_default','as'=>'fill_csv_table_default']);

	Route::get('/importExeclfromCron/{date}',['uses'=>'ImportExport@importExeclfromCron',
		'as'=>'importExeclfromCron']);

    Route::get('/autoImportExcelFile', 'ImportExport@autoImportExcelFile');
    
	Route::post('/signme','AccountController@signme' );

	Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains', 'as' => 'viewDomainsOfUnlockedLeed']);

    Route::post('/download_csv_single_page','SearchController@download_csv_single_page');

       
    Route::post('assignLeads', ['uses' => 'SearchController@assignLeads', 'as' => 'assignLeads']);
    
    Route::group(['middleware' => 'auth'],function(){
        
        Route::post('editUser', ['uses' => 'AccountController@editUser', 'as' => 'editUser']);

        Route::post('createUser', ['uses' => 'AccountController@createUser', 'as' => 'createUser']);

        Route::any('userlist',['uses'=>'AccountController@UserList','as'=>'UserList']);

        Route::post('delete-user', ['uses' => 'AccountController@deleteUser', 'as' => 'deleteUserPost']);

        Route::post('suspendOrUnsuspendUser', ['uses' => 'AccountController@suspendOrUnsuspendUser', 'as' => 'suspendOrUnsuspendUser']);

        Route::post('totalLeadsUnlockedToday', ['uses' => 'SearchController@totalLeadsUnlockedToday', 'as' => 'totalLeadsUnlockedToday']);

        Route::post('download-unlocked-leads', ['uses' => 'UserController@downloadUnlockedLeads', 'as' => 'downloadUnlockedLeads']);
        
        Route::get('unlocked-leads', ['uses' => 'UserController@myUnlockedLeads', 'as' => 'myUnlockedLeads']);

        Route::post('unlocked-leads', ['uses' => 'UserController@myUnlockedLeads', 'as' => 'myUnlockedLeadsPost']);

    	Route::get('importExport', 'ImportExport@importExport');

    	Route::post('/importExcel', 'ImportExport@importExcel')->name('import_Excel'); // new version of import exel

        Route::get('downloadExcel', 'SearchController@downloadExcel');

        Route::get('downloadExcel2', 'SearchController@downloadExcel2');
        //Route::get('downloadExcel' , ['uses'=>'SearchController@downloadExcel','as'=>'downloadExcel']);

    	//Route::get('/search', ['uses'=>'SearchController@search','as'=>'search']);

    	Route::any('/search' , ['uses'=>'SearchController@search','as'=>'search']);
        

        Route::post('createWordpressForDomain' , ['uses'=>'SearchController@createWordpressForDomain','as'=>'createWordpressForDomain']);
        Route::post('storechkboxvariable' , ['uses'=>'SearchController@storechkboxvariable','as'=>'storechkboxvariable']);
        Route::post('removeChkedEmailfromSession' , ['uses'=>'SearchController@removeChkedEmailfromSession','as'=>'removeChkedEmailfromSession']);

    	Route::get('/myLeads/{id}',['uses'=>'SearchController@myLeads','as'=>'myLeads']);
    	Route::post('/unlockleed' , ['uses'=>'SearchController@unlockleed','as'=>'unlockleed']);

        Route::get('/manage',['uses'=>'Maintainance@manage','as'=>'manage']);

 });




Route::post('search_paginated',['uses'=>'SearchController@search_paginated','as'=>'search_paginated']);


Route::post('login', ['uses' => 'AccountController@login', 'as' => 'loginPost']);
Route::get('logout', 'AccountController@logout');

Route::get('/404',['uses'=>'Maintainance@notfound_404','as'=>'404']);
Route::get('/500',['uses'=>'Maintainance@notfound_500','as'=>'500']);

Route::any('regredirect',['uses'=>'AccountController@regredirect','as'=>'regredirect']);

//=============================new import===================================
//importExcelNew
Route::any('/import-excel', 'ImportExport@importExcelNew')->name('importExcelNew');
