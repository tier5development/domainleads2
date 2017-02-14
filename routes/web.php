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
	echo($_ENV['DB_DATABASE']);
});


   Route::post('/signme','AccountController@signme' );


	Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains']);

   Route::group(['middleware' => 'auth'],function(){

	Route::get('importExport', 'ImportExport@importExport');

	Route::post('/importExcel', 'ImportExport@importExcel'); // new version of import exel

     Route::get('downloadExcel', 'SearchController@downloadExcel');
    //Route::get('downloadExcel' , ['uses'=>'SearchController@downloadExcel','as'=>'downloadExcel']);

	//Route::get('/search', ['uses'=>'SearchController@search','as'=>'search']);
	Route::any('/search' , ['uses'=>'SearchController@search','as'=>'search']);
    Route::post('chkWebsiteForDomain' , ['uses'=>'SearchController@chkWebsiteForDomain','as'=>'chkWebsiteForDomain']);
    Route::post('storechkboxvariable' , ['uses'=>'SearchController@storechkboxvariable','as'=>'storechkboxvariable']);
	Route::get('/myLeads/{id}',['uses'=>'SearchController@myLeads','as'=>'myLeads']);
	Route::post('/unlockleed' , ['uses'=>'SearchController@unlockleed','as'=>'unlockleed']);



 });


Route::post('login', 'AccountController@login');
Route::get('logout', 'AccountController@logout');
