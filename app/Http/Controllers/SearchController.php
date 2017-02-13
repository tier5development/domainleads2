<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use \App\DomainAdministrative;
use \App\DomainBilling;
use \App\DomainInfo;
use \App\DomainNameServer;
use \App\DomaStatus;
use \App\DomaTechnical;
use \App\EachDomain;
use \App\Lead;
use \App\LeadUser;
use \App\User;
use DB;
use Hash;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class SearchController extends Controller
{
    
    public function chkWebsiteForDomain(Request $request){
   
     $domain_name= $request->domain_name;
     $client = new Client(); //GuzzleHttp\Client
      $client->setDefaultOption('verify', false);
                $result = $client->get('http://api.tier5.website/api/make_free_wp_website/'.$domain_name);
                $domain_data = json_decode($result->getBody()->getContents(), true);
                echo $domain_data['message'];

    }
    public function lead_domains($email)
    {
      $email = decrypt($email);
      $alldomains = EachDomain::where('registrant_email',$email);
      return view('home.lead_domains',['alldomain'=>$alldomains , 'email'=>$email]);
    }


    public function unlockleed(Request $request)
    {
        $leaduser = new LeadUser();
        $leaduser->user_id = $request->user_id;
        $leaduser->registrant_email = $request->registrant_email;

        $lead = Lead::where('registrant_email',$request->registrant_email)->first();
        $lead->unlocked_num++;

        if($lead->save() && $leaduser->save())
        {
          $data = Lead::where('registrant_email',$request->registrant_email)->first();

          $array = array();
          $array['status'] = 'success';
          $array['registrant_email']    = $data->registrant_email;
          $array['registrant_name']     = $data->registrant_name ;
          $array['registrant_phone']    = $data->registrant_phone;
          $array['registrant_company']  = $data->registrant_company;
          $array['domain_name']         = $data->each_domain->first()->domain_name;
          $array['domains_create_date'] = $data->each_domain->first()->domains_info->first()->domains_create_date;
          $array['unlocked_num']        = $lead->unlocked_num;
          //$array['total_domain_count']  = $lead->each_domain;

          return \Response::json($array);
        }

        return \Response::json(array('status'=>'failure'));
    }

    // public function search()
    // {
    // 	$allrecords = null;
    //   $leadArr = null;
    //   $totalDomains = null;
    //   return view('home.search' , ['record' => $allrecords , 'leadArr'=>$leadArr , 'totalDomains'=>$totalDomains]);
    // }

    public function myLeads($id)
    {
      if(\Auth::check())
      {
        $id = decrypt($id);

        $leads_ = LeadUser::where('user_id',$id)->pluck('registrant_email')->toArray();
        $leads  = Lead::whereIn('registrant_email',$leads_);
        //dd($leads);

        $leadArr_ = EachDomain::pluck('registrant_email')->toArray();
        $leadArr = array_flip($leadArr_);


            foreach($leadArr as $key=>$each)
              $leadArr[$key] = 0;

            
            
            $eachdomainArr = EachDomain::pluck('registrant_email','domain_name')->toArray();
            $totalDomains = 0;
            
            foreach($eachdomainArr as $key=>$each)
            {
                if(isset($leadArr[$each]))
                {
                  $leadArr[$each]++;
                  $totalDomains++;
                }
            }

        return view('home.myleads' , ['myleads'=>$leads,'leadArr'=>$leadArr]);
      }
      else
      {
        dd('Not Logged In');
      }
    }

    public function search(Request $request)
    {

      if(\Auth::check())
      {

        if($request->all())
        {

          $allrecords = Lead::with('each_domain','valid_phone');
      
            $st = microtime(true);
            foreach($request->all() as $key => $req)
            {         

                if(!is_null($request->$key))
                {

                    //var_dump($key.'<br>');
                    if($key == 'registrant_country')
                    {
                        $allrecords = $allrecords->where('registrant_country', $req);

                    }
                    else if($key == 'registrant_state')
                    {
                        $allrecords = $allrecords->where('registrant_state', $req);
                    }
                    else if($key == 'domain_name')
                    {
                        $allrecords = $allrecords->whereHas('each_domain' , function($query) use($key,$req){
                            $query->where($key, 'like', '%'.$req.'%');
                        });
                    }
                    else if($key == 'domains_create_date')
                    {
                        $allrecords = $allrecords->whereHas('each_domain' , function($query) use($key,$req){
                            $query->whereHas('domains_info',function($q) use($key,$req){
                                //dd($req);
                                $q->where($key,$req);
                            });
                        });
                    }
                    else if($key == 'cell_number')
                    {
                        $allrecords = $allrecords->whereHas('valid_phone',function($query) use($key,$req){
                            $query->where($key,$req);
                        });
                    }
                    else if($key == 'landline_number')
                    {

                        $allrecords = $allrecords->whereHas('valid_phone',function($query) use($key,$req){
                            $query->where($key,$req);
                        });
                      
                    }
                    //applying sort filter
                    else if ($key == 'sort') 
                    {
                        if($req == 'unlocked_asnd')
                        {
                            $allrecords = $allrecords->orderBy('unlocked_num','asc');
                        }
                        else if($req == 'unlocked_dcnd')
                        {
                            $allrecords = $allrecords->orderBy('unlocked_num','desc');
                        }
                        else if($req == 'domain_count_asnd')
                        {
                            $allrecords = $allrecords->select(DB::raw('leads.*, count(*) as `aggregate`'))
                            ->join('each_domain', 'leads.registrant_email', '=', 'each_domain.registrant_email')
                            ->groupBy('leads.registrant_email')
                            ->orderBy('aggregate', 'asc');
                        }
                        else if($req == 'domain_count_dcnd') 
                        {
                            $allrecords = $allrecords->select(DB::raw('leads.*, count(*) as `aggregate`'))
                            ->join('each_domain', 'leads.registrant_email', '=', 'each_domain.registrant_email')
                            ->groupBy('leads.registrant_email')
                            ->orderBy('aggregate', 'desc');
                        }
                    }
                }

            }


            $leadArr_ = $allrecords->pluck('registrant_email')->toArray();
            $leadArr = array_flip($leadArr_);


            foreach($leadArr as $key=>$each)
              $leadArr[$key] = 0;

            
            
            $eachdomainArr = EachDomain::pluck('registrant_email','domain_name')->toArray();
            $totalDomains = 0;
            
            foreach($eachdomainArr as $key=>$each)
            {
                if(isset($leadArr[$each]))
                {
                  $leadArr[$each]++;
                  $totalDomains++;
                }
            }

            //dd($allrecords->first());

            $user_id = \Auth::user()->id;

            //dd($leadArr_);

            $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
            

            // $users_array = array_flip($users_array);

            
            $tst = $allrecords->pluck('domains_count','registrant_email')->toArray();
            //dd($tst);
            foreach($tst as $key=>$val)
            {
                if($leadArr[$key] != $tst[$key])
                dd("key=".$key."   val=".$val."   real count=".$leadArr[$key]."   domains_count=".$tst[$key]."<br>");
            }

            dd('over');
            
                //->paginate(100);
            //$en = microtime(true);
            //dd($en-$st);
            //dd($allrecords);

            

            return view('home.search' , 
                  ['record' => $allrecords->paginate($request->pagination), 
                  'leadArr'=>$leadArr , 
                  'totalDomains'=>$totalDomains,
                  'users_array'=>$users_array]);
        }
        else
        {
          $allrecords = null;
          $leadArr = null;
          $totalDomains = null;
          return view('home.search' , ['record' => $allrecords , 'leadArr'=>$leadArr , 'totalDomains'=>$totalDomains]);
        }
      }
      else
      {

        dd('Please log in');  

      }
    }



}
