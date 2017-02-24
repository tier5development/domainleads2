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
use DB;
use Carbon\Carbon;
use Zipper;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Client;

class Maintainance extends Controller
{
  public function async_domain()
  {
    //dd(1);
    $promise = new Promise();
    dd('done'); 
    // $promise = $client->requestAsync('GET', 'http://httpbin.org/get');
    // $promise->then(function ($response) {
    // echo 'Got a response! ' . $response->getStatusCode();
    //});
  }


  public function domain_verification()
  {
        $curl_errors = CurlError::pluck('err_reason','curl_error')->toArray();
        $domains_check = DomainFeedback::pluck('curl_error','domain_name')->toArray();

        $domains_feedback = array();
        $curl_new_error   = array();

        foreach($curl_errors as $key=>$val)
            $domains_list[$key] = "";
        


        $each_domain =EachDomain::skip(0)->take(10);
        $reasons  = array();
        $domains_to_update = array();

        foreach($each_domain->get() as $each)
        {
            $domain_name = $each->domain_name;
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
                $msg = explode(":", $e->getMessage());
                if(!isset($domains_check[$each->domain_name]))
                {
                  // array_push($domains_feedback,
                  //           array('domain_name' =>  $each->domain_name,
                  //                 'checked'     =>  1,
                  //                 'curl_error'  =>  $msg[0],
                  //                 'created_at'  =>  Carbon::now(),
                  //                 'updated_at'  =>  Carbon::now()));
                  $d_feedback  = new DomainFeedback();
                  $d_feedback->domain_name = $each->domain_name;
                  $d_feedback->checked     = 1;
                  $d_feedback->curl_error  = $msg[0];
                  $d_feedback->save();
                }
                if(!isset($curl_errors[$msg[0]]))
                {
                  $reason = explode(" ",$msg[1],2);
                  $curl_errors[$msg[0]] = $reason[1]; 

                  // array_push($curl_new_error,
                  //           array('curl_error'  =>  $msg[0],
                  //                 'err_reason'  =>  $reason[1]));

                  $obj = new CurlError();
                  $obj->curl_error = $msg[0];
                  $obj->err_reason = $reason[1];
                  $obj->save();
                }
            }
        }
        //dd($st);
        //dd($domains_feedback);
        
        // UPDATE `domains_feedback`
        //   SET curl_error = CASE domain_name
        //       WHEN 'domain_name1' THEN 'error1'
        //       WHEN 'domain_name2' THEN 'error2'
        //   END
        // WHERE domain_name IN ('domain_name1','domain_name2')

        //DomainFeedback::insert($domains_feedback);
        //CurlError::insert($curl_new_error);
        echo('success');


        // Create a client with a base URI
  }


















  public function each_domain_verification()
  {
            $domain_name = '01c.loan';//001hf.com
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
              dd($e->getMessage());
            }
  }

}
