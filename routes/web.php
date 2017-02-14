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


Route::get('/' , ['uses'=>'AccountController@home' , 'as'=>'home']);

Route::get('/checknum/{num}' , ['uses' => 'ImportExport@checknum']);

Route::get('/abc' , ['uses'=>'ImportExport@validate_ph_no']);

Route::get('/tst' , function()
{
	//$x = \App\Lead::where('registrant_email' , 'drlipman@gmail.com')->get();


	// $domains = \App\EachDomain::take(100)->pluck('registrant_email','domain_name')->toArray();
 //      $l_count = array();
 //      $e_ids = "";
 //      foreach($domains as $key=>$value)
 //      {
 //          $e_ids .= $value.","; 
 //          if(isset($l_count[$value]))
 //            $l_count[$value]++;
 //          else
 //            $l_count[$value]=1;
 //      }

 //      $e_ids = rtrim($e_ids,',');

 //      dd($e_ids);

 //      DB::statement("select * from `leads` where ");
	$x = \App\Lead::pluck('registrant_country','registrant_state')->toArray();

    dd($x);

	//dd($x);
});

	Route::get('/importExeclfromCron/{date}',['uses'=>'ImportExport@importExeclfromCron',
		'as'=>'importExeclfromCron']);


	Route::post('/signme','AccountController@signme' );

	Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains']);

   	Route::group(['middleware' => 'auth'],function(){

	Route::get('importExport', 'ImportExport@importExport');

	Route::post('/importExcel', 'ImportExport@importExcel'); // new version of import exel


	//Route::get('/search', ['uses'=>'SearchController@search','as'=>'search']);
	Route::any('/search' , ['uses'=>'SearchController@search','as'=>'search']);

	Route::get('/myLeads/{id}',['uses'=>'SearchController@myLeads','as'=>'myLeads']);
	Route::post('/unlockleed' , ['uses'=>'SearchController@unlockleed','as'=>'unlockleed']);



 });


Route::post('login', 'AccountController@login');
Route::get('logout', 'AccountController@logout');
