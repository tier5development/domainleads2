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
use \App\CSV;
use \App\SearchMetadata;
use DB;
use Hash;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;
use Input;
use Illuminate\Pagination\Paginator;


class SearchController extends Controller
{   


public $phone_type_array = array();
public $domain_ext_arr   = array();
public $domain_ext_str   = ' ';
public $totalLeads;
public $totalDomains;
public $totalPage;
public $meta_id;
 
public function downloadExcel2(Request $request)
{
  dd($request->all());
}

public function print_csv($leads,$type)
{
  $ed = EachDomain::with('leads')
            ->whereIn('registrant_email',$leads)
            ->whereHas('leads',function($query) use($leads){
              $query->whereIn('registrant_email',$leads);
            })->get();


      $reqData = array();
      $key=0;
      $hash = array();
      foreach($ed as $each)
      {
        if(!isset($hash[$each->registrant_email]))
        {
          $hash[$each->registrant_email] = 1;
          $reqData[$key]['first_name'] = $each->leads->registrant_fname;
          $reqData[$key]['last_name']  = $each->leads->registrant_lname;
          $reqData[$key]['website']    = $each->domain_name;
          $reqData[$key]['phone']      = $each->leads->registrant_phone;
          $reqData[$key++]['email_id'] = $each->registrant_email;
        }
      }

      return Excel::create('domainleads', function($excel) use ($reqData) {

        $excel->sheet('mySheet', function($sheet) use ($reqData){
          $sheet->fromArray($reqData);
        });
      })->download($type);
}

public function download_csv_single_page(Request $request)
{
  $type = 'csv';
  if($request->exportLeads)
  {
      $leads = array();
      if($request->csv_leads)
      {
        $i = 0;
        foreach($request->csv_leads as $key=>$val) $leads[$i++] = $val;
        $this->print_csv($leads,$type);  
      }
      else
      {
        \Session::put('csv_msg','Please Select Some Leads and then Export..!');
        return \Redirect::back();
      }
  }
  else
  {
    $this->print_csv(unserialize($request->all_leads_to_export[0]),$type);

    return;
  }
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
                         
                          ->select('leads.registrant_fname as fname','leads.registrant_lname as lname','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
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
                    ->select('leads.registrant_fname as fname','leads.registrant_lname as lname','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                      

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
                          
                          ->select('leads.registrant_fname as fname','leads.registrant_lname as lname','each_domain.domain_name as website','leads.registrant_address as address','leads.registrant_phone as phone','leads.registrant_email as email_id')
                          ->whereIn('each_domain.registrant_email',$emailID_list)
                           ->groupBy('leads.registrant_email')
                          ->get();   
          //print_r($exel_data);dd();

        }




       $data = json_decode(json_encode($exel_data), true);
       //print_r($data);dd();
       $reqData=array();
       foreach($data as $key=>$result){
          //$reqData[$key]['name']=$result['name'];
          // $name_ = explode(" ",$result['name']);

          // $name_[0] = isset($name_[0]) ? $name_[0] : " ";
          // $name_[1] = isset($name_[1]) ? $name_[1] : " ";

          $reqData[$key]['first_name'] = $result['fname'];
          $reqData[$key]['last_name']  = $result['lname'];
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
   
    //dd($request->all());

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
        $array = array();  
        $array['message']    = $domain_data['message'];


        // $IP = env('TR5IP');
        // $ip = gethostbyname($request->domain_name);
        // $array['created'] = $ip != $IP ? 'false' : 'true';
        // $array['created'] == 'false' ? $obj->status = 1 : $obj->status = 2;
        $array['error'] = 'null';
        if($obj->save())
        {
          return \Response::json($array); 
        }
        else
        {
          $array['error'] = 'cannot insert into db';
          return \Response::json($array); 
        }
            
      
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
          $array['id']     = $data->id;
          $array['registrant_email']    = $data->registrant_email;
          $array['registrant_name']     = $data->registrant_fname." ".$data->registrant_lname;
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

    private function raw_leads($leads_str)
    {
      $sql = "SELECT DISTINCT l.registrant_email 
      , l.id
      , l.registrant_fname 
      , l.registrant_lname 
      , l.registrant_country 
      , l.registrant_company 
      , l.registrant_phone 
      , l.registrant_state 
      , l.domains_count 
      , l.unlocked_num 
      FROM leads as l
      WHERE l.id IN ".$leads_str;
      $leads = DB::select(DB::raw($sql));
      return $leads;
    }

    public function ajax_search_paginated(Request $request)
    {
      //dd($request->all());
      $start = microtime(true);
      $sql    = "SELECT leads,compression_level from search_metadata 
                where id = ".$request->meta_id;
      $data   = DB::select(DB::raw($sql)); 
      $leads  = $this->uncompress($data[0]->leads,$data[0]->compression_level);
      $data   = null;

      $leads  = $this->raw_leads("(".$leads.")");

      $array  = $this->leadsPerPage_Search($request->thisPage,$request->pagination,$leads);
      $param  = ['domain_name'=>$request->domain_name
               ,'domain_ext' =>$request->domain_ext
               ,'domains_create_date'=>$request->domains_create_date
               ,'domains_create_date2'=>$request->domains_create_date2];
      $data   = $array['data'];
      $domains= $this->domainsPerPage_Search($param,$request->phone_type_array,$array['leads_string']);


      $domain_list = array();
      foreach($data as $k=>$v) // setting up the leads array
        $domain_list[$v['registrant_email']]['checked'] = false;
      
      foreach($domains as $k=>$v)
      {
        if(!($domain_list[$v->registrant_email]['checked']))
        {
          $domain_list[$v->registrant_email]['checked'] = true;
          $domain_list[$v->registrant_email]['domain_name'] = $v->domain_name;
          $domain_list[$v->registrant_email]['domain_ext']  = $v->domain_ext;
          $domain_list[$v->registrant_email]['domains_create_date'] = $v->domains_create_date;
          $domain_list[$v->registrant_email]['number_type'] = $v->number_type;
        }
      }

      foreach ($data as $key => $value) 
      {
        $data[$key]['domain_name'] = $domain_list[$value['registrant_email']]['domain_name'];
        $data[$key]['domain_ext']  = $domain_list[$value['registrant_email']]['domain_ext'];
        $data[$key]['domains_create_date'] = $domain_list[$value['registrant_email']]['domains_create_date'];
        $data[$key]['number_type'] = $domain_list[$value['registrant_email']]['number_type'];
      }
      //dd($data);
      $time = microtime(true) - $start;
      return \Response::json(array('data'=>$data , 'time'=>$time));

    }

    public function search_paginated(Request $request)
    {

      $start = microtime(true);
      $leads = $this->raw_leads($request->lead_list);

      $i=0;
      $x=0;
      $data = array();
      $leads_string = ' ';
      foreach($leads as $each)
      {
        $i++; 
        
        $data[$x]['registrant_email']   = $each->registrant_email;
        $data[$x]['registrant_name']               = $each->registrant_fname.' '.$each->registrant_lname;
        $data[$x]['registrant_country'] = $each->registrant_country;
        $data[$x]['registrant_company'] = $each->registrant_company;
        $data[$x]['registrant_phone']   = $each->registrant_phone;
        $data[$x]['unlocked_num']       = $each->unlocked_num;
        $data[$x]['domains_count']      = $each->domains_count;
        $data[$x++]['registrant_state'] = $each->registrant_state;
        if($leads_string == ' ')
        {
           $leads_string .= "'".$each->registrant_email."'";
        }
        else
        {
           $leads_string .= ",'".$each->registrant_email."'";
        }
              
      }

      $leads_string= '('.$leads_string.')';

      $sql = " SELECT ed.domain_name, ed.domain_ext, ed.registrant_email ,di.domains_create_date,vp.number_type FROM `each_domain` ed 
              INNER JOIN domains_info as di ON di.domain_name = ed.domain_name 
              INNER JOIN valid_phone as vp ON vp.registrant_email = ed.registrant_email 
              WHERE ed.registrant_email 
              IN ".$leads_string;



      $date_flag = 0;
             // domain_name , domain_ext , domains_create_date , domains_create_date2
      foreach ($request->all() as $key=>$req) 
      {
        if(!is_null($req))
        {
          if($key == 'domain_name')
          {
            $sql .= " and ed.domain_name LIKE '%".$req."%' "; 
          }
          else if($key == 'domain_ext')
          {
            $sql .= " and ed.domain_ext IN ".$domain_ext_str." "; 
          }
          else if(($key == 'domains_create_date' || $key == 'domains_create_date2') 
              && $date_flag == 0)
          {
                $date_flag = 1;
                $dates_array = generateDateRange(
                          $request->domains_create_date,
                          $request->domains_create_date2);
                
            if(isset($dates_array))
            {
              if(sizeof($dates_array) == 1)
              {
                $sql .= " and di.domains_create_date = '".$dates_array[0]."' "; 
              }
              else if(sizeof($dates_array) == 2)
              {
                $sql .= " and di.domains_create_date >= '".$dates_array[0]."' and di.domains_create_date <= '".$dates_array[1]."'";
              }
            }
          }
        }
      }



      if(isset($phone_type_array))
      {
        $phone_type_array_str = ' ';
        foreach($phone_type_array as $k=>$v)
        {
          if($v == ' ')
            $phone_type_array_str .= "'".$v."'";
          else
            $phone_type_array_str .= ",'".$v."'";
        }
        if($phone_type_array_str != ' ')
        {
          $phone_type_array_str = "(".$phone_type_array_str.")";
          $sql .= " and vp.number_type IN ".$phone_type_array_str;
        }
      }

      //dd($sql);

      $domains = DB::select(DB::raw($sql));
      $domain_list = array();

      foreach($data as $k=>$v) // setting up the leads array
      {
        $domain_list[$v['registrant_email']]['checked'] = false;
      }

      foreach($domains as $k=>$v)
      {
        if(!($domain_list[$v->registrant_email]['checked']))
        {
          $domain_list[$v->registrant_email]['checked'] = true;
          $domain_list[$v->registrant_email]['domain_name'] = $v->domain_name;
          $domain_list[$v->registrant_email]['domain_ext']  = $v->domain_ext;
          $domain_list[$v->registrant_email]['domains_create_date'] = $v->domains_create_date;
          $domain_list[$v->registrant_email]['number_type'] = $v->number_type;
        }
      }
      dd($domain_list);
      return \Response::json($domain_list);

      $set = array();

      foreach ($data as $key => $value) 
      {

        $data[$key]['domain_name'] = $domain_list[$value['registrant_email']]['domain_name'];
        $data[$key]['domain_ext']  = $domain_list[$value['registrant_email']]['domain_ext'];
        $data[$key]['domains_create_date'] = $domain_list[$value['registrant_email']]['domains_create_date'];
        $data[$key]['number_type'] = $domain_list[$value['registrant_email']]['number_type'];
      }
      //dd($data);
      $time = microtime(true) - $start;
      return \Response::json(array('data'=>$data , 'time'=>$time));
    }

    

    private function leads_Search(Request $request)
    {
      $date_flag = 0;
      $phone_type_array = $this->phone_type_array;
      $domain_ext_str   = $this->domain_ext_str;
      $sql = "SELECT DISTINCT l.registrant_email 
            , l.id
            , l.registrant_fname 
            , l.registrant_lname 
            , l.registrant_country 
            , l.registrant_company 
            , l.registrant_phone 
            , l.registrant_state 
            , l.domains_count 
            , l.unlocked_num 

            FROM leads as l INNER JOIN each_domain AS ed 
            ON ed.registrant_email = l.registrant_email
            INNER JOIN domains_info AS di 
            ON di.domain_name = ed.domain_name 
            INNER JOIN valid_phone AS vp 
            ON vp.registrant_email = l.registrant_email 
            WHERE l.registrant_email != '' ";


            $flag = 0;
            foreach ($request->all() as $key => $req) 
            {
               if(!is_null($request->$key))
              {
                if($key == 'registrant_country')
                {
                    $sql .= " and l.registrant_country='".$req."' ";
                }
                else if($key == 'registrant_state')
                {
                    $sql .= " and l.registrant_state='".$req."' ";
                }
                else if($key == 'domain_name')
                {
                    $sql .= " and ed.domain_name LIKE '%".$req."%' "; 
                }
                else if($key == 'domain_ext')
                {
                    $sql .= " and ed.domain_ext IN ".$domain_ext_str." ";  
                }
                else if(($key == 'domains_create_date' || $key == 'domains_create_date2') 
                  && $date_flag == 0)
                {
                    $date_flag = 1;
                    $dates_array = generateDateRange($request->domains_create_date,
                                    $request->domains_create_date2);
                    
                    if(isset($dates_array))
                    {
                      if(sizeof($dates_array) == 1) $sql .= " and di.domains_create_date = '"
                                                         .$dates_array[0]."' ";

                      else if(sizeof($dates_array) == 2) $sql .= " and di.domains_create_date >= '"
                                                        .$dates_array[0]."' and di.domains_create_date <= '"
                                                        .$dates_array[1]."'";
                    }
                }
                else if($key=='gt_ls_leadsunlocked_no')
                {
                    if($req == 0)      $gt_ls_leadsunlocked_no='';
                    else if($req == 1) $gt_ls_leadsunlocked_no='>';
                    else if($req == 2) $gt_ls_leadsunlocked_no='<';
                    else if($req == 3) $gt_ls_leadsunlocked_no='=';
                }
                else if($key == 'leadsunlocked_no')
                {
                    if($gt_ls_leadsunlocked_no == '') continue;
                    if($req == '')  $req=0;
                    $sql .= " and l.unlocked_num ".$gt_ls_leadsunlocked_no." ".$req; 
                }
                else if($key=='gt_ls_domaincount_no')
                {
                    if($req==0)        $gt_ls_domaincount_no='';
                    else if($req == 1) $gt_ls_domaincount_no='>';
                    else if($req == 2) $gt_ls_domaincount_no='<';
                    else if($req == 3) $gt_ls_domaincount_no='=';
                }
                else if($key == 'domaincount_no')
                {
                    if($gt_ls_domaincount_no == '') continue;
                    if($req=='') $req = 0;
                    $sql .= " and l.domains_count ".$gt_ls_domaincount_no." ".$req;
                }
              }
            }
            if(isset($phone_type_array))
            {
              $phone_type_array_str = ' ';
              foreach($phone_type_array as $k=>$v)
              {
                if($phone_type_array_str == ' ') $phone_type_array_str .= "'".$v."'";
                else                             $phone_type_array_str .= ",'".$v."'";
              }
              if($phone_type_array_str != ' ')
              {
                $phone_type_array_str = "(".$phone_type_array_str.")";
                $sql .= " and vp.number_type IN ".$phone_type_array_str;
              }
            }
            if(isset($request->sort)) 
            {
                $req = $request->sort;
                if($req == 'unlocked_asnd')  $sql .= " ORDER BY l.unlocked_num ASC ";   
                else if($req == 'unlocked_dcnd') $sql .= " ORDER BY l.unlocked_num DESC ";
                else if($req == 'domain_count_asnd')  $sql .= " ORDER BY l.domains_count ASC ";
                else if($req == 'domain_count_dcnd')  $sql .= " ORDER BY l.domains_count DESC";
            }
            //echo($sql);exit();
            $leads = DB::select(DB::raw($sql));
            return $leads;
    }

    private function leadsPerPage_Search($page,$pagination,$leads)
    {
        $leads_string = '';
        $totalLeads = sizeof($leads);
        $leadsid_per_page   = array(); 
        $i=$x=$z=$totalDomains=$lastz=0;
        $data = array();

        $low =($page-1)*$pagination;
        $high=$low + $pagination;
        //dd($low.' '.$high);
        for($i=$low ; $i<$high ; $i++)
        {
          if(isset($leads[$i]))
          {
            //dd($leads[$i]);
            $data[$x]['id']                 = $leads[$i]->id;
            $data[$x]['registrant_email']   = $leads[$i]->registrant_email;
            $data[$x]['registrant_name']               = $leads[$i]->registrant_fname.' '.$leads[$i]->registrant_lname;
            $data[$x]['registrant_country'] = $leads[$i]->registrant_country;
            $data[$x]['registrant_company'] = $leads[$i]->registrant_company;
            $data[$x]['registrant_phone']   = $leads[$i]->registrant_phone;
            $data[$x]['unlocked_num']       = $leads[$i]->unlocked_num;
            $data[$x]['domains_count']      = $leads[$i]->domains_count;
            $data[$x++]['registrant_state'] = $leads[$i]->registrant_state;

            if($leads_string == '') $leads_string .= "'".$leads[$i]->registrant_email."'";
            else                     $leads_string .= ",'".$leads[$i]->registrant_email."'";
          }
        }
        $leads_string = "(".$leads_string.")";
        //dd($totalLeads);
        //dd($pagination);
        //dd(($totalLeads%$pagination) == 0 ? 0 : 1);
        $pages    = (int)($totalLeads/$pagination) ;
        $lastpage = ($totalLeads%$pagination) == 0 ? 0 : 1;
        $this->totalPage =  $pages+$lastpage;
        //dd($pages+$lastpage);
        //dd($this->totalPage);
        return array('data'=>$data,'leads_string'=>$leads_string);

        //return $leads_string;
    }
    private function domainsPerPage_Search($param, $phone_type_array ,$leads_string)
    {
      //dd($param);
      //dd($phone_type_array);
      //dd($leads_string);
      $phone_type_array = $this->phone_type_array;
      $domain_ext_str   = $this->domain_ext_str;
      $date_flag = 0;

      if($leads_string != '()')
      {
        $sql = " SELECT ed.domain_name, ed.domain_ext, ed.registrant_email ,di.domains_create_date,vp.number_type FROM `each_domain` ed 
        INNER JOIN domains_info as di ON di.domain_name = ed.domain_name 
        INNER JOIN valid_phone as vp ON vp.registrant_email = ed.registrant_email 
        WHERE ed.registrant_email 
        IN ".$leads_string;
              //$flag = 0;
             
        /*
        param :
              domain_name
              domain_ext
              domains_create_date
              domains_create_date2
        
        phone_type_array
        */
        $domain_ext_str = '';

        if(isset($param['domain_ext']))
        {
          foreach($param['domain_ext'] as $k => $v)
          {
            if($domain_ext_str == '') 
              $domain_ext_str .= "'".$v."'";
            else
              $domain_ext_str .= ",'".$v."'";
          }
          $domain_ext_str = '('.$domain_ext_str.')';
        }
        //dd($domain_ext_str);


        foreach ($param as $key=>$req) 
        {
          if(!is_null($req))
          {
            if($key == 'domain_name')
              $sql .= " and ed.domain_name LIKE '%".$req."%' "; 
            
            else if($key == 'domain_ext' && $domain_ext_str != '()')
              $sql .= " and ed.domain_ext IN ".$domain_ext_str." ";

            else if(($key == 'domains_create_date' || $key == 'domains_create_date2') 
                && $date_flag == 0)
            {
                  $date_flag = 1;
                  $dates_array = generateDateRange(
                            $param['domains_create_date'],
                            $param['domains_create_date2']);
                  
              if(isset($dates_array))
              {
                if(sizeof($dates_array) == 1) $sql .= " and di.domains_create_date = '"
                                                   .$dates_array[0]."' "; 
                
                else if(sizeof($dates_array) == 2) $sql .= " and di.domains_create_date >= '"
                                                        .$dates_array[0]
                                                        ."' and di.domains_create_date <= '"
                                                        .$dates_array[1]."'";
              }
            }
          }
        }
        // echo $sql;
        // dd('---');

        if(isset($phone_type_array) && !is_null($phone_type_array))
        {
          $phone_type_array_str = ' ';
          foreach($phone_type_array as $k=>$v)
          {
            if($phone_type_array_str == ' ') $phone_type_array_str .= "'".$v."'";
            else                             $phone_type_array_str .= ",'".$v."'";
          }
          if($phone_type_array_str != ' ')
          {
            $phone_type_array_str = "(".$phone_type_array_str.")";
            $sql .= " and vp.number_type IN ".$phone_type_array_str;
          }
        }
        $domains = DB::select(DB::raw($sql));
        return $domains;
      }
      return null;
      


        // foreach ($request->all() as $key=>$req) 
        // {
        //   if(!is_null($req))
        //   {
        //     if($key == 'domain_name')
        //     {
        //       $sql .= " and ed.domain_name LIKE '%".$req."%' "; 
        //     }
        //     else if($key == 'domain_ext')
        //     {
        //       $sql .= " and ed.domain_ext IN ".$domain_ext_str." "; 
        //     }
        //     else if(($key == 'domains_create_date' || $key == 'domains_create_date2') 
        //         && $date_flag == 0)
        //     {
        //           $date_flag = 1;
        //           $dates_array = generateDateRange(
        //                     $request->domains_create_date,
        //                     $request->domains_create_date2);
                  
        //       if(isset($dates_array))
        //       {
        //         if(sizeof($dates_array) == 1) $sql .= " and di.domains_create_date = '"
        //                                            .$dates_array[0]."' "; 
                
        //         else if(sizeof($dates_array) == 2) $sql .= " and di.domains_create_date >= '"
        //                                                 .$dates_array[0]
        //                                                 ."' and di.domains_create_date <= '"
        //                                                 .$dates_array[1]."'";
        //       }
        //     }
        //   }
        // }
        // // echo $sql;
        // // dd('---');

        // if(isset($phone_type_array))
        // {
        //   $phone_type_array_str = ' ';
        //   foreach($phone_type_array as $k=>$v)
        //   {
        //     if($phone_type_array_str == ' ') $phone_type_array_str .= "'".$v."'";
        //     else                             $phone_type_array_str .= ",'".$v."'";
        //   }
        //   if($phone_type_array_str != ' ')
        //   {
        //     $phone_type_array_str = "(".$phone_type_array_str.")";
        //     $sql .= " and vp.number_type IN ".$phone_type_array_str;
        //   }
        // }
        // $domains = DB::select(DB::raw($sql));
        // return $domains;
    }

    private function estimated_input_fields()
    {
      //'cell_number','landline_number' -> condensed into phone_type
      return ['domain_name'=>false
              ,'registrant_country'=>false
              ,'registrant_state'=>false
              ,'domains_create_date'=>false
              ,'domains_create_date2'=>false
              ,'number_type'=>false
              ,'pagination'=>false
              ,'sort'=>false
              ,'gt_ls_domaincount_no'=>false
              ,'domaincount_no'=>false
              ,'gt_ls_leadsunlocked_no'=>false
              ,'leadsunlocked_no'=>false];
    }

    private function checkMetadata_Search(Request $request)
    {
      //dd($request->all());
      $input = $this->estimated_input_fields();
      $date_flag = 0;
      $phone_type_meta  = '('.implode(',',$this->phone_type_array).')';
      $domain_ext_meta  = '('.implode(',',$this->domain_ext_arr).')';
      $dates_array = array();
      $domains_count_operator = '';
      $leads_unlocked_operator= '';

      switch($request->gt_ls_leadsunlocked_no)
      {
          case 0 : $gt_ls_leadsunlocked_no=''; break;
          case 1 : $gt_ls_leadsunlocked_no='>'; break;
          case 2 : $gt_ls_leadsunlocked_no='<'; break;
          case 3 : $gt_ls_leadsunlocked_no='='; break;
          default: break;
      }

      switch ($request->gt_ls_domaincount_no) 
      {
          case 0 : $gt_ls_domaincount_no=''; break;
          case 1 : $gt_ls_domaincount_no='>'; break;
          case 2 : $gt_ls_domaincount_no='<'; break;
          case 3 : $gt_ls_domaincount_no='='; break;
          default: break;
      }


      $sql = "SELECT id,leads,compression_level,`totalLeads`,`totalDomains`,updated_at from search_metadata WHERE leads != '' ";
      foreach ($request->all() as $key => $req) 
      {
        if(!is_null($request->$key))
        {
          if($key == 'registrant_country')
          {
              $sql .= " and registrant_country='".$req."' ";
              $input[$key] = true;
          }
          else if($key == 'registrant_state')
          {
              $sql .= " and registrant_state='".$req."' ";
              $input[$key] = true;
          }
          else if($key == 'domain_name')
          {
              $sql .= " and domain_name = '".$req."'"; 
              $input[$key] = true;
          }
          else if($key == 'domain_ext')
          {   
            if($domain_ext_meta != '()')
            {
              $sql .= " and domain_ext = '".$domain_ext_meta."'";  
              $input[$key] = true;
            }
          }
          else if(($key == 'domains_create_date' || $key == 'domains_create_date2') 
            && $date_flag == 0)
          {
              $date_flag = 1;
              $dates_array = generateDateRange($request->domains_create_date,
                              $request->domains_create_date2);
              
              if(isset($dates_array))
              {
                if(sizeof($dates_array) == 1)
                { 
                  $sql .= " and domains_create_date1 = '".$dates_array[0]."' ";
                  $input['domains_create_date'] = true;
                }
                else if(sizeof($dates_array) == 2)
                {
                  $sql .= " and domains_create_date1 = '".$dates_array[0]."' and domains_create_date2 = '".$dates_array[1]."'";
                  $input['domains_create_date'] = true;
                  $input['domains_create_date2'] = true;
                }
              }
          }
          // else if($key=='gt_ls_leadsunlocked_no')
          // {
          //     $sql .= " and leads_unlocked_operator = '".$gt_ls_leadsunlocked_no."'"; 
          // }
          else if($key == 'leadsunlocked_no')
          {
              if($gt_ls_leadsunlocked_no == '') continue;
              else if($gt_ls_leadsunlocked_no != '' && is_null($req)) continue;
              if($req == '')  $req=0;
              $sql .= " and unlocked_num = ".$req." and leads_unlocked_operator = '".$gt_ls_leadsunlocked_no."'"; 
              $input[$key] = true;
              $input['gt_ls_leadsunlocked_no'] = true;
          }
          // else if($key=='gt_ls_domaincount_no')
          // {
          //     $sql .= " and domains_count_operator = '".$gt_ls_leadsunlocked_no."'"; 
          // }
          else if($key == 'domaincount_no')
          {
              if($gt_ls_domaincount_no == '') continue;
              else if($gt_ls_domaincount_no != '' && is_null($req)) continue;
              if($req=='') $req = 0;
              $sql .= " and domains_count = ".$req." and domains_count_operator = '".$gt_ls_leadsunlocked_no."'";
              $input[$key] = true;
              $input['gt_ls_domaincount_no'] = true;
          }
        }
      }

      if($phone_type_meta != '()')
      {
        $input['number_type'] = true;
        $sql .= " and number_type = '".$phone_type_meta."'"; 
      }

      if(isset($request->sort) && !is_null($request->sort))
      {
        $input['sort'] = true;
        $req = $request->sort;

        foreach ($input as $key=>$value) 
        {
          if(!$value)
          {
            switch($key)
            {
              case 'domain_name': $sql .= " and domain_name IS NULL";
                    break;     
              case 'registrant_country': $sql .= " and registrant_country IS NULL";
                    break;
              case 'registrant_state' : $sql .= " and registrant_state IS NULL";
                    break;
              case 'domains_create_date' : 
                    if(!$input['domains_create_date2'])
                    $sql .= " and domains_create_date1 IS NULL and domains_create_date2 IS NULL";
                    else
                    $sql .= " and domains_create_date1 IS NULL";
                    break;
              case 'number_type' : $sql .=" and number_type IS NULL";
                    break;
              case 'gt_ls_domaincount_no' : $sql .= " and domains_count_operator IS NULL";
                    break;
              case 'domaincount_no' : $sql .= " and domains_count IS NULL";
                    break;
              case 'gt_ls_leadsunlocked_no' : $sql .=" and leads_unlocked_operator IS NULL";
                    break;
              case 'leadsunlocked_no' : $sql .= " and unlocked_num IS NULL";
                    break;
              default: break;
            }
          }
        }

        $sql .= " and sortby = '".$req."'";

        // if($req == 'unlocked_asnd')  $sql .= " ORDER BY unlocked_num ASC ";   
        // else if($req == 'unlocked_dcnd') $sql .= " ORDER BY unlocked_num DESC ";
        // else if($req == 'domain_count_asnd')  $sql .= " ORDER BY domains_count ASC ";
        // else if($req == 'domain_count_dcnd')  $sql .= " ORDER BY domains_count DESC";
      }

      //echo($sql);exit();

      $meta_data_leads = DB::select(DB::raw($sql));

      if($meta_data_leads == null)
      {
        //dd('--null');
        $leads = $this->leads_Search($request);
        $leads_id = '';
        $this->totalLeads   = 0;
        $this->totalDomains = 0;
        foreach($leads as $key=>$val)
        {
          $this->totalLeads ++;
          $this->totalDomains += $val->domains_count;
          if($leads_id == '')
            $leads_id .= $val->id;
          else
            $leads_id .= ','.$val->id;
        }

        if($this->insert_metadata($this->compress($leads_id)
                              ,$request
                              ,$phone_type_meta
                              ,$domain_ext_meta
                              ,$dates_array
                              ,$domains_count_operator
                              ,$leads_unlocked_operator)) 
        {
          return $leads;
        }

        
        else
          dd('error in checkMetadata_Search');
      }
      else
      {
        $last_csv_insert_time = DB::select(DB::raw('SELECT MAX(created_at) as created FROM `csv_record`'));
        $last_query_update_time = strtotime($meta_data_leads[0]->updated_at); 
        $last_csv_insert_time   = strtotime($last_csv_insert_time[0]->created);
        //dd($last_query_update_time);
        if($last_query_update_time > $last_csv_insert_time)
        {
          $this->update_metadata_partial($meta_data_leads[0]->id);
          $raw_leads_id = $this->uncompress($meta_data_leads[0]->leads
                                  ,$meta_data_leads[0]->compression_level);
          $this->totalDomains = $meta_data_leads[0]->totalDomains;
          $this->totalLeads   = $meta_data_leads[0]->totalLeads;
          return $this->raw_leads("(".$raw_leads_id.")");
        }
        else
        {
          //dd('in else');
          $leads = $this->leads_Search($request);
          $leads_id = '';
          $totalLeads   = 0;
          $totalDomains = 0;
          foreach($leads as $key=>$val)
          {
            $totalLeads ++;
            $totalDomains += $val->domains_count;
            if($leads_id == '')
              $leads_id .= $val->id;
            else
              $leads_id .= ','.$val->id;
          }
          if($this->update_metadata_full($meta_data_leads[0]->id
                              ,$totalLeads
                              ,$totalDomains
                              ,$this->compress($leads_id)))
          return $leads;
        }
        //return $meta_data_leads[0];
      }
    }
    private function update_metadata_partial($id)
    {
      $meta = SearchMetadata::where('id',$id)->first();
      $meta->search_priority++;

      if($meta->save())
      {
        $this->meta_id = $meta->id;
        return true;
      }
      dd('error in update_metadata_partial');
    }
    private function update_metadata_full($id,$totalLeads,$totalDomains,$compresed_leads)
    {
      $meta = SearchMetadata::where('id',$id)->first();
      $meta->search_priority++;
      $meta->totalLeads         = $totalLeads;
      $meta->totalDomains       = $totalDomains;
      $meta->leads              = $compresed_leads['compressed'];
      $meta->compression_level  = $compresed_leads['compression_level'];
      if($meta->save())
      {
        $this->meta_id = $meta->id;
        return true;
      }
      dd('error in update_metadata_full');
    }
    //https://t5ilmpnba:4on9sq6ae8lMRVHCZxp2@www.whoxy.com/newly-registered-domains/download.php?file=2017-03-19_proxies.zip

    private function insert_metadata($compress,Request $request,$phone_type_meta
                                      ,$domain_ext_meta,$dates_array
                                      ,$domains_count_operator,$leads_unlocked_operator)
    {
      $meta = new SearchMetadata();
      $meta->domain_name             = $request->domain_name==null?null:$request->domain_name;
      $meta->domain_ext              = $domain_ext_meta ==null?null:$domain_ext_meta;
      $meta->registrant_country      = $request->registrant_country ==null ? null
                                                :$request->registrant_country;
      $meta->registrant_state        = $request->registrant_state ==null ? null
                                                :$request->registrant_state;
      $meta->domains_create_date1    = isset($dates_array[0])?$dates_array[0]:null;
      $meta->domains_create_date2    = isset($dates_array[1])?$dates_array[1]:null;
      $meta->domains_count           = $request->domaincount_no == null ? null : $request->domaincount_no;
      $meta->number_type             = $phone_type_meta == '()' ? null : $phone_type_meta;
      $meta->sortby                  = (isset($request->sort) && !is_null($request->sort)) ? 
                                        $request->sort : null;
      $meta->domains_count_operator  = $domains_count_operator == null?null
                                          :$domains_count_operator;
      $meta->leads_unlocked_operator = $leads_unlocked_operator == null?null
                                          :$leads_unlocked_operator;
      $meta->unlocked_num            = $request->leadsunlocked_no == null ? null : $request->leadsunlocked_no;
      $meta->search_priority         = 1;
      $meta->totalLeads              = $this->totalLeads;
      $meta->totalDomains            = $this->totalDomains;
      $meta->compression_level       = $compress['compression_level'];
      $meta->leads                   = $compress['compressed'];
      
      if($meta->save())
      {
        $this->meta_id = $meta->id; 
        return true;
      }
      dd('problem in insert metadata function');
    }

    private function compress($string)
    {
      $last_size = strlen($string);
      $compressed = $string;
      $ratio = 0;
      while(true)
      {
          if($ratio > 255) break;
          $min_size   = strlen(gzdeflate($compressed, 9));
          if($min_size >= $last_size)  break;

          $compressed = gzdeflate($compressed, 9);
          $last_size = $min_size;
          $ratio++;   
          //echo $last_size.'<br/>'; 
      }
      //echo($compressed);exit();

      return array('compressed'=>$compressed
                    ,'compression_level'=>$ratio);
    }
    private function uncompress($leads,$compression_level)
    {
      while($compression_level--) $leads = gzinflate($leads);
      return $leads;
    }
    private function setVariables(Request $request)
    {
      //variables
      $this->phone_type_array = array();
      $this->domain_ext_arr   = array();
      $this->domain_ext_str   = ' ';
      
      if($request->landline_number) 
        array_push($this->phone_type_array,'Landline');
      if($request->cell_number)     
        array_push($this->phone_type_array,'Cell Number');
      if(isset($request->domain_ext) && sizeof($request->domain_ext)>0)
        $this->domain_ext = $request->domain_ext;
      
      if(isset($request->domain_ext))
      {
        foreach($request->domain_ext as $k => $v)
        {
          array_push($this->domain_ext_arr,$v);

          if($this->domain_ext_str == ' ') 
            $this->domain_ext_str .= "'".$v."'";
          else
            $this->domain_ext_str .= ",'".$v."'";
        }
        $this->domain_ext_str = '('.$this->domain_ext_str.')';
      }
       
      //$date_flag = 0;
    }

    public function search(Request $request)
    {
      ini_set('max_execution_time', 346000);
      if(\Auth::check())
      {
        if($request->all())
        {

          $start = microtime(true);
          
          $this->setVariables($request); //initiating MY VARIABLES
          $leads = $this->checkMetadata_Search($request);//----------check in the metadata table
          

          //$leads = $this->leads_Search($request);//---Selecting leads from search parameters

          //--------------------------Data for single page-------------//
          $array = $this->leadsPerPage_Search(1,$request->pagination,$leads);
          //dd('here');
          $data             = $array['data'];
          //$leadsid_per_page = $array['leadsid_per_page'];
          $leads_string     = $array['leads_string'];
          $totalDomains     = $this->totalDomains;
          $totalLeads       = $this->totalLeads;
          $totalPage        = $this->totalPage;
          
          if($leads_string != "()")
          {
            //------------Selecting domains for selected leads------------//

            $param = ['domain_name'=>$request->domain_name
                     ,'domain_ext' =>$request->domain_ext
                     ,'domains_create_date'=>$request->domains_create_date
                     ,'domains_create_date2'=>$request->domains_create_date2];
            $phone_type_array = $this->phone_type_array;
            $domains = $this->domainsPerPage_Search($param,$this->phone_type_array
                                              ,$leads_string);
            $domain_list = array();
            foreach($data as $k=>$v) // setting up the leads array
              $domain_list[$v['registrant_email']]['checked'] = false;
            
            foreach($domains as $k=>$v)
            {
              if(!($domain_list[$v->registrant_email]['checked']))
              {
                $domain_list[$v->registrant_email]['checked'] = true;
                $domain_list[$v->registrant_email]['domain_name'] = $v->domain_name;
                $domain_list[$v->registrant_email]['domain_ext']  = $v->domain_ext;
                $domain_list[$v->registrant_email]['domains_create_date'] = $v->domains_create_date;
                $domain_list[$v->registrant_email]['number_type'] = $v->number_type;
              }
            }
          }
          
          $leads_arr = array(); //consists only leads
          foreach ($data as $key => $value) 
          {
            if(!isset($leads_arr[$value['registrant_email']]))
            {
              $leads_arr[$value['registrant_email']] = 1;
            }
            else
              $leads_arr[$value['registrant_email']]++;
          }

              
          $user_id = \Auth::user()->id;
          $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
          $users_array = array_flip($users_array);
          $obj_array = Wordpress_env::where('user_id',$user_id)->pluck('registrant_email')->toArray(); 
          $obj_array = array_flip($obj_array);
        
          //dd($leadsid_per_page);
          //dd($allrecords->count());

          $string_leads = serialize($leads_arr);
          // exit();
          $end = microtime(true)-$start;
          //echo "time : ".$end."<br>";


            if(\Auth::user()->user_type == 2)
            {
              return view('home.admin.admin_search',
                [ 'record'            =>$data,
                  'page'              => 1,
                  'meta_id'           => $this->meta_id,
                  'leadsid_per_page'  => isset($leadsid_per_page) ? $leadsid_per_page : null,
                  'totalLeads'        =>$this->totalLeads,
                  'totalDomains'      =>$this->totalDomains,
                  'totalPage'         =>$this->totalPage,
                  'domain_list'       =>isset($domain_list) ? $domain_list : null,
                  'leadArr'           =>$leads_arr,
                  'string_leads'      =>$string_leads,
                  // 'users_array'       =>$users_array,
                  'query_time'        =>$end       
                ]);      
            }


            return view('home.search' , 
                  ['record'       => $data, 
                   'page'         => 1,
                   'totalLeads'   =>$totalLeads,
                   'totalDomains' =>$totalDomains,
                   'domain_list'       =>isset($domain_list) ? $domain_list : null,
                   'leadArr'      =>$leads_arr , 
                   'string_leads'=>$string_leads,
                   'users_array'=>$users_array,
                   'obj_array'=>$obj_array,
                   'query_time'=>$end]);
        }
        else
        {
          Session::forget('emailID_list');
          $allrecords = null;
          $leadArr = null;
          $totalDomains = null;
          return view('home.search' , ['record' => null , 'leadArr'=>null , 'totalDomains'=>null]);
        }
      }
      else
      {
        dd('Please log in');  
      }
    }
}
