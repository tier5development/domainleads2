<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Area;
use \App\AreaCode;
USE \App\Lead;
use DB;
use Carbon\Carbon;


class ImportExport extends Controller
{

// use \App\Area;
// use \App\AreaCode;

// return[7

//   'dummy'            => 1,
//   'Area_state'        => Area::pluck('state','prefix')->toArray(),
//   'Area_major_city'       => Area::pluck('major_city','prefix')->toArray(),
//   'Area_codes_primary_city' => AreaCode::pluck('primary_city','prefix')->toArray(),
//   'Area_codes_county'     => AreaCode::pluck('county','prefix')->toArray(),
//   'Area_codes_carrier_name' => AreaCode::pluck('company','prefix')->toArray(),
//   'Area_codes_number_type'  => AreaCode::pluck('usage','prefix')->toArray(),

// ];



  public $Area_state = array();
  public $Area_major_city = array();
  public $Area_codes_primary_city = array();
  public $Area_codes_county = array();
  public $Area_codes_carrier_name = array();
  public $Area_codes_number_type = array();


  private function create()
  {
      $this->Area_state               = Area::pluck('state','prefix')->toArray();
      $this->Area_major_city          = Area::pluck('major_city','prefix')->toArray();
      $this->Area_codes_primary_city  = AreaCode::pluck('primary_city','prefix')->toArray();
      $this->Area_codes_county        = AreaCode::pluck('county','prefix')->toArray();
      $this->Area_codes_carrier_name  = AreaCode::pluck('company','prefix')->toArray();
      $this->Area_codes_number_type   = AreaCode::pluck('usage','prefix')->toArray();
  } 

   public function importExport()
  {
    return view('importExport');
  }



  public function validate_phone_query_builder($num , $registrant_email,$i , $created_at , $updated_at)
  {
  		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
      $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

  		$str = '';
  		try
      {
  			if($num != '')
	  		{
	  			$no = explode('.',$num);

	  			if(isset($no[1]))
		  			$arr = ($this->validateUSPhoneNumber($no[1]));
		  		else
		  			$arr = ($this->validateUSPhoneNumber($no[0]));
		  		if($arr['http_code'] == 200)
		  		{
		  			$str = "NULL , '"
		  					.$arr['phone_number']
		  					."','".str_replace($search, $replace, $arr['validation_status'])
		  					."','".str_replace($search, $replace, $arr['state'])
		  					."','".str_replace($search, $replace, $arr['major_city'])
		  					."','".str_replace($search, $replace, $arr['primary_city'])
		  					."','".str_replace($search, $replace, $arr['county'])
		  					."','".str_replace($search, $replace, $arr['carrier_name'])
		  					."','".str_replace($search, $replace, $arr['number_type'])
                ."','".$created_at
                ."','".$updated_at
		  					."','".str_replace($search, $replace, $registrant_email)."'";
		  		}
	  		}
  		}
  		catch(\Exception $e)
  		{
  			dd($i , $num ,$no);
  		}
  		
	     
  		return $str;
  		
  		
  }


