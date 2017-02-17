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
use \App\obj;
use \App\Wordpress_env;
use DB;
use Hash;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;
use Input;


class SearchController extends Controller
{   


public function downloadExcel2(Request $request)
{
  dd($request->all());
}

    public function downloadExcel(Request $request)

  {
    //dd($request->all());
    $type='csv'; 
      
      $user_type=Auth::user()->user_type;
      $user_id=Auth::user()->id;
        $domains_for_export_allChecked=$request->domains_for_export_allChecked;
        if($domains_for_export_allChecked==1){
          if($user_type==1){
            $exel_data=DB::table('leadusers')
                          ->join('leads', 'leads.registrant_email', '=', 'leadusers.registrant_email')
                          ->join('each_domain', 'each_domain.registrant_email', '=', 'leadusers.registrant_email')
                         
                          ->select('leads.registrant_name as name','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                          ->where('leadusers.user_id',$user_id)
                          ->groupBy('leads.registrant_email')
                          ->get();  

          }else{

            
          //$gt_ls_domaincount_no_downloadExcel=$request->gt_ls_domaincount_no_downloadExcel;
          //$domaincount_no_downloadExcel=$request->domaincount_no_downloadExcel;
          //$gt_ls_leadsunlocked_no_downloadExcel=$request->gt_ls_leadsunlocked_no_downloadExcel;
          //$leadsunlocked_no_downloadExcel=$request->leadsunlocked_no_downloadExcel;  

          $filterOption_downloadExcel=$request->filterOption_downloadExcel;
          $create_date=$request->domains_create_date_downloadExcel;
          $registrant_state=$request->registrant_state_downloadExcel;
          $tdl_com=$request->tdl_com_downloadExcel;
          $tdl_net=$request->tdl_net_downloadExcel;
          $tdl_org=$request->tdl_org_downloadExcel;
          $tdl_io=$request->tdl_io_downloadExcel;
          $tdl_gov=$request->tdl_gov_downloadExcel;
          $tdl_edu=$request->tdl_edu_downloadExcel;
          $tdl_in=$request->tdl_in_downloadExcel;

           $cell_number=$request->cell_number_downloadExcel;
           $landline=$request->landline_downloadExcel;

          $phone_number=array();
          if($cell_number=='cell number'){
            $phone_number[]='Cell Number';
          }
          if($landline=='landline number'){
            $phone_number[]='Landline';
          }
          //print_r($phone_number);dd();
          $tdl=array();
          if($tdl_com=='com'){
           $tdl[]='com'; 
          }
          if($tdl_net=='net'){
           $tdl[]='net'; 
          }
          if($tdl_org=='org'){
           $tdl[]='org'; 
          }
          if($tdl_io=='io'){
           $tdl[]='io'; 
          }
          if($tdl_gov=='gov'){
           $tdl[]='gov'; 
          }
          if($tdl_edu=='edu'){
           $tdl[]='edu'; 
          }
          if($tdl_in=='in'){
           $tdl[]='in'; 
          }
          

         // if($gt_ls_domaincount_no_downloadExcel==0){
          // $gt_ls_domaincount_no='>';
         // }else if($gt_ls_domaincount_no_downloadExcel==1){
          // $gt_ls_domaincount_no='>';
         // }else{
         //  $gt_ls_domaincount_no='<';
          //}
      

         // if($gt_ls_leadsunlocked_no_downloadExcel==0){
          // $gt_ls_leadsunlocked_no='>';
         // }else if($gt_ls_leadsunlocked_no_downloadExcel==1){
          // $gt_ls_leadsunlocked_no='>';
        //  }else{
          // $gt_ls_leadsunlocked_no='<';
          //}

         //if($request->domaincount_no_downloadExcel){
          //$domaincount_no=$request->domaincount_no_downloadExcel;
        //}else {  $domaincount_no=0;    }
        // $leadsunlocked_no=$request->leadsunlocked_no_downloadExcel;

        switch ($filterOption_downloadExcel) {
       
        case 'unlocked_asnd':
            $key='leads.unlocked_num';
            $value='asc';
            break;
        case 'unlocked_dcnd':
            $key='leads.unlocked_num';
            $value='desc';
            break;
        case 'domain_count_asnd':
            $key='leads.domains_count';
            $value='asc';
            break;
        case 'domain_count_dcnd':
            $key='leads.domains_count';
            $value='desc';
            break;
          default: 
          $key='domains_info.domains_create_date';
          $value='desc';  
        }
          
         
          $registrant_country=$request->registrant_country_downloadExcel;
       
          $domain_name=$request->domain_name_downloadExcel;
          
          $requiredData=array();
          $leadusersData=array();
          $user_id=Auth::user()->id;


                 //dd($phone_number);
            $exel_data = DB::table('leads')
                    ->join('each_domain', 'leads.registrant_email', '=', 'each_domain.registrant_email')
                    //->join('valid_phone', 'valid_phone.registrant_email', '=', 'leads.registrant_email')
                      
                    ->join('domains_info', 'domains_info.domain_name', '=', 'each_domain.domain_name')
                    ->select('leads.registrant_name as name','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                      

                    ->where(function($query) use ($create_date,$domain_name,$registrant_country,$phone_number,$tdl,$registrant_state)
                      {
                        if (!empty($phone_number)) {
                          

                            $query->whereExists(function ($query) use($phone_number) {
                            $query->select(DB::raw(1))
                            ->from('valid_phone')
                            ->whereRaw('valid_phone.registrant_email = leads.registrant_email')
                            ->whereIn('valid_phone.number_type', $phone_number); 
                            });
                            
                             
                          }
                          if (!empty($registrant_country)) {
                              $query->where('leads.registrant_country', $registrant_country);
                          } 
                          if (!empty($create_date)) {
                              $query->where('domains_info.domains_create_date', $create_date);
                          } 
                         //  if (!empty($leadsunlocked_no)) {
                             // $query->where('leads.unlocked_num',$gt_ls_leadsunlocked_no, $leadsunlocked_no);
                          //}
                          if (!empty($domain_name)) {
                             $query->where('each_domain.domain_name','like', '%'.$domain_name.'%');
                             
                          }
                          if(!empty($registrant_state))
                          {
                              $query->where('leads.registrant_state', $registrant_state);
                          }
                          
                          //dd($query);
                           if (!empty($tdl)) {
                              $query->whereIn('each_domain.domain_ext', $tdl);
                             
                          }
                        
                      })
                 //->skip(0)
                 //->take(50)

                 ->groupBy('each_domain.registrant_email')
                 // ->havingRaw('count(domains.user_id) '.$gt_ls_domaincount_no.''. $domaincount_no)
                 //->orderBy($key,$value)
                 
                 ->get();


             // dd($exel_data);      

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
    
    public function createWordpressForDomain(Request $request)
    {
   
    //dd($request->domain_name);
     $domain_name= $request->domain_name;
     $registrant_email= $request->registrant_email;
     $user_id= $request->user_id;
     $client = new Client(); //GuzzleHttp\Client
     $client->setDefaultOption('verify', false);
                $result = $client->get('http://api.tier5.website/api/make_free_wp_website/'.$domain_name);
               $domain_data = json_decode($result->getBody()->getContents(), true);
             //  echo $domain_data['message'];
        
        $obj = new Wordpress_env();
        $obj->domain_name= $domain_name;  
        $obj->registrant_email= $registrant_email; 
        $obj->user_id= $user_id;  
        $obj->status= 1;  
        $obj->save();
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
    
     public function removeChkedEmailfromSession(Request $request){
   
      Session::forget('emailID_list');
     }
    public function lead_domains($email)
    {
      $email = decrypt($email);
      $alldomains = EachDomain::with('wordpress_env')->where('registrant_email',$email);
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

          
          //initiating MY VARIABLES
          $phone_type_array = array();

          if($request->landline_number)
            array_push($phone_type_array,'Landline');

          if($request->cell_number)
            array_push($phone_type_array,'Cell Number');

          if(isset($request->domain_ext) && sizeof($request->domain_ext)>0)
            $domain_ext = $request->domain_ext;



          $allrecords = Lead::with('each_domain','valid_phone');
          //initiating ends

          
          
      
            $st = microtime(true);
            //print_r($request->all());dd();
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
                    else if($key == 'domain_ext')
                    {
                        
                        $allrecords = $allrecords->whereHas('each_domain' , function($query) use($key,$req,$domain_ext){
                            $query->whereIn($key,$domain_ext);
                        });
                    }
                    else if($key == 'domains_create_date')
                    {
                        $allrecords = $allrecords->whereHas('each_domain' , function($query) use($key,$req){
                            $query->whereHas('domains_info',function($q) use($key,$req){
                              
                                $q->where($key,$req);
                            });
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
                            $allrecords = $allrecords->orderBy('domains_count','asc');
                        }
                        else if($req == 'domain_count_dcnd') 
                        {
                            $allrecords = $allrecords->orderBy('domains_count','desc');
                        }
                    }
                    else if($key=='gt_ls_leadsunlocked_no'){
                            if($req==0){
                              $gt_ls_leadsunlocked_no='>';

                            }else if($req==1){
                              $gt_ls_leadsunlocked_no='>';
                            }
                            else{
                              $gt_ls_leadsunlocked_no='<';
                            }

                     }
                    else if($key == 'leadsunlocked_no')
                     {

                        if($req==''){
                          $req='0';
                        } 
                        $allrecords = $allrecords->where('unlocked_num',$gt_ls_leadsunlocked_no, $req);
                        
                      
                     }
                     else if($key=='gt_ls_domaincount_no'){
                            if($req==0){
                              $gt_ls_domaincount_no='>';

                            }else if($req==1){
                              $gt_ls_domaincount_no='>';
                            }
                            else{
                              $gt_ls_domaincount_no='<';
                            }

                     }
                    else if($key == 'domaincount_no')
                     {

                        if($req==''){
                          $req='0';
                        } 
                        $allrecords = $allrecords->where('domains_count',$gt_ls_domaincount_no, $req);
                         
                      
                     }
                }

            }

          
          if(isset($phone_type_array) && sizeof($phone_type_array)>0)
          {
            $allrecords = $allrecords->whereHas('valid_phone' , function($query) use($phone_type_array){
              
                  $query->whereIn('number_type',$phone_type_array);
                  
              
            });
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


          

            $user_id = \Auth::user()->id;


            // dd($allrecords);

            // $A = $allrecords->whereHas('each_domain' , function($query) use($key,$req){
            //     $query->whereHas('valid_phone',function($q){
                    
            //         $q->where('number_type','Landline');
            //     });
            // });

            // dd($A);

            // $B = $allrecords->whereHas('each_domain' , function($query) use($key,$req){
            //     $query->whereHas('valid_phone',function($q){
                    
            //         $q->where('number_type','Cell Number');
            //     });
            // });

            // $a = $A->pluck('registrant_email')->toArray();

            // $b = $B->pluck('registrant_email')->toArray();

            // dd($a);
            // dd($b);
















           
            

            $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
            

            $users_array = array_flip($users_array);

            $obj_array = Wordpress_env::where('user_id',$user_id)->pluck('registrant_email')->toArray(); 

            $obj_array = array_flip($obj_array);

            

            if(\Auth::user()->user_type == 2)
            {
                return view('home.admin.admin_search',[

                    'record' => $allrecords->paginate($request->pagination), 
                    'leadArr'=>$leadArr , 
                    'totalDomains'=>$totalDomains,
                    'users_array'=>$users_array
                  ]);      
            }


            return view('home.search' , 
                  ['record' => $allrecords->paginate($request->pagination), 
                  'leadArr'=>$leadArr , 
                  'totalDomains'=>$totalDomains,
                  'users_array'=>$users_array,
                  'obj_array'=>$obj_array]);
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
