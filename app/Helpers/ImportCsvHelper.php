<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use \App\Area;
use \App\AreaCode;

use \App\Lead;
use \App\EachDomain;
use \App\DomainInfo;
use \App\DomainNameServer;
use \App\DomainStatus;
use \App\DomainTechnical;
use \App\DomainFeedback;
use \App\DomainBilling;
use \App\DomainAdministrative;
use Exception;
use \App\LeadUser;
use \App\ValidatedPhone;
use \App\CSV;
use DB;
use Carbon\Carbon;
use Zipper;
use Excel;
use App\Jobs\ImportCsv;
use Session;
use App\SocketMeta;
use App\Events\UsageInfo;
class ImportCsvHelper {

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
    
    try {
      $this->prepareLeadsArray();
      $this->prepareDomainsArray();
    } catch(\Exception $e) {
      $this->remove_atrocious_data();
      $this->prepareLeadsArray();
      $this->prepareDomainsArray();
    }
}

private function prepareLeadsArray() {
  $this->__leads  = Lead::pluck('registrant_email')->toArray();
  $this->__leads  = array_flip($this->__leads);
  foreach($this->__leads as $key=>$val)  $this->__leads[$key] = 0;
  return;
}

private function prepareDomainsArray() {
  $this->__domains= EachDomain::pluck('registrant_email','domain_name')->toArray();
  foreach($this->__domains as $key=>$val) {
    $this->__leads[$val]++;
  }
}

