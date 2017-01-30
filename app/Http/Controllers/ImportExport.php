<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ImportExport extends Controller
{
   public function importExport()
  {
    return view('importExport');
  }

  	public function make_qquery($low , $high , $record , $hash)
     {
     	$str = '';
     	for($i = $low ; $i<= $high ; $i++)
     	{

     		if($i == $low)
	  			{
	  				 $str .= "NULL , '" . $record[$i] ."' ,";
	  			}
	  			else if($key != $high)
	  			{
	  				 $str  .= "'".$record[$i]."' ," ;
	  			}
	  			else
	  			{
	  				 $str  .= "'".$record[$i]. "' , '".$hash."'";
	  			}


     	}
     				
     }

  public function importExcel(Request $request)
  {
  		
    

  		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    	$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    	$u_hash = array();

      	$start = microtime(true);
      	$upload = $request->file('import_file');
      	$filepath = $upload->getRealPath();

      	$file  = fopen($filepath , 'r');

      	$cnt = 0;

      	ini_set("memory_limit","7G");
      	ini_set('max_execution_time', '0');
      	ini_set('max_input_time', '0');
      	set_time_limit(0);
      	ignore_user_abort(true);  


      	$string;
      	$header = fgetcsv($file); // get the head row of csv file
      	$length = count($header); // get the count of columns in it
      	//dd($header);

      

      //record 1 goes to each_domains table
      $each_domains_head = "(`id` ,`unique_hash`, `domain_name` , `domain_ext` , `unlocked_num` , `created_at` , `updated_at`)";


      //from record 2 to 9 goes to domains_info table

      $domains_info_head = "(`id`,`query_time`,`create_date`,`update_date`,`expiry_date`,`domain_registrar_id`,`domain_registrar_name`,`domain_registrar_whois`,`domain_registrar_url`,`unique_hash`)" ;


      //10 - 19 goes to leads table
      //heads for leads table for mysql entry
      $leads_head = "(`id`,`registrant_name`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`registrant_fax`,`unique_hash`)" ;


      //20-29 goes to domains administrative
      $domains_administrative_head = "(`id` , `administrative_name`,`administrative_company`,`administrative_address`,`administrative_city`,
		`administrative_state`,`administrative_zip` , `administrative_country`, `administrative_email` , `administrative_phone`, `administrative_fax` , `unique_hash`)";


		//30-39 goes to domains_technical table
	  $domains_technical_head = "(`id` , `technical_name`,`technical_company`,`technical_address`,`technical_city`,`technical_state`,`technical_zip`,`technical_country`,`technical_email`,`technical_phone`,`technical_fax` , `unique_hash`)";

  
  		//40-49 goes to domains_billing
	  $domains_billing_head = "(`id`,`billing_name`,`billing_company`,`billing_address`,`billing_city` , `billing_state`,`billing_zip`,`billing_country`,`billing_email`,`billing_phone`,`billing_fax` , `unique_hash`)";

	  $domains_nameserver_head = "(`id`,`name_server_1`,`name_server_2`,`name_server_3`,`name_server_4`,`unique_hash`)";

	  $domains_status_head = "(`id`,`domain_status_1`,`domain_status_2`,`domain_status_3`,`domain_status_4`,`unique_hash`)";

      


      

      

      //user_id is leads id which cannot be dynamic with bulk data

      $each_domains = '';
      $domains_info = '';
      $leads = '';
      $domains_administrative = '';
      $domains_technical = '';
      $domains_billing = '' ; 
      $domains_nameserver = '';
      $domains_status = '';

      $EACH_DOMAINS = '';
      $DOMAINS_INFO = '';
      $LEADS = '';
      $DOMAINS_ADMINISTRATIVE = '';
      $DOMIANS_TECHNICAL = '';
      $DOMIANS_BILLING = '';
      $DOMIANS_NAMESERVER = '';
      $DOMAINS_STATUS = '';

      $BATCH  = 30000; // to insert 10000 data at 1 go 

      //str_replace($search, $replace,$h)
      $counter = 0;
      while(true)
      {
          $row = fgetcsv($file);


          

          //echo($EACH_DOMAINS);

          $counter++ ;
          $hash = date("Y:M:D").":".microtime(true)."::".$counter;

          if($row)
          {
          		

          		if($DOMAINS_STATUS != '') $DOMAINS_STATUS .=',';
		        if($DOMIANS_NAMESERVER != '') $DOMIANS_NAMESERVER .=',';
		        if($DOMIANS_BILLING != '') $DOMIANS_BILLING .=',';
		        if($DOMIANS_TECHNICAL != '') $DOMIANS_TECHNICAL .=',';
		        if($DOMAINS_ADMINISTRATIVE != '') $DOMAINS_ADMINISTRATIVE .=',';
		        if($LEADS != '') $LEADS .=',';
		        if($DOMAINS_INFO != '') $DOMAINS_INFO .=',';
		        if($EACH_DOMAINS != '') $EACH_DOMAINS .=',';

          		$each_domains = '';
			    $domains_info = '';
			    $leads = '';
			    $domains_administrative = '';
			    $domains_technical = '';
			    $domains_billing = '' ; 
			    $domains_nameserver = '';
			    $domains_status = '';
          	  	foreach($row as $key=>$record)
          	  	{
          	  		$val = str_replace($search, $replace, $record);
          	  		if($key == 0)
          	  		{
          	  			continue;
          	  		}
          	  		if($key == 1)
          	  		{
          	  			$d_ext = explode("." , $val);
          	  			$ext = $d_ext[sizeof($d_ext)-1];
          	  			$each_domains .= "NULL,'".$hash."','".$val."','".$ext."','0', NULL , NULL";
          	  			continue;
          	  		}
          	  		else if($key >=2 && $key <= 9)
          	  		{
          	  			

          	  			if($key == 2)
          	  			{
          	  				$domains_info .= "NULL , '".$val."',";
          	  			}
          	  			else if($key != 9) 
          	  			{
          	  				$domains_info .="'".$val ."'," ;
          	  				//echo('in key '.$key."  ");
          	  			}
          	  			else
          	  			{
          	  				$domains_info .= "'".$val . "','".$hash."'" ;
          	  			}

          	  			//echo($domains_info . '<br>');
          	  		}
          	  		else if($key >=10 && $key <= 19)
          	  		{
          	  			//dd(1);
          	  			//dd($domains_info);
          	  			

          	  			if($key == 10) 
          	  			{
          	  				$leads .= "NULL , '" . $val ."',";
          	  			}
          	  			else if($key != 19)
          	  			{
          	  				$leads .= "'".$val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				$leads .="'".$val . "','".$hash."'";
          	  			}
          	  			//echo($leads . '<br>');
          	  		}
          	  		else if($key >=20 && $key <= 29)
          	  		{
          	  			if($key == 20) 
          	  			{
          	  				$domains_administrative .= "NULL , '".$val."' ,";
          	  			}
          	  			else if($key != 29)
          	  			{
          	  				$domains_administrative .= "'". $val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				$domains_administrative .= "'".$val . "' , '".$hash."'";
          	  			}
          	  		}
          	  		else if($key >=30 && $key <= 39)
          	  		{

          	  			if($key == 30) 
          	  			{
          	  				 $domains_technical .= "NULL , '" . $val ."' ,";
          	  			}
          	  			else if($key != 39)
          	  			{
          	  				 $domains_technical .= "'".$val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				 $domains_technical .=  "'".$val . "' , '".$hash."'";
          	  			}

          	  		}
          	  		else if($key >=40 && $key <= 49)
          	  		{
          	  			if($key == 40) 
          	  			{
          	  				 $domains_billing .= "NULL , '" . $val ."' ,";
          	  			}
          	  			else if($key != 49)
          	  			{
          	  				 $domains_billing .= "'".$val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				 $domains_billing .= "'".$val . "' , '".$hash."'";
          	  			}
          	  		}
          	  		else if($key >=50 && $key <= 53)
          	  		{
          	  			if($key == 50) 
          	  			{
          	  				 $domains_nameserver .= "NULL , '" . $val ."' ,";
          	  			}
          	  			else if($key != 53)
          	  			{
          	  				 $domains_nameserver .= "'".$val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				 $domains_nameserver .= "'".$val . "' , '".$hash."'";
          	  			}

          	  		}
          	  		else //condition for 54 to 57
          	  		{
          	  			if($key == 54) 
          	  			{
          	  				 $domains_status .= "NULL , '" . $val ."' ,";
          	  			}
          	  			else if($key != 57)
          	  			{
          	  				 $domains_status  .= "'".$val."' ," ;
          	  			}
          	  			else
          	  			{
          	  				$domains_status  .= "'".$val . "' , '".$hash."'";
          	  			}
          	  		}
          	  	}

      	  		$EACH_DOMAINS .= '('.$each_domains.')';
		      	$DOMAINS_INFO .= '(' . $domains_info . ')';
		      	$LEADS .= 		'('.$leads.')';
		      	$DOMAINS_ADMINISTRATIVE .= '('.$domains_administrative.')'; 
		      	$DOMIANS_TECHNICAL .= '('.$domains_technical.')';
		      	$DOMIANS_BILLING .= '('.$domains_billing.')';
		      	$DOMIANS_NAMESERVER .= '('.$domains_nameserver.')';
		      	$DOMAINS_STATUS .= '('.$domains_status .')';

			      	
		      	if($counter%$BATCH == 0)
		      	{
		      		
		      		$q_each_domains = "INSERT INTO `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;
		      		$q_domains_info = "INSERT INTO `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
		      		$q_leads		= "INSERT INTO `leads` ". $leads_head. " VALUES ".$LEADS;
		      		$q_domains_administrative = "INSERT INTO `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
		      		$q_domains_technical = "INSERT INTO `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
		      		$q_domains_billing = "INSERT INTO `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
		      		$q_domains_nameserver = "INSERT INTO `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
		      		$q_domains_status = "INSERT INTO `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


		      		DB::statement($q_each_domains);
		      		DB::statement($q_domains_info);
		      		DB::statement($q_leads);
		      		DB::statement($q_domains_administrative);
		      		DB::statement($q_domains_technical);
		      		DB::statement($q_domains_billing);
		      		DB::statement($q_domains_nameserver);
		      		DB::statement($q_domains_status);


		      		$EACH_DOMAINS 			= '';
			      	$DOMAINS_INFO 			= '';
			      	$LEADS 					= '';
			      	$DOMAINS_ADMINISTRATIVE = ''; 
			      	$DOMIANS_TECHNICAL 		= '';
			      	$DOMIANS_BILLING 		= '';
			      	$DOMIANS_NAMESERVER 	= '';
			      	$DOMAINS_STATUS 		= '';
		      	}
			      	

          	}
          	else
          	{
          		if($counter % $BATCH != 0)
          		{

          			
          			$q_each_domains = "INSERT INTO `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;
					$q_domains_info = "INSERT INTO `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
		      		$q_leads		= "INSERT INTO `leads` ". $leads_head. " VALUES ".$LEADS;
		      		$q_domains_administrative = "INSERT INTO `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
		      		$q_domains_technical = "INSERT INTO `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
		      		$q_domains_billing = "INSERT INTO `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
		      		$q_domains_nameserver = "INSERT INTO `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
		      		$q_domains_status = "INSERT INTO `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


		      		//dd($q_domains_administrative);

		      		DB::statement($q_each_domains);
		      		DB::statement($q_domains_info);
		      		DB::statement($q_leads);
		      		DB::statement($q_domains_administrative);
		      		DB::statement($q_domains_technical);
		      		DB::statement($q_domains_billing);
		      		DB::statement($q_domains_nameserver);
		      		DB::statement($q_domains_status);
          		}
          		break;

          	}
             

      	}

      	

      $end = microtime(true) - $start;
      echo('TOTAL TIME: ' . $end . " seconds");



      //echo('configuring database ...... <br><br>');

      //Domain::where('unique_hash', $remember_hash)->first();
          	



        
     
  
  }


}
