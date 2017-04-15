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

Route::get('/aaaaa',function(){
    dd(DB::select(DB::raw("SHOW KEYS FROM leads WHERE Key_name='leads_registrant_country_index'")));
});

Route::get('/aaaa',function(){

    $x = DB::select(DB::raw('SELECT leads from search_metadata where id = 1'));
    dd(gzinflate($x[0]->leads));

    //$last_csv_insert_time = DB::select(DB::raw('SELECT MAX(created_at) as created FROM `csv_record` where id = 53'));
    //dd($last_csv_insert_time[0]->created);

    //echo(\Carbon\carbon::now());

// //$string = str_repeat('1234567890'.implode('',range('a','z')),48800);
// $string='';
// for($i=0;$i<10000000;$i++) $string .= $i.",";
// //dd($string);
// echo strlen($string).'<br/>';//1756800 bytes


// $last_size = strlen($string);
// $compressed = $string;
// $ratio = 0;
// while(true)
// {
//     if($ratio > 10) break;

    
//     $min_size   = strlen(gzdeflate($compressed, 9));
    
//     if($min_size >= $last_size) 
//         break;

//     $compressed = gzdeflate($compressed, 9);
//     $last_size = $min_size;
//     $ratio++;

//     echo $last_size.'<br/>';

    
// }

// $s = $compressed;
// while($ratio--)
// {
//     $s = gzinflate($s);
// }

// dd($s);


//$compressed = gzdeflate($string,  9);
//$compressed = gzdeflate($compressed, 9);
//$compressed = gzdeflate($compressed, 9);
//$compressed = gzdeflate($compressed, 9);
//$compressed = gzdeflate($compressed, 9);
//$compressed = gzdeflate($compressed, 9);
//$compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);
// $compressed = gzdeflate($compressed, 9);


//dd($compressed);
//echo '<br/>'.strlen($compressed).'<br/>';//99 bytes

//echo gzinflate(gzinflate($compressed));

});

Route::post('/ajax_search_paginated',['uses'=>'SearchController@ajax_search_paginated','as'=>'ajax_search_paginated']);

Route::get('/aaa',function(){
    $v = custom_curl_errors();
    dd($v);
});

Route::get('/aaa',function(){
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
    
	Route::post('/signme','AccountController@signme' );

	Route::get('/lead/{email}',['uses'=>'SearchController@lead_domains']);

    Route::post('/download_csv_single_page','SearchController@download_csv_single_page');

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

Route::post('search_paginated',['uses'=>'SearchController@search_paginated','as'=>'search_paginated']);


Route::post('login', 'AccountController@login');
Route::get('logout', 'AccountController@logout');


