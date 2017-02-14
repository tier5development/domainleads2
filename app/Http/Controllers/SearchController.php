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
use \App\ChkWebsite;
use DB;
use Hash;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;


class SearchController extends Controller
{   


    public function downloadExcel(Request $request)

  {
    $type='csv'; 
      
      $user_type=Auth::user()->user_type;
      $user_id=Auth::user()->id;
        $domains_for_export_allChecked=$request->domains_for_export_allChecked;
        if($domains_for_export_allChecked==1){
          if($user_type==1){
            $exel_data=DB::table('leadusers')
                          ->join('leads', 'leads.id', '=', 'leadusers.leads_id')
                          ->join('domains', 'domains.id', '=', 'leadusers.domain_id')
                          ->join('validatephone', 'validatephone.user_id', '=', 'leads.id')
                          ->select('leads.registrant_name as name','domains.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                          ->where('leadusers.user_id',$user_id)->get();  

          }else{
          $gt_ls_domaincount_no_downloadExcel=$request->gt_ls_domaincount_no_downloadExcel;
          $domaincount_no_downloadExcel=$request->domaincount_no_downloadExcel;
          $gt_ls_leadsunlocked_no_downloadExcel=$request->gt_ls_leadsunlocked_no_downloadExcel;
          $leadsunlocked_no_downloadExcel=$request->leadsunlocked_no_downloadExcel;  

          $filterOption_downloadExcel=$request->filterOption_downloadExcel;
          $create_date=$request->create_date_downloadExcel;
          $registrant_state=$request->registrant_state_downloadExcel;
          $tdl_com=$request->tdl_com_downloadExcel;
          $tdl_net=$request->tdl_net_downloadExcel;
          $tdl_org=$request->tdl_org_downloadExcel;
          $tdl_io=$request->tdl_io_downloadExcel;

          $cell_number=$request->cell_number_downloadExcel;
          $landline=$request->landline_downloadExcel;

          $phone_number=array();
          if($cell_number=='1'){
            $phone_number[]='Cell Number';
          }
          if($landline=='1'){
            $phone_number[]='Landline';
          }
          $tdl=array();
          if($tdl_com==1){
           $tdl[]='com'; 
          }
          if($tdl_net==1){
           $tdl[]='net'; 
          }
          if($tdl_org==1){
           $tdl[]='org'; 
          }
          if($tdl_io==1){
           $tdl[]='io'; 
          }
          

          if($gt_ls_domaincount_no_downloadExcel==0){
           $gt_ls_domaincount_no='>';
          }else if($gt_ls_domaincount_no_downloadExcel==1){
           $gt_ls_domaincount_no='>';
          }else{
           $gt_ls_domaincount_no='<';
          }
      

          if($gt_ls_leadsunlocked_no_downloadExcel==0){
           $gt_ls_leadsunlocked_no='>';
          }else if($gt_ls_leadsunlocked_no_downloadExcel==1){
           $gt_ls_leadsunlocked_no='>';
          }else{
           $gt_ls_leadsunlocked_no='<';
          }

         if($request->domaincount_no_downloadExcel){
          $domaincount_no=$request->domaincount_no_downloadExcel;
        }else {  $domaincount_no=0;    }
         $leadsunlocked_no=$request->leadsunlocked_no_downloadExcel;

        switch ($filterOption_downloadExcel) {
       
        case 1:
            $key='domainCount';
            $value='asc';
            break;
        case 2:
            $key='domainCount';
            $value='desc';
            break;
        case 3:
            $key='leads.unlocked_num';
            $value='asc';
            break;
        case 4:
            $key='leads.unlocked_num';
            $value='desc';
            break;
          default: 
          $key='domains.create_date';
          $value='desc';  
        }
          
         
          $registrant_country=$request->registrant_country_downloadExcel;
       
          $domain_name=$request->domain_name_downloadExcel;
          
          $requiredData=array();
          $leadusersData=array();
          $user_id=Auth::user()->id;


                 
            $exel_data = DB::table('leads')
                    ->join('domains', 'leads.id', '=', 'domains.user_id')
                    ->join('validatephone', 'validatephone.user_id', '=', 'leads.id')
                    ->select('leads.registrant_name as name','domains.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id',DB::raw('count(domains.user_id) as domainCount'))
                    
                    ->where(function($query) use ($create_date,$domain_name,$registrant_country,$phone_number,$tdl,$registrant_state,$leadsunlocked_no,$gt_ls_leadsunlocked_no)
                      {
                          if (!empty($registrant_country)) {
                              $query->where('leads.registrant_country', $registrant_country);
                          } 
                          if (!empty($create_date)) {
                              $query->where('domains.create_date', $create_date);
                          } 
                           if (!empty($leadsunlocked_no)) {
                              $query->where('leads.unlocked_num',$gt_ls_leadsunlocked_no, $leadsunlocked_no);
                          }
                          if (!empty($domain_name)) {
                             $query->where('domains.domain_name','like', '%'.$domain_name.'%');
                             
                          }
                          if(!empty($registrant_state))
                          {
                              $query->where('leads.registrant_state', $registrant_state);
                          }
                          if (!empty($phone_number)) {
                              $query->whereIn('validatephone.number_type', $phone_number);
                             
                          }
                           if (!empty($tdl)) {
                              $query->whereIn('domains.domain_ext', $tdl);
                             
                          }
                        
                      })
                 //->skip(0)
                 //->take(50)
                 ->groupBy('leads.registrant_email')
                  ->havingRaw('count(domains.user_id) '.$gt_ls_domaincount_no.''. $domaincount_no)
                 ->orderBy($key,$value)
                 
                 ->get();

          } 
        }else{
            $emailID_list=array();
            if(Session::has('emailID_list')){
                   $emailID_list=Session::get('emailID_list');
                   
            }
             Session::forget('emailID_list');
            //print_r($emailID_list);dd();
            $domains_for_export=$request->domains_for_export;
            //$domainsforexport=explode(",",$domains_for_export);
            //$req_domainsforexport=array();
             // foreach($domainsforexport as $val){
            //  $req_domainsforexport[]=$val; 
            //  }
              $exel_data=DB::table('each_domain')                         
                          ->join('leads', 'leads.registrant_email', '=', 'each_domain.registrant_email')
                          
                          ->select('leads.registrant_name as name','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                          ->whereIn('each_domain.registrant_email',$emailID_list)
                           ->groupBy('leads.registrant_email')
                          ->get();   
          //print_r($exel_data);dd();

        }




       $data = json_decode(json_encode($exel_data), true);
       //print_r($data);dd();
       $reqData=array();
       foreach($data as $key=>$result){
          $reqData[$key]['name']=$result['name'];
          $reqData[$key]['website']=$result['website'];
          $reqData[$key]['phone']=substr(strrchr($result['phone'], "."), 1);
          $reqData[$key]['email_id']=$result['email_id'];
       }
      // print_r($reqData);dd();
    return Excel::create('domainleads', function($excel) use ($reqData) {

      $excel->sheet('mySheet', function($sheet) use ($reqData)

          {

        $sheet->fromArray($reqData);

          });

    })->download($type);

  }
    
    public function chkWebsiteForDomain(Request $request){
   
     $domain_name= $request->domain_name;
     $registrant_email= $request->registrant_email;
     $user_id= $request->user_id;
     $client = new Client(); //GuzzleHttp\Client
     $client->setDefaultOption('verify', false);
                $result = $client->get('http://api.tier5.website/api/make_free_wp_website/'.$domain_name);
               $domain_data = json_decode($result->getBody()->getContents(), true);
             //  echo $domain_data['message'];
        
        $chkWebsite = new ChkWebsite();
        $chkWebsite->domain_name= $domain_name;  
        $chkWebsite->registrant_email= $registrant_email; 
        $chkWebsite->user_id= $user_id;  
        $chkWebsite->status= 1;  
        $chkWebsite->save();
        $array = array();  
        $array['message']    = $domain_data['message'];
        return \Response::json($array);     
      
    }
     public function storechkboxvariable(Request $request){
   
     $isChecked= $request->isChecked;
     $emailID= $request->emailID;
     $emailID_list=array();
     if(Session::has('emailID_list')){

                   $emailID_list=Session::get('emailID_list');
                   
                }
        if($isChecked){
         array_push($emailID_list,$emailID);
        }else{
          if (($key = array_search($emailID, $emailID_list)) !== false) {
          unset($emailID_list[$key]);
          }
        }
   
       Session::put('emailID_list', $emailID_list);
     
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
            //print_r($eachdomainArr);dd();

            foreach($eachdomainArr as $key=>$each)
            {
                if(isset($leadArr[$each]))
                {
                  $leadArr[$each]++;
                  $totalDomains++;
                }
            }

          // print_r($leadArr);dd();

            $user_id = \Auth::user()->id;

            //dd($leadArr_);

            $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
            

            $users_array = array_flip($users_array);


            $chkWebsite_array = ChkWebsite::where('user_id',$user_id)->pluck('registrant_email')->toArray();
            

            $chkWebsite_array = array_flip($chkWebsite_array);
           // print_r($chkWebsite_array);dd();
            //dd($users_array);

            
            // $tst = $allrecords->pluck('domains_count','registrant_email')->toArray();
            // foreach($tst as $key=>$val)
            // {
            //     if($leadArr[$key] != $tst[$key])
            //     dd("key=".$key."   val=".$val."   real count=".$leadArr[$key]."   domains_count=".$tst[$key]."<br>");
            // }

            // dd('over');
            
                //->paginate(100);
            //$en = microtime(true);
            //dd($en-$st);
            //dd($allrecords);

            


            return view('home.search' , 
                  ['record' => $allrecords->paginate($request->pagination), 
                  'leadArr'=>$leadArr , 
                  'totalDomains'=>$totalDomains,
                  'users_array'=>$users_array,
                  'chkWebsite_array'=>$chkWebsite_array]);
        }
        else
        {

          Session::forget('emailID_list');
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
