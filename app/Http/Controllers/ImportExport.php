<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Area;
use \App\AreaCode;
use \App\Lead;
use \App\EachDomain;
use \App\LeadUser;
use \App\ValidatedPhone;
use \App\CSV;
use DB;
use Carbon\Carbon;
use Zipper;


class ImportExport extends Controller
{
  public $Area_state = array();
  public $Area_major_city = array();
  public $Area_codes_primary_city = array();
  public $Area_codes_county = array();
  public $Area_codes_carrier_name = array();
  public $Area_codes_number_type = array();
  public $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
  public $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
  public $__leads = array();
  public $__domains = array();

  public $__clipboard = array(); // stores leads which needs to be altered in the table--on basis of domains count

  private function create()
  {

      $this->Area_state               = Area::pluck('state','prefix')->toArray();
      $this->Area_major_city          = Area::pluck('major_city','prefix')->toArray();
      $this->Area_codes_primary_city  = AreaCode::pluck('primary_city','prefix')->toArray();
      $this->Area_codes_county        = AreaCode::pluck('county','prefix')->toArray();
      $this->Area_codes_carrier_name  = AreaCode::pluck('company','prefix')->toArray();
      $this->Area_codes_number_type   = AreaCode::pluck('usage','prefix')->toArray();

      $this->__leads  = Lead::pluck('registrant_email')->toArray();
      $this->__leads  = array_flip($this->__leads);
      $this->__domains= EachDomain::pluck('registrant_email','domain_name')->toArray();

      foreach($this->__leads as $key=>$val)  $this->__leads[$key] = 0;
      //dd($this->__leads);
      foreach($this->__domains as $key=>$val)
      {
        try{
            $this->__leads[$val]++;  
        }
        catch(\Exception $e)
        {
          echo($key.'  '.$val.'<br>');
          dd($e);
        }
      }
  }
  private function destroy()
  {
      $this->Area_state               = null;
      $this->Area_major_city          = null;
      $this->Area_codes_primary_city  = null;
      $this->Area_codes_county        = null;
      $this->Area_codes_carrier_name  = null;
      $this->Area_codes_number_type   = null;
      $this->__leads  = null;
      $this->__leads  = null;
      $this->__domains= null;
  }



    public function importExport()
    {
      try
      {
        if(\Auth::check())
          return view('importExport');
        else
          return redirect('/home');
      }
      catch(\Exception $e)
      {
        dd($e->getMessage());
      }
    }

    public function importExcel(Request $request)
    {
      
     
        //dd('here');
        $start = microtime(true);
        $upload = $request->file('import_file');
        $filepath = $upload->getRealPath();
        $original_file_name = $request->import_file->getClientOriginalName();


        $csv_exists = CSV::where('file_name',$original_file_name);
        if($csv_exists->first() == null)
        {
          $total_leads_before_insertion = Lead::count();
          $total_domains_before_insertion = EachDomain::count();
          $file  = fopen($filepath , 'r');
          $this->insertion_Execl($file);
          fclose($file);
          $leads_inserted   = Lead::count()-$total_leads_before_insertion;
          $domains_inserted = EachDomain::count()-$total_domains_before_insertion;
          $end = microtime(true) - $start;

          $obj = new CSV();
          $obj->file_name          = $original_file_name;
          $obj->leads_inserted    = $leads_inserted;
          $obj->domains_inserted  = $domains_inserted;
          $obj->status            = 2;
          $obj->query_time        = $end;
          $obj->save();

          return \Response::json(array('TOTAL TIME TAKEN:'=>$end." seconds",
                                    'leads_inserted'=>$leads_inserted,
                                    'domains_inserted'=>$domains_inserted,
                                    'status'=>200,
                                    'filename'=>$original_file_name));

        }
        else
        {
            return \Response::json(array('insertion_time'=>'null',
                                           'message'=>'This file is inserted already::'.$original_file_name,
                                           'status'=>500));
        }

        
        //echo('TOTAL TIME TAKEN :: '.$end);

    }