  public function make_query($low , $high , $record, $created_at, $updated_at)
	{
	 	$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    	$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
	 	$str = '';


	 	if($high - $low == 0)
	 	{
	 		$rec = str_replace($search, $replace, $record[$low]);
	 		$d_ext = explode("." , $rec);
          	$ext = $d_ext[sizeof($d_ext)-1];
          	$str .= "NULL,'".$rec."','".$ext."','0',NULL,NULL,'".str_replace($search, $replace, $record[17])."'";

          	//dd($str);
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

		  			if($i == 18)
		  			{
		  				$str  .= "'".$rec."' , 'yes' ," ;
		  			}
		  			else
		  			{
		  				$str  .= "'".$rec."'," ;
		  			}
		  			
		  		}
		  			
		  		else
		  		{
		  			$str  .= "'".$rec."','".$created_at."' , '".$updated_at."'";

		  			if($low != 10)
			  		{
			  			//do nothing
			  		
			  			$str .= ",'".str_replace($search, $replace, $record[1])."'";
			  		}
            else{
              $str .= ",0";
            }
		  		}
		 	}
	 	}
	 	return $str;			
	}

  private function execute_batch_query($leads_head    ,$LEADS
                        ,$valid_phone_head            ,$VALID_PHONE
                        ,$each_domains_head           ,$EACH_DOMAINS
                        ,$domains_info_head           ,$DOMAINS_INFO
                        ,$domains_administrative_head , $DOMAINS_ADMINISTRATIVE
                        ,$domains_technical_head      , $DOMIANS_TECHNICAL
                        ,$domains_billing_head        , $DOMIANS_BILLING
                        ,$domains_nameserver_head     , $DOMIANS_NAMESERVER
                        ,$domains_status_head         , $DOMAINS_STATUS)

  {

    try
    {
        $q_leads    = "REPLACE `leads` ". $leads_head. " VALUES ".$LEADS;

        if($VALID_PHONE != '')
        { 

          $len_ = strlen($VALID_PHONE);
          if($VALID_PHONE[$len_ -1] == ",")
          {
            $VALID_PHONE[$len_ -1] = " ";
          }


          $q_valid_phone  = "INSERT IGNORE `valid_phone` ".$valid_phone_head." VALUES ".$VALID_PHONE;
        }

        $q_each_domains = "REPLACE `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;

        $q_domains_info = "REPLACE `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
          
        $q_domains_administrative = "REPLACE `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
        $q_domains_technical = "REPLACE `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
        $q_domains_billing = "REPLACE `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
        $q_domains_nameserver = "REPLACE `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
        $q_domains_status = "REPLACE `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


          
          

          DB::statement($q_leads);

          if(isset($q_valid_phone))
          {
            
              DB::statement($q_valid_phone);
          }
          DB::statement($q_each_domains);
          DB::statement($q_domains_info);
          DB::statement($q_domains_administrative);
          DB::statement($q_domains_technical);
          DB::statement($q_domains_billing);
          DB::statement($q_domains_nameserver);
          DB::statement($q_domains_status);
    }
    catch(\Exception $e)
    {
      \Log::info($e);
      dd($e);
    }
    
  }

  	public function importExcel(Request $request)
  	{

  		  $this->create();
        //dd($this->Area_state);

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



      	$header = fgetcsv($file); // get the head row of csv file
      	$length = count($header); // get the count of columns in it


      	//dd($header);
      	

	      $valid_phone_head = "(`id` , `phone_number` , `validation_status` , `state` , `major_city` , `primary_city` ,`county` , `carrier_name` , `number_type` , `created_at`,`updated_at`,`registrant_email`)"  ; //dynamically created

      	//10 - 19 goes to leads table
        //heads for leads table for mysql entry
        $leads_head = "(`id`,`registrant_name`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_validated`,`registrant_fax`,`created_at`,`updated_at`,`unlocked_num`)" ;


        //record 1 goes to each_domains table
        $each_domains_head = "(`id`,`domain_name`,`domain_ext`,`unlocked_num`,`created_at` , `updated_at`,`registrant_email`)";


        //from record 2 to 9 goes to domains_info table
        $domains_info_head = "(`id`,`query_time`,`domains_create_date`,`domains_update_date`,`expiry_date`,`domain_registrar_id`,`domain_registrar_name`,`domain_registrar_whois`,`domain_registrar_url`,`created_at`,`updated_at`,`domain_name`)";


        //20-29 goes to domains administrative
        $domains_administrative_head = "(`id` , `administrative_name`,`administrative_company`,`administrative_address`,`administrative_city`,
  		`administrative_state`,`administrative_zip` , `administrative_country`, `administrative_email` , `administrative_phone`, `administrative_fax` , `created_at`,`updated_at`,`domain_name`)";


    		//30-39 goes to domains_technical table
    	  $domains_technical_head = "(`id` , `technical_name`,`technical_company`,`technical_address`,`technical_city`,`technical_state`,`technical_zip`,`technical_country`,`technical_email`,`technical_phone`,`technical_fax`,`created_at`,`updated_at`,`domain_name`)";

    
      		//40-49 goes to domains_billing
    	  $domains_billing_head = "(`id`,`billing_name`,`billing_company`,`billing_address`,`billing_city` , `billing_state`,`billing_zip`,`billing_country`,`billing_email`,`billing_phone`,`billing_fax` , `created_at`,`updated_at`,`domain_name`)";

    	  // 50-53 goes to nameserver
    	  $domains_nameserver_head = "(`id`,`name_server_1`,`name_server_2`,`name_server_3`,`name_server_4`,`created_at`,`updated_at`,`domain_name`)";

    	  // 54-57 goes to domainstatus
    	  $domains_status_head = "(`id`,`domain_status_1`,`domain_status_2`,`domain_status_3`,`domain_status_4`,`created_at`,`updated_at`, `domain_name`)";


        $each_domains 			= '';
        $domains_info 			= '';
        $leads 					= '';
        $valid_phone 				= '';
        $domains_administrative 	= '';
        $domains_technical 		= '';
        $domains_billing 			= ''; 
        $domains_nameserver 		= '';
        $domains_status 			= '';

        $EACH_DOMAINS 			= '';
        $DOMAINS_INFO 			= '';
        $LEADS 					= '';
        $VALID_PHONE 				= '';
        $DOMAINS_ADMINISTRATIVE 	= '';
        $DOMIANS_TECHNICAL 		= '';
        $DOMIANS_BILLING 			= '';
        $DOMIANS_NAMESERVER 		  = '';
        $DOMAINS_STATUS 			   = '';

        $BATCH  = 30000; // to insert 30000 data at 1 go 

      
        $counter = 0;
        while(true)
        {
            $row = fgetcsv($file);
            $counter++ ;
            
            if($row)
            {
            		$domain_name = str_replace($search, $replace, $row[1]);
            		if($LEADS != '') $LEADS .=',';
                if($DOMAINS_STATUS != '') $DOMAINS_STATUS .=',';
                if($DOMIANS_NAMESERVER != '') $DOMIANS_NAMESERVER .=',';
                if($DOMIANS_BILLING != '') $DOMIANS_BILLING .=',';
                if($DOMIANS_TECHNICAL != '') $DOMIANS_TECHNICAL .=',';
                if($DOMAINS_ADMINISTRATIVE != '') $DOMAINS_ADMINISTRATIVE .=',';
                if($DOMAINS_INFO != '') $DOMAINS_INFO .=',';
                if($EACH_DOMAINS != '') $EACH_DOMAINS .=',';
            		if($VALID_PHONE !='' && $VALID_PHONE[strlen($VALID_PHONE)-1] != ',' ) 
                    $VALID_PHONE = $VALID_PHONE .','; 

      		      $leads = '';
      			    $valid_phone = '';
                $each_domains = '';
      			    $domains_info = '';
      			    $domains_administrative = '';
      			    $domains_technical = '';
      			    $domains_billing = '' ; 
      			    $domains_nameserver = '';
      			    $domains_status = '';



                $created_at = str_replace($search, $replace, Carbon::now());
                $updated_at = str_replace($search, $replace, Carbon::now());

  			       
               $leads 		  			= $this->make_query(10 , 19 , $row ,$created_at,$updated_at);

               $rg_em =  str_replace($search, $replace, $row[17]);
  			       $valid_phone = $this->validate_phone_query_builder($row[18],$rg_em,$counter,$created_at,$updated_at);
  			       $each_domains 			= $this->make_query(1 , 1 ,   $row,$created_at,$updated_at);
  			       $domains_info 			= $this->make_query(2 , 9 ,   $row,$created_at,$updated_at);
  			       $domains_administrative = $this->make_query(20 , 29 , $row,$created_at,$updated_at);
  			       $domains_technical  	= $this->make_query(30 , 39 , $row,$created_at,$updated_at);
  			       $domains_billing 		= $this->make_query(40 , 49 , $row,$created_at,$updated_at);
  			       $domains_nameserver 	= $this->make_query(50 , 53 , $row,$created_at,$updated_at);
  			       $domains_status 		= $this->make_query(54 , 57 , $row,$created_at,$updated_at);
            	  	


  			       $LEADS .= 		'('.$leads.')';

  			       if($valid_phone != '') 
  			    		$VALID_PHONE .= "(".$valid_phone.")";

        	  	$EACH_DOMAINS .= '('.$each_domains.')';
  		      	$DOMAINS_INFO .= '(' . $domains_info . ')';
  		      	$DOMAINS_ADMINISTRATIVE .= '('.$domains_administrative.')'; 
  		      	$DOMIANS_TECHNICAL .= '('.$domains_technical.')';
  		      	$DOMIANS_BILLING .= '('.$domains_billing.')';
  		      	$DOMIANS_NAMESERVER .= '('.$domains_nameserver.')';
  		      	$DOMAINS_STATUS .= '('.$domains_status .')';

  			      // 	if($counter == 4)
  			    		// dd($VALID_PHONE);


      		      	if($counter%$BATCH == 0)
      		      	{
      		      		

                    $st = microtime(true);
      		      		$this->execute_batch_query($leads_head    ,$LEADS
                            ,$valid_phone_head            ,$VALID_PHONE
                            ,$each_domains_head           ,$EACH_DOMAINS
                            ,$domains_info_head           ,$DOMAINS_INFO
                            ,$domains_administrative_head , $DOMAINS_ADMINISTRATIVE
                            ,$domains_technical_head      , $DOMIANS_TECHNICAL
                            ,$domains_billing_head        , $DOMIANS_BILLING
                            ,$domains_nameserver_head     , $DOMIANS_NAMESERVER
                            ,$domains_status_head         , $DOMAINS_STATUS);
                    $ed = microtime(true)-$st;

                    echo('time = '.$ed.'<br>');

      		      		$LEADS 					= '';
      		      		$EACH_DOMAINS 			= '';
      			      	$DOMAINS_INFO 			= '';
      			      	$DOMAINS_ADMINISTRATIVE = ''; 
      			      	$DOMIANS_TECHNICAL 		= '';
      			      	$DOMIANS_BILLING 		= '';
      			      	$DOMIANS_NAMESERVER 	= '';
      			      	$DOMAINS_STATUS 		= '';

      			      	//dd('first_batch_complete');
                    
      		      	}
            	}
            	else
            	{
            		if($counter % $BATCH != 0)
            		{
                  $st = microtime(true);
                  $this->execute_batch_query($leads_head    ,$LEADS
                        ,$valid_phone_head            ,$VALID_PHONE
                        ,$each_domains_head           ,$EACH_DOMAINS
                        ,$domains_info_head           ,$DOMAINS_INFO
                        ,$domains_administrative_head , $DOMAINS_ADMINISTRATIVE
                        ,$domains_technical_head      , $DOMIANS_TECHNICAL
                        ,$domains_billing_head        , $DOMIANS_BILLING
                        ,$domains_nameserver_head     , $DOMIANS_NAMESERVER
                        ,$domains_status_head         , $DOMAINS_STATUS);
                  $ed = microtime(true)-$st;

                  echo('time = '.$ed . '<br>');

                  

            		}
            		break;
          	}
             
      	}

      	//$this->validate_ph_no(); // validate all ph numbers from leads table

      $end = microtime(true) - $start;
      echo('TOTAL TIME: ' . $end . " seconds");

      \Log::info('TOTAL TIME: ' . $end . " seconds");

        
     
  
  }


 

  public function checknum($num)
  {
  	dd ($this->validateUSPhoneNumber($num));	
  }

  private function validateUSPhoneNumber($ph)
    {
        $unmaskedPhoneNumber = preg_replace('/[\s()+-]+/', null, $ph);
        $phoneNumberLength = strlen($unmaskedPhoneNumber);
        if ($phoneNumberLength === 10) 
        {
            // $validationPayload = $this->validateAreaCode($unmaskedPhoneNumber, false);
            // return response()->json($validationPayload);

            return ($this->validateAreaCode($unmaskedPhoneNumber, false));
        } 
        elseif ($phoneNumberLength === 11) 
        {
            if ((int)substr($unmaskedPhoneNumber, 0, 1) === 1) 
            {
                // $validationPayload = $this->validateAreaCode(substr($unmaskedPhoneNumber, 1, 10), true);
                // return response()->json($validationPayload);

            	return ($this->validateAreaCode(substr($unmaskedPhoneNumber, 1, 10), true));

            } 
            else
            {
                

                return [
                    "http_code" => 404,
                    "validation_status" => "invalid",
                    "validation_message" => "This phone number does not belongs to US."
                ];

                
            }
        } 
        else 
        {
          
            return [
                "http_code" => 404,
                "validation_status" => "invalid",
                "validation_message" => "This phone number is not in valid format."
            ];
        }
    }
    private function validateAreaCode($phoneNumber, $isdPrefix)
    {
        $areaPrefix = substr($phoneNumber, 0, 3);
        $areaIdentifier = substr($phoneNumber, 0, 6);
        //$validateByAereaPrefix = Area::where('prefix', $areaPrefix)->first();



        //$validateByAereaPrefix = $this->Area_[$areaPrefix]; //<+++++++++ changed code 
        
        if (isset($this->Area_state[$areaPrefix]))
        {
        	
            if(isset($this->Area_codes_primary_city[$areaIdentifier]))
            {
            	
                $actualPhoneNumber = (($isdPrefix === true) ? "+1" : null ). $phoneNumber;
                return [
                		"http_code" => 200,
                    "validation_status" => "valid",
                		"phone_number" => $actualPhoneNumber,
                		"state"        => !isset($this->Area_state[$areaPrefix]) ? null : ucwords(trim($this->Area_state[$areaPrefix])),
                		"major_city"   => !isset($this->Area_major_city[$areaPrefix]) ? null : ucwords(trim($this->Area_major_city[$areaPrefix])),
                		"primary_city" => ucwords(trim($this->Area_codes_primary_city[$areaIdentifier])),
                		"county"       => ucwords(trim($this->Area_codes_county[$areaIdentifier])),
                		"carrier_name" => ucwords(trim($this->Area_codes_carrier_name[$areaIdentifier])),
                		"number_type"  => ucwords(trim($this->Area_codes_number_type[$areaIdentifier]))
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

            //return null;
        }
    }



}