private function destroy()
{
  unset($this->Area_state);
  unset($this->Area_major_city);
  unset($this->Area_codes_primary_city);
  unset($this->Area_codes_county);
  unset($this->Area_codes_carrier_name);
  unset($this->Area_codes_number_type);
  unset($this->__leads);
  unset($this->__domains);
  unset($this->search);
  unset($this->replace);
  unset($this->__clipboard);
}


  public function importExcel(Request $request)
  {
    try {
      $start = microtime(true);
      $upload = $request->file('import_file');
      if($upload == null) {
        return redirect()->back()->with('error', 'Please select a csv file');
      }
      $filepath = $upload->getRealPath();
      $original_file_name = $request->import_file->getClientOriginalName();


      $csv_exists = CSV::where('file_name',$original_file_name);
      if($csv_exists->first() == null) {
        $total_leads_before_insertion = Lead::count();
        $total_domains_before_insertion = EachDomain::count();
        $file  = fopen($filepath , 'r');
        echo('***** Insertion started ********');
        $this->insertion_Execl($file);
        fclose($file);
        echo('***** Insertion ended *******');
        $leads_inserted   = Lead::count()-$total_leads_before_insertion;
        $domains_inserted = EachDomain::count()-$total_domains_before_insertion;
        $end = microtime(true) - $start;

        $obj = new CSV();
        $obj->file_name         = $original_file_name;
        $obj->leads_inserted    = $leads_inserted;
        $obj->domains_inserted  = $domains_inserted;
        $obj->status            = 2;
        $obj->query_time        = $end;
        $obj->save();

        $socketMeta = SocketMeta::first();
        $socketMeta->leads_added_last_day = $leads_inserted;
        $socketMeta->save();
        event(new UsageInfo());

        return \Response::json(array('TOTAL TIME TAKEN:'=>$end." seconds",
                                  'leads_inserted'=>$leads_inserted,
                                  'domains_inserted'=>$domains_inserted,
                                  'status'=>200,
                                  'filename'=>$original_file_name));

      } else {
          return \Response::json(array('insertion_time'=>'null',
                                         'message'=>'This file is inserted already::'.$original_file_name,
                                         'status'=>500));
      }
    } catch(\Exception $e) {
      return \Response::json(array('insertion_time' =>  'n/a',
                                         'message'  =>  ' ERROR : '.$e->getMessage().' LINE : '.$e->getLine(),
                                         'status'   =>  500));
    }
  }

  public function autoImportExcelFile(){
    //$currentDate = date("Y-m-d");
    $currentDate = date('Y-m-d',time()-3600*24);
    $whoxyURL = "https://www.whoxy.com/newly-registered-domains/download.php?key=3b1205bf714563e&file=".$currentDate."_proxies.zip";
    $startTime = microtime(true);
    try {
      $checkFileExist = CSV::where('file_name',$currentDate."_whois-proxies-removed.csv")->first();
      if($checkFileExist == null) {
        $downloadDir = public_path();
        $getDownloadData = file_get_contents($whoxyURL);
        file_put_contents($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip',$getDownloadData);
        Zipper::make($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip')->extractTo($downloadDir.'/unzipFiles/');
        $csvFilePath = $downloadDir.'/unzipFiles/'.$currentDate.'_whois-proxies-removed.csv';
        $getCSVFile  = fopen($csvFilePath , 'r');
        $total_leads_before_insertion = Lead::count();
        $total_domains_before_insertion = EachDomain::count();
        $this->insertion_Execl($getCSVFile);
        fclose($getCSVFile);
        $leads_inserted   = Lead::count() - $total_leads_before_insertion;
        $domains_inserted = EachDomain::count() - $total_domains_before_insertion;
        $endTime = microtime(true) - $startTime;
        $csvObj = new CSV();
        $csvObj->file_name          = $currentDate."_whois-proxies-removed.csv";
        $csvObj->leads_inserted     = $leads_inserted;
        $csvObj->domains_inserted   = $domains_inserted;
        $csvObj->status             = 2;
        $csvObj->query_time         = $endTime;
        $csvObj->save();
        unlink($csvFilePath);
        unlink($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip');
        return \Response::json(array('TOTAL TIME TAKEN:'=>$endTime." seconds",
                                    'leads_inserted'=>$leads_inserted,
                                    'domains_inserted'=>$domains_inserted,
                                    'status'=>200,
                                    'message'=>'success',
                                    'filename'=>$currentDate."_whois-proxies-removed.csv"));
      } else {
        return \Response::json(array('insertion_time'=>'null',
                                  'message'=>'This file is inserted already::'.$currentDate."_whois-proxies-removed.csv",
                                  'status'=>500));
      }
    } catch (Exception $exception) {
      dd($exception->getMessage());
    }
  }

  public function importExeclfromCron($date)
  {
    $user = 't5ilmpnba';
    $pass = '4on9sq6ae8lMRVHCZxp2';  //+++++++ date format :: '2017-01-18';
    $start = microtime(true);
    //dd('kjyfgkg');
    try {
          $csv_exists = CSV::where('file_name',$date."_whois-proxies-removed.csv");
          if($csv_exists->first() == null) {
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
          } else {
            return \Response::json(array('insertion_time'=>'null',
                                         'message'=>'This file is inserted already::'.$date."_whois-proxies-removed.csv",
                                         'status'=>500));
          }
    } catch(\Exception $e) {
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
    try {
        
        if($num != '') {
            $no = explode('.',$num);
            if(isset($no[1])) {
                $arr = ($this->validateUSPhoneNumber($no[1]));
            } else {
                $arr = ($this->validateUSPhoneNumber($no[0]));
            }

            if($arr['http_code'] == 200 && ($arr['number_type'] == 'Landline' || $arr['number_type'] == 'Cell Number')) {
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
    } catch(\Exception $e) {
        //\Log::info('from :: validate_phone_query_builder :: '.$e->getMessage());
        dd($e);
    }
    return $str;
  }

  public function make_query($low , $high , $record, $created_at, $updated_at,$domains_count)
  {
    $str = '';
    if($high - $low == 0) {
      $rg_em = str_replace($this->search, $this->replace, $record[17]);
      $rec = str_replace($this->search, $this->replace, $record[$low]);
      $d_ext = explode("." , $rec);
      $ext = $d_ext[sizeof($d_ext)-1];

      if(strlen($rg_em) > 110 || strlen($rec)>100 || strlen($ext)>30)
        return -1;

      $str .= "NULL,'".$rec."','".$ext."','0',NULL,NULL,'".$rg_em."'";
    } else {
      
      for($i = $low ; $i<= $high ; $i++) {
        $x = !isset($record[$i]) ? " " : $record[$i];
        $rec = str_replace($this->search, $this->replace, $x);
        $rec = trim($rec);
        if($i == $low) {
            if($i == 10) {
              $name_ = explode(" ",$rec);
              $fname = isset($name_[0]) ? $name_[0] : " ";
              $lname = isset($name_[1]) ? $name_[1] : " ";
              $str .= "NULL , '".$fname."' , '".$lname."' ,";
            } else {
              $str .= "NULL , '".$rec."' ,";
            }
        } else if($i != $high) {
          if($i == 18) $str .= "'".$rec."' , 'yes' ,";
          else if($i == 16) $str .= "'".getCountryName($rec)."',";
          else $str .= "'".$rec."'," ;
        } else {
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
        // DB::statement(DB::raw('RESET QUERY CACHE;'));
        $q_leads    = "REPLACE `leads` ". $leads_head. " VALUES ".$LEADS;

        if($VALID_PHONE != '') {
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

          $time_array = array();

          $t = microtime(true);
          DB::statement($q_leads);
          array_push($time_array, 'leads -> '.(microtime(true)-$t));
          unset($leads_head);
          unset($LEADS);
          unset($q_leads);

          $t = microtime(true);
          if(isset($q_valid_phone)) DB::statement($q_valid_phone);
          array_push($time_array, 'valid_phone -> '.(microtime(true)-$t));
          unset($valid_phone_head);
          unset($VALID_PHONE);
          unset($q_valid_phone);

          $t = microtime(true);
          DB::statement($q_each_domains);
          array_push($time_array, 'each_domain -> '.(microtime(true)-$t));
          unset($each_domains_head);
          unset($EACH_DOMAINS);
          unset($q_each_domains);

          $t = microtime(true);
          DB::statement($q_domains_info);
          array_push($time_array, 'domains_info -> '.(microtime(true)-$t));
          unset($domains_info_head);
          unset($DOMAINS_INFO);
          unset($q_domains_info);

          $t = microtime(true);
          DB::statement($q_domains_administrative);
          array_push($time_array, 'domains_info -> '.(microtime(true)-$t));
          unset($domains_administrative_head);
          unset($DOMAINS_ADMINISTRATIVE);
          unset($q_domains_administrative);

          $t = microtime(true);
          DB::statement($q_domains_technical);
          array_push($time_array, 'domains_technical -> '.(microtime(true)-$t));
          unset($domains_technical_head);
          unset($DOMIANS_TECHNICAL);
          unset($q_domains_technical);

          $t = microtime(true);
          DB::statement($q_domains_billing);
          array_push($time_array, 'domains_billing -> '.(microtime(true)-$t));
          unset($domains_billing_head);
          unset($DOMIANS_BILLING);
          unset($q_domains_billing);

          $t = microtime(true);
          DB::statement($q_domains_nameserver);
          array_push($time_array, 'domains_nameserver -> '.(microtime(true)-$t));
          unset($domains_nameserver_head);
          unset($DOMIANS_NAMESERVER);
          unset($q_domains_nameserver);

          $t = microtime(true);
          DB::statement($q_domains_status);
          array_push($time_array, 'domains_status -> '.(microtime(true)-$t));
          unset($domains_status_head);
          unset($DOMAINS_STATUS);
          unset($q_domains_status);

          return $time_array;
    } catch(\Exception $e) {
      \Log::info('From import export :: while querry executing :: '.$e->getMessage());
      dd($e->getMessage());
    }
  }

  public function set($domain_name,$registrant_email)
  {
      if(isset($this->__domains[$domain_name])) {
          if($this->__domains[$domain_name] == $registrant_email) {
              //do nothing because same record--
          } else {
              $this->__leads[$this->__domains[$domain_name]] --;
              $this->__clipboard[$this->__domains[$domain_name]] = $this->__leads[$this->__domains[$domain_name]];
              if(isset($this->__leads[$registrant_email]))
                $this->__leads[$registrant_email]++;
              else
                $this->__leads[$registrant_email]=1;
              $this->__domains[$domain_name] = $registrant_email;
          }
      } else {
          if(isset($this->__leads[$registrant_email])) {
              $this->__leads[$registrant_email]++;
              $this->__domains[$domain_name] = $registrant_email;
          } else {
              $this->__domains[$domain_name] = $registrant_email;
              $this->__leads[$registrant_email] = 1;
          }
      }
      return $this->__leads[$registrant_email];
  }

  //atrocious data populates when server gets shut down or import data process is forced stopped while execution or server restarts or mysql restarts.
  //this clears out data with mismatches
  private function remove_atrocious_data()
  {
    /**
     * Checking bad leads with bad email
     */
    $deleteSQL = "DELETE FROM leads WHERE LOWER(registrant_email) NOT REGEXP '^.+@.+$';";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in each_domain
     */
    $table = 'each_domain';
    $deleteSQL = "DELETE `each_domain` FROM `each_domain` LEFT JOIN `leads` on `each_domain`.`registrant_email` = `leads`.`registrant_email` WHERE `leads`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_info if found any delete
     */
    $table = 'domains_info';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_technical if found any delete
     */
    $table = 'domains_technical';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_status if found any delete
     */
    $table = 'domains_status';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_nameserver if found any delete
     */
    $table = 'domains_nameserver';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_feedback if found any delete
     */
    $table = 'domains_feedback';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_billing if found any delete
     */
    $table = 'domains_billing';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);

    /**
     * Checking orphan data in domains_administrative if found any delete
     */
    $table = 'domains_administrative';
    $deleteSQL = "DELETE `$table` FROM `$table` LEFT JOIN `each_domain` on `$table`.`domain_name` = `each_domain`.`domain_name` WHERE `each_domain`.`id` is NULL;";
    DB::statement($deleteSQL);
  }

  private function validate_input($row)
  {
    $email  = str_replace($this->search, $this->replace, $row[17]);
    $domain = str_replace($this->search, $this->replace, $row[1]);
    $d_ext  = explode("." , $domain);
    $ext    = $d_ext[sizeof($d_ext)-1];
    $zip    = str_replace($this->search, $this->replace, $row[15]);
    if(strlen($email)>110 || strlen($domain)>100 || strlen($ext)>30 || strlen($zip)>15)
      return false;
    return true;
  }

  private function validateEmail($email) {
    // This is standard laravel rule for email validation
    return preg_match('/^.+@.+$/i', $email);
  }

  public function insertion_Execl($file)
  {
      $query_time_array = array();
      $loop_time = array();
      $tm1 = microtime(true);

      // ini_set("memory_limit","15G");
      set_time_limit(30000);
      ini_set('max_execution_time', '0');
      ini_set('max_input_time', '0');
      set_time_limit(0);
      ignore_user_abort(true);

      $st = microtime(true);
      // $this->remove_atrocious_data(); //:: NOT NEEDED
      $ed = microtime(true)-$st;
      array_push($query_time_array,'database_cleanup',$ed);

      $st = microtime(true);
      $this->create();
      $ed = microtime(true) - $st;
      array_push($query_time_array,'create globals',$ed);

      $start = microtime(true);
      $cnt = 0;
      $header = fgetcsv($file); // get the head row of csv file
      $length = count($header); // get the count of columns in it

      // dd($header);
      $valid_phone_head = "(`id` , `phone_number` , `validation_status` , `state` , `major_city` , `primary_city` ,`county` , `carrier_name` , `number_type` , `created_at`,`updated_at`,`registrant_email`)"  ; //dynamically created

      // 10 - 19 goes to leads table
      // heads for leads table for mysql entry
      $leads_head = "(`id`,`registrant_fname`,`registrant_lname`,`registrant_company`,`registrant_address`,`registrant_city`,`registrant_state`,`registrant_zip`,`registrant_country`,`registrant_email`,`registrant_phone`,`phone_validated`,`registrant_fax`,`created_at`,`updated_at`,`unlocked_num`,`domains_count`)" ;

      // record 1 goes to each_domains table
      $each_domains_head = "(`id`,`domain_name`,`domain_ext`,`unlocked_num`,`created_at` , `updated_at`,`registrant_email`)";

      // from record 2 to 9 goes to domains_info table
      $domains_info_head = "(`id`,`query_time`,`domains_create_date`,`domains_update_date`,`expiry_date`,`domain_registrar_id`,`domain_registrar_name`,`domain_registrar_whois`,`domain_registrar_url`,`created_at`,`updated_at`,`domain_name`)";

      // 20-29 goes to domains administrative
      $domains_administrative_head = "(`id` , `administrative_name`,`administrative_company`,`administrative_address`,`administrative_city`,
      `administrative_state`,`administrative_zip` , `administrative_country`, `administrative_email` , `administrative_phone`, `administrative_fax` , `created_at`,`updated_at`,`domain_name`)";

      // 30-39 goes to domains_technical table
      $domains_technical_head = "(`id` , `technical_name`,`technical_company`,`technical_address`,`technical_city`,`technical_state`,`technical_zip`,`technical_country`,`technical_email`,`technical_phone`,`technical_fax`,`created_at`,`updated_at`,`domain_name`)";

      // 40-49 goes to domains_billing
      $domains_billing_head = "(`id`,`billing_name`,`billing_company`,`billing_address`,`billing_city` , `billing_state`,`billing_zip`,`billing_country`,`billing_email`,`billing_phone`,`billing_fax` , `created_at`,`updated_at`,`domain_name`)";

      // 50-53 goes to nameserver
      $domains_nameserver_head = "(`id`,`name_server_1`,`name_server_2`,`name_server_3`,`name_server_4`,`created_at`,`updated_at`,`domain_name`)";

      // 54-57 goes to domainstatus
      $domains_status_head = "(`id`,`domain_status_1`,`domain_status_2`,`domain_status_3`,`domain_status_4`,`created_at`,`updated_at`, `domain_name`)";


      $each_domains             = '';
      $domains_info             = '';
      $leads                    = '';
      $valid_phone              = '';
      $domains_administrative   = '';
      $domains_technical        = '';
      $domains_billing          = '';
      $domains_nameserver       = '';
      $domains_status           = '';

      $EACH_DOMAINS             = '';
      $DOMAINS_INFO             = '';
      $LEADS                    = '';
      $VALID_PHONE              = '';
      $DOMAINS_ADMINISTRATIVE   = '';
      $DOMIANS_TECHNICAL        = '';
      $DOMIANS_BILLING          = '';
      $DOMIANS_NAMESERVER       = '';
      $DOMAINS_STATUS           = '';

      $BATCH  = 5000; // to insert 5000 data at 1 go
      $counter = 0;
      while(true)
      {
          $row = fgetcsv($file);
          $counter++ ;
          if($row)
          {
              //dd($row);
              $created_at = str_replace($this->search, $this->replace, Carbon::now());
              $updated_at = str_replace($this->search, $this->replace, Carbon::now());
              // dd($this->validateEmail($row[17]));
              if(!$this->validateEmail($row[17])) {
                continue;
              }
              
              $return_val = $this->make_query(1 , 1 , $row,$created_at,$updated_at,null);
              if($return_val == -1) continue;

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

              $each_domains = $return_val;
              $rg_em = str_replace($this->search, $this->replace, $row[17]);
              $domains_count          = $this->set($row[1],$row[17]); //-------------
              $leads                  = $this->make_query(10 , 19 , $row ,$created_at
                                              ,$updated_at,$domains_count);
              $valid_phone            = $this->validate_phone_query_builder($row[18]
                                                          ,$rg_em,$counter,$created_at,$updated_at);
              $domains_info           = $this->make_query(2 , 9 , $row,$created_at
                                        ,$updated_at,null);
              $domains_administrative = $this->make_query(20 , 29 , $row,$created_at
                                        ,$updated_at,null);
              $domains_technical      = $this->make_query(30 , 39 , $row,$created_at
                                        ,$updated_at,null);
              $domains_billing        = $this->make_query(40 , 49 , $row,$created_at
                                        ,$updated_at,null);
              $domains_nameserver     = $this->make_query(50 , 53 , $row,$created_at
                                        ,$updated_at,null);
              $domains_status         = $this->make_query(54 , 57 , $row,$created_at
                                        ,$updated_at,null);

              $LEADS .= '('.$leads.')';
              if($valid_phone != '')  $VALID_PHONE .= "(".$valid_phone.")";
              $EACH_DOMAINS           .= '('.$each_domains.')';
              $DOMAINS_INFO           .= '(' . $domains_info . ')';
              $DOMAINS_ADMINISTRATIVE .= '('.$domains_administrative.')';
              $DOMIANS_TECHNICAL      .= '('.$domains_technical.')';
              $DOMIANS_BILLING        .= '('.$domains_billing.')';
              $DOMIANS_NAMESERVER     .= '('.$domains_nameserver.')';
              $DOMAINS_STATUS         .= '('.$domains_status .')';

              if($counter%$BATCH == 0) {
                // dd('over');
                $ed = $this->execute_batch_query($leads_head    ,$LEADS
                        ,$valid_phone_head            ,$VALID_PHONE
                        ,$each_domains_head           ,$EACH_DOMAINS
                        ,$domains_info_head           ,$DOMAINS_INFO
                        ,$domains_administrative_head , $DOMAINS_ADMINISTRATIVE
                        ,$domains_technical_head      , $DOMIANS_TECHNICAL
                        ,$domains_billing_head        , $DOMIANS_BILLING
                        ,$domains_nameserver_head     , $DOMIANS_NAMESERVER
                        ,$domains_status_head         , $DOMAINS_STATUS);
                array_push($query_time_array, $ed);

                $LEADS                  = '';
                $VALID_PHONE            = '';
                $EACH_DOMAINS           = '';
                $DOMAINS_INFO           = '';
                $DOMAINS_ADMINISTRATIVE = '';
                $DOMIANS_TECHNICAL      = '';
                $DOMIANS_BILLING        = '';
                $DOMIANS_NAMESERVER     = '';
                $DOMAINS_STATUS         = '';
              }
            } else {
              if($counter % $BATCH != 0) {
                $ed = $this->execute_batch_query($leads_head    ,$LEADS
                      ,$valid_phone_head            ,$VALID_PHONE
                      ,$each_domains_head           ,$EACH_DOMAINS
                      ,$domains_info_head           ,$DOMAINS_INFO
                      ,$domains_administrative_head , $DOMAINS_ADMINISTRATIVE
                      ,$domains_technical_head      , $DOMIANS_TECHNICAL
                      ,$domains_billing_head        , $DOMIANS_BILLING
                      ,$domains_nameserver_head     , $DOMIANS_NAMESERVER
                      ,$domains_status_head         , $DOMAINS_STATUS);
                array_push($query_time_array, $ed);
              }
              break;
          }
      }

    $st = microtime(true);
    $this->rectify_leads();
    $ed = microtime(true) - $st;
    array_push($query_time_array,'rectification_of_inserted_data',$ed);
    $end = microtime(true) - $start;
    \Log::info('time ==>> ',$query_time_array);
    echo "<pre>";print_r($query_time_array);
    $this->destroy();

    // DESTROY ALL MEORY ALLOCATIONS
    unset($leads_head);
    unset($LEADS);
    unset($valid_phone_head);
    unset($VALID_PHONE);
    unset($each_domains_head);
    unset($EACH_DOMAINS);
    unset($domains_info_head);
    unset($DOMAINS_INFO);
    unset($domains_administrative_head);
    unset($DOMAINS_ADMINISTRATIVE);
    unset($domains_technical_head);
    unset($DOMIANS_TECHNICAL);
    unset($domains_billing_head);
    unset($DOMIANS_BILLING);
    unset($domains_nameserver_head);
    unset($DOMIANS_NAMESERVER);
    unset($domains_status_head);
    unset($DOMAINS_STATUS);
    return $query_time_array;
  }

  public function rectify_leads()
  {
    // DO NOT DELETE THIS
    // rectifies those leads whose domain names are changed but domain counts are not
    // in the process of insertion

    // UPDATE `leads`
    //   SET domains_count = CASE registrant_email
    //       WHEN 'info1@gctld.com' THEN 10
    //       WHEN '616822783@qq.com' THEN 20
    //   END
    // WHERE registrant_email IN ('info1@gctld.com','616822783@qq.com')

    // delete
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

  public function validateUSPhoneNumber($ph)
  {
      $unmaskedPhoneNumber = preg_replace('/[\s()+-]+/', null, $ph);
      $phoneNumberLength = strlen($unmaskedPhoneNumber);
      if ($phoneNumberLength === 10) {
          return ($this->validateAreaCode($unmaskedPhoneNumber, false));
      } elseif ($phoneNumberLength === 11) {
          if ((int)substr($unmaskedPhoneNumber, 0, 1) === 1) {
              return ($this->validateAreaCode(substr($unmaskedPhoneNumber, 1, 10), true));
          } else {
              return [
                  "http_code" => 404,
                  "validation_status" => "invalid",
                  "validation_message" => "This phone number does not belongs to US."
              ];
          }
      } else {
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
      }
  }

  public function importExpiredDomainsZip($currentDate = null, $whoxyURLexpired = null) {
    try {
      
      if($currentDate == null || $whoxyURLexpired == null) {
        return \Response::json(['insertion_time' =>  'null', 'message' =>  'Improper arguments, function parameters cannot be null', 'status'  =>  500]);
      }
      
      CSV::where("status", 1)->delete();
      $startTime = microtime(true);
      $checkFileExist = CSV::where('file_name',$currentDate.".csv")->first();
      if($checkFileExist == null)
      {
        $downloadDir = public_path();
        $getDownloadData = file_get_contents($whoxyURLexpired);
        file_put_contents($downloadDir.'/zipFiles/'.$currentDate.'.zip',$getDownloadData);
        
        /**
         * Unzipping and putting to a destination
         */
        Zipper::make($downloadDir.'/zipFiles/'.$currentDate.'.zip')->extractTo($downloadDir.'/unzipFiles/');
        $csvFilePath = $downloadDir.'/unzipFiles/'.$currentDate.'.csv';
        $getCSVFile  = fopen($csvFilePath , 'r');
        $total_leads_before_insertion = Lead::count();
        $total_domains_before_insertion = EachDomain::count();

        /**
         * Calling system wide basic insert function
         */
        $csvObj = new CSV();
        $csvObj->file_name = $currentDate.".csv";
        $csvObj->status = 1;
        $csvObj->save();

        $this->insertion_Execl($getCSVFile);
        fclose($getCSVFile);
        
        /**
         * Calculate how many leads and domains got effected by the insertion
         */
        $leads_inserted   = Lead::count() - $total_leads_before_insertion;
        $domains_inserted = EachDomain::count() - $total_domains_before_insertion;
        $endTime = microtime(true) - $startTime;

        /**
         * Insert a new record in csv
         */
        $csvObj->leads_inserted   = $leads_inserted;
        $csvObj->domains_inserted = $domains_inserted;
        $csvObj->status           = 2;
        $csvObj->query_time       = $endTime;
        $csvObj->save();
        $socketMeta = SocketMeta::first();
        $socketMeta->leads_added_last_day = $leads_inserted;
        $socketMeta->save();
        event(new UsageInfo());

        /**
         * Free up memory by deleting the files stored by unzipping
         */
        unlink($csvFilePath);
        unlink($downloadDir.'/zipFiles/'.$currentDate.'.zip');
        unset($import);
        return \Response::json(array('TOTAL TIME TAKEN:'=>$endTime." seconds",
                                    'leads_inserted'=>$leads_inserted,
                                    'domains_inserted'=>$domains_inserted,
                                    'status'=>200,
                                    'message'=>'success',
                                    'filename'=>$currentDate.".csv"));
      } else {
        return \Response::json(array('insertion_time'=>'null',
                                  'message'=>'This file is inserted already::'.$currentDate.".csv",
                                  'status'=>500));
      }
    } catch (\Exception $e) {
      return \Response::json(['insertion_time'=>'null',
                                  'message'=>" ERROR : ".$e->getMessage().' LINE : '.$e->getLine(),
                                  'status'=>500]);
      \Log::info('from :: Error ::: '.$e->getMessage());
    }
  }
}
?>