    public function importExeclfromCron($date)
    {   
     
      $user = 't5ilmpnba';
      $pass = '4on9sq6ae8lMRVHCZxp2';  //+++++++ date format :: '2017-01-18';

      $start = microtime(true);
      //dd('kjyfgkg');
      try{

            $csv_exists = CSV::where('file_name',$date."_whois-proxies-removed.csv");
            if($csv_exists->first() == null)
            {
              $dir = getcwd();
              $str = "http://".$user.":".$pass."@download.whoxy.com/newly-registered-whois/whois-proxies-removed/".$date."_whois-proxies-removed.zip";
              $data = file_get_contents($str);
              file_put_contents($dir.'/zip/'.$date.'_whois-proxies-removed.zip',$data);
              Zipper::make($dir.'/zip/'.$date.'_whois-proxies-removed.zip')->extractTo($dir.'/unzip/');

              $filepath = $dir.'/unzip/'.$date.'_whois-proxies-removed.csv';
              $file  = fopen($filepath , 'r');


              $total_leads_before_insertion = Lead::count();
              $total_domains_before_insertion = EachDomain::count();
              $this->insertion_Execl($file);
              fclose($file);
              $leads_inserted   = Lead::count()-$total_leads_before_insertion;
              $domains_inserted = EachDomain::count()-$total_domains_before_insertion;
              $end = microtime(true) - $start;

              $obj = new CSV();
              $obj->file_name           = $date."_whois-proxies-removed.csv";
              $obj->leads_inserted      = $leads_inserted;
              $obj->domains_inserted    = $domains_inserted;
              $obj->status              = 2;
              $obj->query_time          = $end;
              $obj->save();

              unlink($filepath);
              unlink($dir.'/zip/'.$date.'_whois-proxies-removed.zip');

              return \Response::json(array('TOTAL TIME TAKEN:'=>$end." seconds",
                                      'leads_inserted'=>$leads_inserted,
                                      'domains_inserted'=>$domains_inserted,
                                      'status'=>200,
                                      'message'=>'success',
                                      'filename'=>$date."_whois-proxies-removed.csv"));
            }
            else
            {
              return \Response::json(array('insertion_time'=>'null',
                                           'message'=>'This file is inserted already::'.$date."_whois-proxies-removed.csv",
                                           'status'=>500));
            }

            
      }
      catch(\Exception $e)
      {
        $end = microtime(true);
        return \Response::json(array(
            'insertion_time ' => $end,
            'message'         => $e->getMessage(),
            'status'          => 'failure'));
      }
    }

