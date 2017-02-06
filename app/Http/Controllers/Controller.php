<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use \App\Area;
use \App\AreaCode;
use DB;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    // public $Area_state 			= array();
    // public $Area_major_city 		= array();

    // public $Area_codes_primary_city = array();
    // public $Area_codes_county 		= array();
    // public $Area_codes_carrier_name = array();
    // public $Area_codes_number_type	= array();


    // public function __construct()
    // {
    // 	//from area table
    // 	$this->Area_state 		=   \Config::get('phonevalidate.Area_state');
    // 	$this->Area_major_city 	=   \Config::get('phonevalidate.Area_major_city');


    // 	//from area_codes_table
    // 	$this->Area_codes_primary_city = \Config::get('phonevalidate.Area_codes_primary_city');
    // 	$this->Area_codes_county 	   = \Config::get('phonevalidate.Area_codes_county');
    // 	$this->Area_codes_carrier_name = \Config::get('phonevalidate.Area_codes_carrier_name');
    // 	$this->Area_codes_number_type  = \Config::get('phonevalidate.Area_codes_number_type');
    	
    	
    // }
}
