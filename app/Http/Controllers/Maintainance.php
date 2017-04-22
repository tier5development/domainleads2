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
use \App\CurlError;
use \App\DomainFeedback;
use \App\CSV;
use DB;
use Carbon\Carbon;
use Zipper;
//use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;

class Maintainance extends Controller
{
  public function notfound_404()
  {
    return view('errors.404');
  }
  public function notfound_500()
  {
    return view('errors.500');
  }


  public function manage()
  {
    $data = CSV::select('file_name','query_time','leads_inserted','domains_inserted','status')->get();
    $data_head = array('file_name','insert_time','total_leads','total_domains'
                ,'status','link','delete');

    $data_arr = array();
    $i=0;
    foreach ($data as $key => $value) 
    {
      $data_arr[$i]['file_name'] = $value->file_name;
      $data_arr[$i]['insert_time']= gmdate("H:i:s", $value->query_time);
      $data_arr[$i]['leads_inserted'] = $value->leads_inserted;
      $data_arr[$i]['domains_inserted']= $value->domains_inserted;
      $data_arr[$i]['status'] = $value->status;
      $data_arr[$i]['link'] = 'some_link';
      $data_arr[$i++]['delete'] = 'delete';
    }
    
    return view('home.admin.manage.manage'
            ,['data'=>$data_arr,'data_head'=>$data_head]);
    //dd($csv);
  }


  // public function async_domain()
  // {
  //   $promise = new Promise();
  //   $nextPromise = new Promise();

  //   $promise
  //       ->then(function ($value) use ($nextPromise) {
  //           echo $value;
  //           return $nextPromise;
  //       })
  //       ->then(function ($value) {
  //           echo $value;
  //       });

  //   // Triggers the first callback and outputs "A"
  //   $promise->resolve('AA');
  //   // Triggers the second callback and outputs "B"
  //   $nextPromise->resolve('B');
  // }

  public function verify_domains() //working function
  {
      $start = microtime(true);

      $limit = 5000; //--<selects how many domain to ping with a get request..>

      $curl_errors = CurlError::pluck('err_reason','curl_error')->toArray();

      $all_domains = EachDomain::pluck('domain_name')->all();

      $domains_check = DomainFeedback::pluck('domain_name')->all();

      $diff = array_diff($all_domains,$domains_check);

      $domain_set = array_slice($diff,0,$limit);

      foreach($domain_set as $key=>$val)
      {
            $domain_name = $val;
            $url = "http://".$domain_name;
            $client = new Client(); //GuzzleHttp\Client
            try
            {
                $result = $client->get($url,array(
                                    'timeout' => 8, 
                                    'connect_timeout' => 8)); 
            }
            catch(\Exception $e)
            {
                $msg = str_replace($url, "", $e->getMessage());

                try{
                  $msg = explode(":", $msg);
                }
                catch(\Exception $e)
                {
                  $msg[0] = "<no : in curl error>";
                  $msg[1] = "<buffer> <as no : in curl error>";
                }
                if(!isset($domains_check[$domain_name]))
                {
                  $d_feedback  = new DomainFeedback();
                  $d_feedback->domain_name = $domain_name;
                  $d_feedback->checked     = 1;
                  $d_feedback->curl_error  = $msg[0];
                  $d_feedback->save();
                }
                if(!isset($curl_errors[$msg[0]]))
                {
                  try
                  {
                    $reason = explode(" ",$msg[1],2);
                  }
                  catch(\Exception $e)
                  {
                    //dd($e->getMessage());
                    $reason[1] = "<error type1>";
                  }
                  
                  $curl_errors[$msg[0]] = $reason[1]; 
                  $obj = new CurlError();
                  $obj->curl_error = $msg[0];
                  $obj->err_reason = $reason[1];
                  $obj->save();
                }
            }
      }

      $time = microtime(true) - $start;

      return ['time taken'=>$time,
              'limit'     =>$limit];
      //echo("time  taken =".$time);
  }


  
  public function each_domain_verification()
  {
            $domain_name = '0-give.org';//001hf.com
            $url = "http://".$domain_name;
            $client = new Client(); //GuzzleHttp\Client

            try{
               // $client->setDefaultOption('verify', true);
                $result = $client->get($url,array(
                                    'timeout' => 5, 
                                    'verify' => true,
                                    'connect_timeout' => 5)); 

                dd($result);
            }
            catch(GuzzleHttp\Exception\TransferException $e)
            {
              echo("in 1st response <br>");
              dd($e->getResponse()->getBody(true));
            }
            catch(GuzzleHttp\Exception\RequestException $e)
            {
              echo("in 2nd response <br>");
              dd($e->getResponse()->getBody());
            }
            catch(GuzzleHttp\Exception\ClientException $e)
            {
              echo("in 3th response <br>");
              dd($e->gettResponse()->getBody(true));
            }
            catch(GuzzleHttp\Exception\BadResponseException $e)
            {
              echo("in 4th response <br>");
              dd($e->getResponse()->getBody(true));
            }
            catch(GuzzleHttp\Exception\ServerException $e)
            {
              echo("in 5th response <br>");
              dd($e->getResponse()->getBody(true));
            }
            catch(GuzzleHttp\Exception\TooManyRedirectsException $e)
            {
              echo("in 6th response <br>");
              dd($e->getResponse()->getBody(true));
            }
            catch(\Exception $e)
            {
              echo("in 7th response <br>");
              $msg = $e->getMessage();

              $msg = str_replace($url, "", $msg);
              dd($msg);
            }
  }

}