  public function validate_phone_query_builder($num , $registrant_email,$i , $created_at , $updated_at)
  { 
  		

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
		  		if($arr['http_code'] == 200 && ($arr['number_type'] == 'Landline' || $arr['number_type'] == 'Cell Number'))
		  		{
		  			$str = "NULL , '"
		  					.$arr['phone_number']
		  					."','".str_replace($this->search, $this->replace, $arr['validation_status'])
		  					."','".str_replace($this->search, $this->replace, $arr['state'])
		  					."','".str_replace($this->search, $this->replace, $arr['major_city'])
		  					."','".str_replace($this->search, $this->replace, $arr['primary_city'])
		  					."','".str_replace($this->search, $this->replace, $arr['county'])
		  					."','".str_replace($this->search, $this->replace, $arr['carrier_name'])
		  					."','".str_replace($this->search, $this->replace, $arr['number_type'])
                ."','".$created_at
                ."','".$updated_at
		  					."','".str_replace($this->search, $this->replace, $registrant_email)."'";
		  		}
	  		}
  		}
  		catch(\Exception $e)
  		{
  			
        //\Log::info('from :: validate_phone_query_builder :: '.$e->getMessage());
        dd($e);
  		}
  		return $str;
  }


  public function make_query($low , $high , $record, $created_at, $updated_at,$domains_count)
	{
	 	$str = '';
	 	if($high - $low == 0)
	 	{
	 		$rec = str_replace($this->search, $this->replace, $record[$low]);
	 		$d_ext = explode("." , $rec);
          	$ext = $d_ext[sizeof($d_ext)-1];
          	$str .= "NULL,'".$rec."','".$ext."','0',NULL,NULL,'".str_replace($this->search, $this->replace, $record[17])."'";
	 	}
	 	else
	 	{
	 		for($i = $low ; $i<= $high ; $i++)
		 	{
        $x = !isset($record[$i]) ? " " : $record[$i];
		 		$rec = str_replace($this->search, $this->replace, $x);
        $rec = trim($rec);
		 		 if($i == $low)
         {
            if($i == 10)
            {
              $name_ = explode(" ",$rec);
              $fname = isset($name_[0]) ? $name_[0] : " ";
              $lname = isset($name_[1]) ? $name_[1] : " ";
              $str .= "NULL , '".$fname."' , '".$lname."' ,";
            }
            else
            {
              $str .= "NULL , '".$rec."' ,";
            }
         }	
	  		else if($i != $high)
	  		{
	  			if($i == 18)  $str  .= "'".$rec."' , 'yes' ," ;
	  			else          $str  .= "'".$rec."'," ;
	  		}
	  		else
	  		{
	  			$str  .= "'".$rec."','".$created_at."' , '".$updated_at."'";
	  			if($low != 10)
              $str .= ",'".str_replace($this->search, $this->replace, $record[1])."'";
          else
            $str .= ",0,".$domains_count;
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
        DB::statement(DB::raw('RESET QUERY CACHE;'));
        $q_leads    = "REPLACE `leads` ". $leads_head. " VALUES ".$LEADS;

        if($VALID_PHONE != '')
        { 
          $len_ = strlen($VALID_PHONE);
          if($VALID_PHONE[$len_ -1] == ",")
          {
            $VALID_PHONE[$len_ -1] = " ";
          }
          $q_valid_phone  = "REPLACE `valid_phone` ".$valid_phone_head." VALUES ".$VALID_PHONE;
        }

          $q_each_domains = "REPLACE `each_domain` ". $each_domains_head. " VALUES ".$EACH_DOMAINS;

          $q_domains_info = "REPLACE `domains_info` ". $domains_info_head. " VALUES ".$DOMAINS_INFO;
          
          $q_domains_administrative = "REPLACE `domains_administrative` ". $domains_administrative_head. " VALUES ".$DOMAINS_ADMINISTRATIVE;
          $q_domains_technical = "REPLACE `domains_technical` ". $domains_technical_head. " VALUES ".$DOMIANS_TECHNICAL;
          $q_domains_billing = "REPLACE `domains_billing` ". $domains_billing_head. " VALUES ".$DOMIANS_BILLING;
          $q_domains_nameserver = "REPLACE `domains_nameserver` ". $domains_nameserver_head. " VALUES ".$DOMIANS_NAMESERVER;
          $q_domains_status = "REPLACE `domains_status` ". $domains_status_head. " VALUES ".$DOMAINS_STATUS;


          //dd($q_leads);
          DB::statement($q_leads);
          if(isset($q_valid_phone)) DB::statement($q_valid_phone);
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
      //\Log::info('From import export :: while querry executing :: '.$e->getMessage());
      //dd($e->getMessage());
    }
    
  }

    public function set($domain_name,$registrant_email)
    {
 
        if(isset($this->__domains[$domain_name]))
        {
            if($this->__domains[$domain_name] == $registrant_email)
            {
                //do nothing because same record--
            }
            else
            {
                $this->__leads[$this->__domains[$domain_name]] --;
                $this->__clipboard[$this->__domains[$domain_name]] = $this->__leads[$this->__domains[$domain_name]];


                if(isset($this->__leads[$registrant_email]))
                  $this->__leads[$registrant_email]++;
                else
                  $this->__leads[$registrant_email]=1;

                $this->__domains[$domain_name] = $registrant_email;

            }
        }
        else
        {
            if(isset($this->__leads[$registrant_email]))
            {
                $this->__leads[$registrant_email]++;
                $this->__domains[$domain_name] = $registrant_email;
            }
            else
            {
                $this->__domains[$domain_name] = $registrant_email;
                $this->__leads[$registrant_email] = 1;
            }
        }

        return $this->__leads[$registrant_email];
    }

    public function insertion_Execl($file)
    {

        $query_time_array = array();
        $loop_time = array();

        $tm1 = microtime(true);

        ini_set("memory_limit","7G");
        ini_set('max_execution_time', '0');
        ini_set('max_input_time', '0');
        set_time_limit(0);
        ignore_user_abort(true);
        $this->create();
        $start = microtime(true);
        $cnt = 0;
        $header = fgetcsv($file); // get the head row of csv file
        $length = count($header); // get the count of columns in it

        //dd($header);

        $valid_phone_head = "(`id` , `phone_number` , `validation_status` , `state` , `major_city` , `primary_city` ,`county` , `carrier_name` , `number_type` , `created_at`,`updated_at`,`registrant_email`)"  ; //dynamically created

        //10 - 19 goes to leads table
        //heads for leads table for mysql entry
        $leads_head = "(`id`,`registrant_fname`,`registrant_lname`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_validated`,`registrant_fax`,`created_at`,`updated_at`,`unlocked_num`,`domains_count`)" ;

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


        $each_domains       = '';
        $domains_info       = '';
        $leads          = '';
        $valid_phone        = '';
        $domains_administrative   = '';
        $domains_technical    = '';
        $domains_billing      = ''; 
        $domains_nameserver     = '';
        $domains_status       = '';

        $EACH_DOMAINS       = '';
        $DOMAINS_INFO       = '';
        $LEADS          = '';
        $VALID_PHONE        = '';
        $DOMAINS_ADMINISTRATIVE   = '';
        $DOMIANS_TECHNICAL    = '';
        $DOMIANS_BILLING      = '';
        $DOMIANS_NAMESERVER       = '';
        $DOMAINS_STATUS          = '';

        $BATCH  = 15000; // to insert 10000 data at 1 go 

        //array_push($loop_time, microtime(true)-$tm1);
      
        $counter = 0;
        while(true)
        {
            $row = fgetcsv($file);
            $counter++ ;
            
            if($row)
            {

                $domain_name = str_replace($this->search, $this->replace, $row[1]);
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



                $created_at             = str_replace($this->search, $this->replace, Carbon::now());
                $updated_at             = str_replace($this->search, $this->replace, Carbon::now());
                $domains_count          = $this->set($row[1],$row[17]); //-------------
                $leads                  = $this->make_query(10 , 19 , $row ,$created_at,$updated_at
                                                            ,$domains_count);
                $rg_em                  = str_replace($this->search, $this->replace, $row[17]);
                $valid_phone            = $this->validate_phone_query_builder($row[18]
                                                            ,$rg_em,$counter,$created_at,$updated_at);
                $each_domains           = $this->make_query(1 , 1 ,   $row,$created_at,$updated_at,null);
                $domains_info           = $this->make_query(2 , 9 ,   $row,$created_at,$updated_at,null);
                $domains_administrative = $this->make_query(20 , 29 , $row,$created_at,$updated_at,null);
                $domains_technical      = $this->make_query(30 , 39 , $row,$created_at,$updated_at,null);
                $domains_billing        = $this->make_query(40 , 49 , $row,$created_at,$updated_at,null);
                $domains_nameserver     = $this->make_query(50 , 53 , $row,$created_at,$updated_at,null);
                $domains_status         = $this->make_query(54 , 57 , $row,$created_at,$updated_at,null);
                  


                $LEADS .=     '('.$leads.')';
                if($valid_phone != '')  $VALID_PHONE .= "(".$valid_phone.")";
                $EACH_DOMAINS           .= '('.$each_domains.')';
                $DOMAINS_INFO           .= '(' . $domains_info . ')';
                $DOMAINS_ADMINISTRATIVE .= '('.$domains_administrative.')'; 
                $DOMIANS_TECHNICAL      .= '('.$domains_technical.')';
                $DOMIANS_BILLING        .= '('.$domains_billing.')';
                $DOMIANS_NAMESERVER     .= '('.$domains_nameserver.')';
                $DOMAINS_STATUS         .= '('.$domains_status .')';

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
                  array_push($query_time_array, $ed);
                  //dd($query_time_array);
                  //echo ($ed."<br/>");

                  $LEADS                  = '';
                  $VALID_PHONE            = '';
                  $EACH_DOMAINS           = '';
                  $DOMAINS_INFO           = '';
                  $DOMAINS_ADMINISTRATIVE = ''; 
                  $DOMIANS_TECHNICAL      = '';
                  $DOMIANS_BILLING        = '';
                  $DOMIANS_NAMESERVER     = '';
                  $DOMAINS_STATUS         = '';

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
                  array_push($query_time_array, $ed);
                  
                  //echo ($ed."<br/>");
                }
                break;
            }    
        }
      $this->rectify_leads();
      $end = microtime(true) - $start;

      \Log::info('time ==>> ',$query_time_array);

      echo "<pre>";print_r($query_time_array);
      //echo('TOTAL TIME: ' . $end . " seconds");
      //\Log::info('TOTAL TIME: ' . $end . " seconds");
      $this->destroy();
      return;
    }

  public function rectify_leads()
  {
    //rectifies those leads whose domain names are changed but domain counts are not
    //in the process of insertion

      // UPDATE `leads`
      //   SET domains_count = CASE registrant_email
      //       WHEN 'info1@gctld.com' THEN 10
      //       WHEN '616822783@qq.com' THEN 20
      //   END
      // WHERE registrant_email IN ('info1@gctld.com','616822783@qq.com')

    //delete
    //     delete from `leads`
    // where registrant_email in ('ramyzaidan@qatar.net.qa', '2717410431@qq.com');



      if(isset($this->__clipboard) && sizeof($this->__clipboard)!= 0)
      {
        
          $leads_head = "UPDATE `leads` SET domains_count = CASE registrant_email ";
          $query  = "";
          $reg_em = "";
          foreach($this->__clipboard as $key=>$val)
          {
            $query .= " WHEN '".$key."' THEN ".$val;
            if($reg_em != '') $reg_em .=",";
            $reg_em .= "'". $key ."'";
          }
          $reg_em = "(".$reg_em.")";
          $leads_head .= $query;
          $leads_head .= " END WHERE registrant_email IN ".$reg_em;

          try{
            DB::statement($leads_head);
          }
          catch(\Exception $e){
            echo('In ..');
            dd($e);
          }
          

          $faulty_leads = Lead::where('domains_count',0)->pluck('registrant_email')->toArray();
          ValidatedPhone::whereIn('registrant_email',$faulty_leads)->delete();
          LeadUser::whereIn('registrant_email',$faulty_leads)->delete();
          Lead::where('domains_count',0)->delete();
      }

      return;
  }


 

  public function checknum($num)
  {
    $x = ValidatedPhone::where('phone_number','like', '%'.$num.'%')->first();
    dd($x);
  }

  public function validateUSPhoneNumber($ph)
    {
        $unmaskedPhoneNumber = preg_replace('/[\s()+-]+/', null, $ph);
        $phoneNumberLength = strlen($unmaskedPhoneNumber);
        if ($phoneNumberLength === 10) 
        {
            return ($this->validateAreaCode($unmaskedPhoneNumber, false));
        } 
        elseif ($phoneNumberLength === 11) 
        {
            if ((int)substr($unmaskedPhoneNumber, 0, 1) === 1) 
            {
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
    public function validateAreaCode($phoneNumber, $isdPrefix)
    {
        $areaPrefix = substr($phoneNumber, 0, 3);
        $areaIdentifier = substr($phoneNumber, 0, 6);
        
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

    public function fill_csv_table_default()
    {
        // $dates_array = array();
        // $leads_array = Lead::pluck('registrant_email','created_at')->toArray();
        // foreach($leads_array as $key=>$val)
        // {
        //     $temp = explode(" ", $key);
        //     if(!isset($dates_array[$temp[0]]))
        //     {
        //         $dates_array[$temp[0]] = 'done';
        //     }
        // }
        
    }
}
