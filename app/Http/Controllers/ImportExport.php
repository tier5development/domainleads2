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

  	public function make_query($low , $high , $record , $domain_name)
	 {
	 	$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    	$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
	 	$str = '';


	 	if($high - $low == 0)
	 	{
	 		$rec = str_replace($search, $replace, $record[$low]);
	 		$d_ext = explode("." , $rec);
          	$ext = $d_ext[sizeof($d_ext)-1];
          	$str .= "NULL,'".$rec."','".$ext."','0',NULL,NULL";
	 	}
	 	else
	 	{
	 		for($i = $low ; $i<= $high ; $i++)
		 	{
		 		$rec = str_replace($search, $replace, $record[$i]);
		 		if($i == $low)
		  			$str .= "NULL , '".$rec."' ,";
		  			
		  		else if($i != $high)
		  		{
		  			if($i == 18 ) //condition to validate num
		  			{
		  				$ph = explode('.',$rec);
		  				if($ph[0] == 1)
		  				{
		  					$x = validateUSPhoneNumber($ph[1]);
		  					dd($x);
		  				}
		  			}
		  			else
		  			{
		  				$x = '';
		  			}
		  			$str  .= "'".$rec."' ,'".$x."'," ;
		  		}
		  			
		  		else
		  			$str  .= "'".$rec. "' , '".$domain_name."'";
		 	}
	 	}
	 	return $str;			
	 }

  	public function importExcel(Request $request)
  	{
  		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    	$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
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
      $each_domains_head = "(`id`,`domain_name` , `domain_ext` , `unlocked_num` , `created_at` , `updated_at`)";


      //from record 2 to 9 goes to domains_info table

      $domains_info_head = "(`id`,`query_time`,`create_date`,`update_date`,`expiry_date`,`domain_registrar_id`,`domain_registrar_name`,`domain_registrar_whois`,`domain_registrar_url`,`domain_name`)" ;


      //10 - 19 goes to leads table
      //heads for leads table for mysql entry
      $leads_head = "(`id`,`registrant_name`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_type`,`registrant_fax`,`domain_name`)" ;


      //20-29 goes to domains administrative
      $domains_administrative_head = "(`id` , `administrative_name`,`administrative_company`,`administrative_address`,`administrative_city`,
		`administrative_state`,`administrative_zip` , `administrative_country`, `administrative_email` , `administrative_phone`, `administrative_fax` , `domain_name`)";


		//30-39 goes to domains_technical table
	  $domains_technical_head = "(`id` , `technical_name`,`technical_company`,`technical_address`,`technical_city`,`technical_state`,`technical_zip`,`technical_country`,`technical_email`,`technical_phone`,`technical_fax` , `domain_name`)";

  
  		//40-49 goes to domains_billing
	  $domains_billing_head = "(`id`,`billing_name`,`billing_company`,`billing_address`,`billing_city` , `billing_state`,`billing_zip`,`billing_country`,`billing_email`,`billing_phone`,`billing_fax` , `domain_name`)";

	  // 50-53 goes to nameserver
	  $domains_nameserver_head = "(`id`,`name_server_1`,`name_server_2`,`name_server_3`,`name_server_4`,`domain_name`)";

	  // 54-57 goes to domainstatus
	  $domains_status_head = "(`id`,`domain_status_1`,`domain_status_2`,`domain_status_3`,`domain_status_4`,`domain_name`)";


      $each_domains 			= '';
      $domains_info 			= '';
      $leads 					= '';
      $domains_administrative 	= '';
      $domains_technical 		= '';
      $domains_billing 			= ''; 
      $domains_nameserver 		= '';
      $domains_status 			= '';

      $EACH_DOMAINS 			= '';
      $DOMAINS_INFO 			= '';
      $LEADS 					= '';
      $DOMAINS_ADMINISTRATIVE 	= '';
      $DOMIANS_TECHNICAL 		= '';
      $DOMIANS_BILLING 			= '';
      $DOMIANS_NAMESERVER 		= '';
      $DOMAINS_STATUS 			= '';

      $BATCH  = 30000; // to insert 30000 data at 1 go 

      //str_replace($search, $replace,$h)
      $counter = 0;
      while(true)
      {
          $row = fgetcsv($file);

          $counter++ ;
          //$domain_namei = date("d/m/y")."::".date('h:m:s').'::'.microtime(true)."::".$counter;



          if($row)
          {
          		$domain_name = str_replace($search, $replace, $row[1]);

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


			    $each_domains 			= $this->make_query(1 , 1 , $row , $domain_name);
			    $domains_info 			= $this->make_query(2 , 9 , $row , $domain_name);
			    $leads 		  			= $this->make_query(10 , 19 , $row , $domain_name);
			    $domains_administrative = $this->make_query(20 , 29 , $row , $domain_name);
			    $domains_technical  	= $this->make_query(30 , 39 , $row , $domain_name);
			    $domains_billing 		= $this->make_query(40 , 49 , $row , $domain_name);
			    $domains_nameserver 	= $this->make_query(50 , 53 , $row , $domain_name);
			    $domains_status 		= $this->make_query(54 , 57 , $row , $domain_name);
          	  	


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
		      		
		      		$q_each_domains = "REPLACE INTO `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;
		      		$q_domains_info = "REPLACE INTO `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
		      		$q_leads		= "REPLACE INTO `leads` ". $leads_head. " VALUES ".$LEADS;
		      		$q_domains_administrative = "REPLACE INTO `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
		      		$q_domains_technical = "REPLACE INTO `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
		      		$q_domains_billing = "REPLACE INTO `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
		      		$q_domains_nameserver = "REPLACE INTO `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
		      		$q_domains_status = "REPLACE INTO `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


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

          			
          			$q_each_domains = "REPLACE `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;
					$q_domains_info = "REPLACE `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
		      		$q_leads		= "REPLACE `leads` ". $leads_head. " VALUES ".$LEADS;
		      		$q_domains_administrative = "REPLACE `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
		      		$q_domains_technical = "REPLACE `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
		      		$q_domains_billing = "REPLACE `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
		      		$q_domains_nameserver = "REPLACE `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
		      		$q_domains_status = "REPLACE `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


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

      //Domain::where('domain_name', $remember_domain_name)->first();
          	



        
     
  
  }


  private function validateUSPhoneNumber ($ph)
    {
        $unmaskedPhoneNumber = preg_replace('/[\s()+-]+/', null, $ph);
        $phoneNumberLength = strlen($unmaskedPhoneNumber);
        if ($phoneNumberLength === 10) 
        {
            $validationPayload = $this->validateAreaCode($unmaskedPhoneNumber, false);
            return response()->json($validationPayload);
        } 
        elseif ($phoneNumberLength === 11) 
        {
            if ((int)substr($unmaskedPhoneNumber, 0, 1) === 1) 
            {
                $validationPayload = $this->validateAreaCode(substr($unmaskedPhoneNumber, 1, 10), true);
                return response()->json($validationPayload);
            } 
            else 
            {
                return response()->json([
                    "http_code" => 404,
                    "validation_status" => "invalid",
                    "validation_message" => "This phone number does not belongs to US."
                ]);
            }
        } 
        else 
        {
            return response()->json([
                "http_code" => 404,
                "validation_status" => "invalid",
                "validation_message" => "This phone number is not in valid format."
            ]);
        }
    }
    private function validateAreaCode($phoneNumber, $isdPrefix)
    {
        $areaPrefix = substr($phoneNumber, 0, 3);
        $areaIdentifier = substr($phoneNumber, 0, 6);
        $validateByAereaPrefix = Area::where('prefix', $areaPrefix)->first();
        
        if ($validateByAereaPrefix) 
        {
            $validateByAreaIdentifier = AreaCode::where('prefix', $areaIdentifier)->first();
            
            if ($validateByAreaIdentifier) 
            {
                $actualPhoneNumber = (($isdPrefix === true) ? "+1" : null ). $phoneNumber;
                return [
                    	"http_code" => 200,
                    	"validation_status" => "valid",
                    	"validation_message" => $actualPhoneNumber . " is a valid US phone number.",
                    	"phone_number_details" => [
                        "phone_number" => $actualPhoneNumber,
                        "state" => ($validateByAreaIdentifier->area == null) ? null : ucwords(trim($validateByAreaIdentifier->area->state)),
                        "major_city" => ($validateByAreaIdentifier->area == null) ? null : ucwords(trim($validateByAreaIdentifier->area->major_city)),
                        "primary_city" => ucwords(trim($validateByAreaIdentifier->primary_city)),
                        "county" => ucwords(trim($validateByAreaIdentifier->county)),
                        "carrier_name" => ucwords(trim($validateByAreaIdentifier->company)),
                        "number_type" => ucwords(trim($validateByAreaIdentifier->usage))
                    ]
                ];
            } 
            else 
            {
                return [
                    "http_code" => 404,
                    "validation_status" => "invalid",
                    "validation_message" => $areaIdentifier . " is an invalid US area identifier."
                ];
            }
        } 
        else 
        {
            return [
                "http_code" => 404,
                "validation_status" => "invalid",
                "validation_message" => $areaPrefix . " is an invalid US area prefix."
            ];
        }
    }



}
