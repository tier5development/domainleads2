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
	$date = '2017-02-06';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://domainleads2.dev/importExeclfromCron/'.$date);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $head = curl_exec($ch); 

    \Log::info($head);
    //echo('here');
    $x = json_decode($head,true);
   	dd($x['status']);

});

Route::get('/wb',function(){
    $x = \App\Wordpress_env::all();
});

Route::get('/aa',function(){
    $host = 'domainleads.io'; 
    $port = 80; 
    $waitTimeoutInSeconds = 1; 
    if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
        $ip = gethostbyname('www.example.com');

       echo( $ip.'<br>');
       echo(env('TR5IP')); 



    } else {
       echo("It didn't work"); 
    } 
    fclose($fp);
});

//Route::get('/checkWordpressStatus','Maintainance@checkWordpressStatus');



	Route::get('/importExeclfromCron/{date}',['uses'=>'ImportExport@importExeclfromCron',
		'as'=>'importExeclfromCron']);
    
	Route::post('/signme','AccountController@signme' );

	Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains']);

   	Route::group(['middleware' => 'auth'],function(){

	Route::get('importExport', 'ImportExport@importExport');

	Route::post('/importExcel', 'ImportExport@importExcel'); // new version of import exel

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



 });


Route::post('login', 'AccountController@login');
Route::get('logout', 'AccountController@logout');


