<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Area;
use \App\AreaCode;
USE \App\Lead;
use DB;


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

  public function validate_phone_query_builder($num , $registrant_email,$i)
  {
  		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
      $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

  		$str = '';
  		try{
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
		  					."','".str_replace($search, $replace, $arr['number_type'])."',NULL,NULL,'"
		  					.str_replace($search, $replace, $registrant_email)."'";
		  		}
	  		}
  		}
  		catch(\Exception $e)
  		{
  			dd($i , $num ,$no);
  		}
  		
		
  		
  		return $str;
  		
  		
  }


  	public function make_query($low , $high , $record)
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

              // $no = explode('.',$rec);

              // if(isset($no[1]))
              //   $rec = $no[1];
              // else
              //   $rec = $no[0];

		  				$str  .= "'".$rec."' , 'yes' ," ;
		  			}
		  			else
		  			{
		  				$str  .= "'".$rec."'," ;
		  			}
		  			
		  		}
		  			
		  		else
		  		{
		  			$str  .= "'".$rec. "' , NULL , NULL ";

		  			if($low ==10 )
			  		{
			  			//do nothing
			  		}
			  		else
			  		{
			  			//add domain_name

			  			$str .= ",'".str_replace($search, $replace, $record[1])."'";
			  		}
		  		}
		 	}
	 	}
	 	return $str;			
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
      $leads_head = "(`id`,`registrant_name`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_validated`,`registrant_fax`,`created_at`,`updated_at`)" ;






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


          		if($LEADS != '') $LEADS .=',';


          		if($VALID_PHONE !='' && $VALID_PHONE[strlen($VALID_PHONE)-1] != ',' ) 
                {
                  $VALID_PHONE = $VALID_PHONE .','; 
                }
          		

                if($DOMAINS_STATUS != '') $DOMAINS_STATUS .=',';
    		        if($DOMIANS_NAMESERVER != '') $DOMIANS_NAMESERVER .=',';
    		        if($DOMIANS_BILLING != '') $DOMIANS_BILLING .=',';
    		        if($DOMIANS_TECHNICAL != '') $DOMIANS_TECHNICAL .=',';
    		        if($DOMAINS_ADMINISTRATIVE != '') $DOMAINS_ADMINISTRATIVE .=',';
    		        if($DOMAINS_INFO != '') $DOMAINS_INFO .=',';
    		        if($EACH_DOMAINS != '') $EACH_DOMAINS .=',';



    		      $leads = '';
    			    $valid_phone = '';
              $each_domains = '';
    			    $domains_info = '';
    			    $domains_administrative = '';
    			    $domains_technical = '';
    			    $domains_billing = '' ; 
    			    $domains_nameserver = '';
    			    $domains_status = '';


              $created_at = \Carbon::now();
              $updated_at = \Carbon::now();
			    $leads 		  			= $this->make_query(10 , 19 , $row );

			   $valid_phone = $this->validate_phone_query_builder($row[18] , $row[17],$counter);


			    $each_domains 			= $this->make_query(1 , 1 ,   $row );
			    $domains_info 			= $this->make_query(2 , 9 ,   $row );
			    
			    $domains_administrative = $this->make_query(20 , 29 , $row );
			    $domains_technical  	= $this->make_query(30 , 39 , $row );
			    $domains_billing 		= $this->make_query(40 , 49 , $row );
			    $domains_nameserver 	= $this->make_query(50 , 53 , $row );
			    $domains_status 		= $this->make_query(54 , 57 , $row );
          	  	


			    $LEADS .= 		'('.$leads.')';

			    if($valid_phone != '') 
			    {
			    		$VALID_PHONE .= "(".$valid_phone.")";


			    }



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
		      		
		      		$q_leads		= "REPLACE INTO `leads` ". $leads_head. " VALUES ".$LEADS;

		      		if($VALID_PHONE != '')
          			{ 
                  $len_ = strlen($VALID_PHONE);

                  if($VALID_PHONE[$len_ -1] == ",")
                  {

                    $VALID_PHONE[$len_ -1] = " ";
                  }


          				$q_valid_phone 	= "REPLACE INTO `valid_phone` ".$valid_phone_head." VALUES ".$VALID_PHONE;

          				//dd($q_valid_phone);
          			}

		      		$q_each_domains = "REPLACE INTO `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;
		      		$q_domains_info = "REPLACE INTO `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
		      		
		      		$q_domains_administrative = "REPLACE INTO `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
		      		$q_domains_technical = "REPLACE INTO `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
		      		$q_domains_billing = "REPLACE INTO `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
		      		$q_domains_nameserver = "REPLACE INTO `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
		      		$q_domains_status = "REPLACE INTO `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;

		      		//dd($q_each_domains);


		      		DB::statement($q_leads);

		      		if(isset($q_valid_phone))
		      		{


                try{
                  //dd($q_valid_phone);
                  DB::statement($q_valid_phone);
                }
                catch(\Exception $e)
                {
                  echo('IN real condition'.'<br>');
                  dd($q_valid_phone);
                }


		      			//DB::statement($q_valid_phone);
		      		}

		      		DB::statement($q_each_domains);
		      		DB::statement($q_domains_info);
		      		
		      		DB::statement($q_domains_administrative);
		      		DB::statement($q_domains_technical);
		      		DB::statement($q_domains_billing);
		      		DB::statement($q_domains_nameserver);
		      		DB::statement($q_domains_status);




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

          			$q_leads		= "REPLACE `leads` ". $leads_head. " VALUES ".$LEADS;

          			if($VALID_PHONE != '')
          			{ 

                  $len_ = strlen($VALID_PHONE);
                  if($VALID_PHONE[$len_ -1] == ",")
                  {
                    $VALID_PHONE[$len_ -1] = " ";
                  }


          				$q_valid_phone 	= "REPLACE INTO `valid_phone` ".$valid_phone_head." VALUES ".$VALID_PHONE;
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
                try{
                  //dd($q_valid_phone);
                  DB::statement($q_valid_phone);
                }
                catch(\Exception $e)
                {
                  echo('IN eccape condition'.'<br>');
                  dd($q_valid_phone);
                }
		      			
		      		}

		      		DB::statement($q_each_domains);
		      		DB::statement($q_domains_info);
		      		
		      		DB::statement($q_domains_administrative);
		      		DB::statement($q_domains_technical);
		      		DB::statement($q_domains_billing);
		      		DB::statement($q_domains_nameserver);
		      		DB::statement($q_domains_status);
          		}
          		break;

          	}
             
      	}

      	//$this->validate_ph_no(); // validate all ph numbers from leads table

      $end = microtime(true) - $start;
      echo('TOTAL TIME: ' . $end . " seconds");



      //echo('configuring database ...... <br><br>');

      //Domain::where('domain_name', $remember_domain_name)->first();
          	



        
     
  
  }


  // public function validate_ph_no()
  // {
  //   $leads_head = "(`id`,`registrant_name`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_validated`,`registrant_fax`,`created_at`,`updated_at`)" ;

  //   $valid_phone_head = "(`id` , `phone_number` , `validation_status` , `state` , `major_city` , `primary_city` ,`county` , `carrier_name` , `number_type` , `created_at`,`updated_at`,`registrant_email`)" ;

  //   $leads_body = "";
  //   $validate_phone_body = "";


  //   $L = Lead::where('phone_validated',null)->pluck('registrant_phone','registrant_email')->toArray();
  //   //dd($L);
  //   $validated_no = array();
    
  //   foreach ($L as $key => $value) 
  //   {
  //     $v = $value;

  //     $arr = explode('.',$v);

  //     if(isset($arr[1]))
  //     {
  //       $obj = $this->validateUSPhoneNumber($arr[1]);
  //     }
  //     else
  //     {
  //       $obj = $this->validateUSPhoneNumber($arr[0]);
  //     }

  //     if($obj['http_code'] == 200)
  //     {
  //       $leads_body = ""
  //     }

        

  //   }

  // }

  public function checknum($num)
  {
  	
  	// dd(Config::get('phone_validate.dummy'));
  	dd ($this->validateUSPhoneNumber($num));
  	// dd("here");
  	//$c = \Config::get('phonevalidate.Area_codes_primary_city');
  	//dd($c);
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
                // return response()->json([
                //     "http_code" => 404,
                //     "validation_status" => "invalid",
                //     "validation_message" => "This phone number does not belongs to US."
                // ]);

                return [
                    "http_code" => 404,
                    "validation_status" => "invalid",
                    "validation_message" => "This phone number does not belongs to US."
                ];

                //return null ;
            }
        } 
        else 
        {
            // return response()->json([
            //     "http_code" => 404,
            //     "validation_status" => "invalid",
            //     "validation_message" => "This phone number is not in valid format."
            // ]);

            return [
                "http_code" => 404,
                "validation_status" => "invalid",
                "validation_message" => "This phone number is not in valid format."
            ];

            //return null ;
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
        	//$validateByAereaPrefix = $this->Area_[$areaPrefix] ;

            //$validateByAreaIdentifier = AreaCode::where('prefix', $areaIdentifier)->first();

            //$validateByAreaIdentifier = $this->Area_codes_prefix[$areaIdentifier];
            
            //if ($validateByAreaIdentifier)
            if(isset($this->Area_codes_primary_city[$areaIdentifier]))
            {
            	//$validateByAreaIdentifier = $this->Area_codes[$areaIdentifier];
                $actualPhoneNumber = (($isdPrefix === true) ? "+1" : null ). $phoneNumber;



                // return [
                //     	"http_code" => 200,
                //     	"validation_status" => "valid",
                //     	"validation_message" => $actualPhoneNumber . " is a valid US phone number.",
                //     	"phone_number_details" => [
                //         "phone_number" => $actualPhoneNumber,
                //         "state" => ($validateByAreaIdentifier->area == null) ? null : ucwords(trim($validateByAreaIdentifier->area->state)),
                //         "major_city" => ($validateByAreaIdentifier->area == null) ? null : ucwords(trim($validateByAreaIdentifier->area->major_city)),
                //         "primary_city" => ucwords(trim($validateByAreaIdentifier->primary_city)),
                //         "county" => ucwords(trim($validateByAreaIdentifier->county)),
                //         "carrier_name" => ucwords(trim($validateByAreaIdentifier->company)),
                //         "number_type" => ucwords(trim($validateByAreaIdentifier->usage))
                //     ]
                // ];





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


                //return ucwords(trim($validateByAreaIdentifier->usage));


                //return ucwords(trim($this->Area_codes[$areaIdentifier]));
            } 
            else 
            {
                return [
                    "http_code" => 404,
                    "validation_status" => "invalid",
                    "validation_message" => $areaIdentifier . " is an invalid US area identifier."
                ];


                //return null;
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
