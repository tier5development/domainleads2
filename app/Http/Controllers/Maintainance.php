<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Area;
use \App\AreaCode;
use \App\Lead;
use \App\EachDomain;
use \App\LeadUser;
use \App\ValidatedPhone;
use \App\Wordpress_env;
use DB;
use Carbon\Carbon;
use Zipper;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Maintainance extends Controller
{
  //   public function checkWordpressStatus()
  //   {
  //   	$wp = Wordpress_env::where('status',1);


		// $IP = env('TR5IP');
  //       $changed_domain = array();
  //   	foreach($wp->get() as $each)
  //   	{
  //   		$ip = gethostbyname($each->domain_name);
  //   		if($ip == $IP)
  //   			$changed_domain[$each->domain_name] = 2;
  //   	}
  //   	/* FORMAT */

  //   	// UPDATE `wordpress` 
  //   	// 	SET status = CASE domain_name
  //   	// 		WHEN 'domain_name1' THEN value1
  //   	// 		WHEN 'domain_name2' THEN value2
  //   	// 	END
  //   	// WHERE domain_name IN ('domain_name1','domain_name2')

  //   	$wp_header = "UPDATE `wordpress` SET status = CASE domain_name ";
  //   	$wp_query_body = "";
  //   	$query_domain_names = "";
  //   	foreach($changed_domain as $key=>$val)
  //   	{
  //   		$wp_query_body .= "WHEN '".$key."' THEN ".$val; 

  //   		if($query_domain_names != '') $query_domain_names .=",";

  //           $query_domain_names .= "'". $key ."'";
  //   	}
  //   	$query_domain_names = "(" . $query_domain_names .")";
  //   	$wp_query_tail = " END WHERE domain_name IN ".$query_domain_names;

  //   	$query = $wp_header.$wp_query_body.$wp_query_tail;

  //   	if($wp_query_body == "")
  //   		return \Response::json(array('status'=>'no row to change'));
  //   	else
  //   	{
  //   		try{
	 //    		DB::statement($query);
	 //    		return \Response::json(array('status'=>'db query executed'
	 //    								,'rows'=>sizeof($changed_domain)));
	 //    	}
	 //    	catch(\Excepttion $e)
	 //    	{
	 //    		return \Response::json(array('status'=>'db query executed'
	 //    								,'rows'=>sizeof($changed_domain)
	 //    								,'message'=>$e->getMessage()));
	 //    	}
  //   	}
  //   }

	public function domain_verification()
	{
		try{
			$url = 'http://abidingbb1rake.men/';
     	$client = new Client(); //GuzzleHttp\Client
     	$client->setDefaultOption('verify', true);
        $result = $client->get($url);
        //$domain_data = json_decode($result->getBody()->getContents(), true);
        //dd($domain_data->getStatusCode());
        dd($result->getStatusCode());
		}
		catch(\Exception $e)
		{
			dd($e->getMessage());
		}
		


        // Create a client with a base URI
	}

	
}
