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
use Illuminate\Pagination\Paginator;


class SearchController extends Controller
{   


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
    dd($request->all());
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

    public function search(Request $request)
    {
      ini_set('max_execution_time', 346000);
      
      if(\Auth::check())
      {

        if($request->all())
        {

          //dd($request->all());
          $start = microtime(true);
          
          //initiating MY VARIABLES
          $phone_type_array = array();
          $domain_ext = null;

          if($request->landline_number)
            array_push($phone_type_array,'Landline');

          if($request->cell_number)
            array_push($phone_type_array,'Cell Number');

          if(isset($request->domain_ext) && sizeof($request->domain_ext)>0)
            $domain_ext = $request->domain_ext;



          $low_limit; $high_limit;

          if(!isset($request->page))
          {
            $low_limit  = 0;
            $high_limit = $request->pagination; 
          }
          else
          {
            $low_limit = ($request->page - 1)*$request->pagination;
            $high_limit = $low_limit + $request->pagination;
          }

          $domain_ext_str = ' ';
          if(isset($request->domain_ext))
          {
            foreach($request->domain_ext as $k => $v)
            {
              if($domain_ext_str == ' ')
              {
                $domain_ext_str .= "'".$v."'";
              } 
              else
              {
                $domain_ext_str .= ",'".$v."'";
              }
            }
            $domain_ext_str = '('.$domain_ext_str.')';
          }
          
          
          $date_flag = 0;

          // echo $low_limit.' '.$high_limit;
          // exit();

          $sql = "SELECT DISTINCT l.registrant_email 
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
                    $sql .= " and ed.domain_name IN ".$domain_ext_str." ";  
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
                        $sql .= " and di.domains_create_date = '".$dates_array[0]."' ";
                      }
                      else if(sizeof($dates_array) == 2)
                      {
                        
                        $sql .= " and di.domains_create_date >= '".$dates_array[0]."' and di.domains_create_date <= '".$dates_array[1]."'";
                        
                      }
                    }
                    //$query->whereIn('domains_info.domains_create_date',$dates_array);
                }
                
                else if($key=='gt_ls_leadsunlocked_no')
                {
                    if($req == 0)
                      $gt_ls_leadsunlocked_no='';
                    
                    else if($req == 1)
                      $gt_ls_leadsunlocked_no='>';
                    
                    else if($req == 2)
                      $gt_ls_leadsunlocked_no='<';
                    else if($req == 3)
                      $gt_ls_leadsunlocked_no='=';
                }
                else if($key == 'leadsunlocked_no')
                 {
                  if($gt_ls_leadsunlocked_no == '')
                    continue;
                  
                  if($req == '')
                      $req=0;
                   
                  $sql .= " and l.unlocked_num ".$gt_ls_leadsunlocked_no." ".$req; 

                    //$query = $query->where('unlocked_num',$gt_ls_leadsunlocked_no,$req);
                 }
                else if($key=='gt_ls_domaincount_no')
                {
                        if($req==0)
                          $gt_ls_domaincount_no='';

                        else if($req == 1)
                          $gt_ls_domaincount_no='>';
                        
                        else if($req == 2)
                          $gt_ls_domaincount_no='<';
                        else if($req == 3)
                          $gt_ls_domaincount_no='=';
                 }
                else if($key == 'domaincount_no')
                 {
                  if($gt_ls_domaincount_no == '')
                    continue;

                  if($req=='')
                      $req = 0;
                    
                    $sql .= " and l.domains_count ".$gt_ls_domaincount_no." ".$req;
                    //$query = $query->where('leads.domains_count',$gt_ls_leadsunlocked_no,$req);
                 }


            }
          }


          if(isset($request->sort)) 
          {
              $req = $request->sort;
              if($req == 'unlocked_asnd')
              {
                $sql .= " ORDER BY l.unlocked_num ASC ";
                //$query->orderBy('leads.unlocked_num','asc');
              }
              else if($req == 'unlocked_dcnd')
              {
                $sql .= " ORDER BY l.unlocked_num DESC ";
                //$query->orderBy('leads.unlocked_num','desc');
              }
              else if($req == 'domain_count_asnd')
              {
                $sql .= " ORDER BY l.domains_count ASC ";
                //$query->orderBy('leads.domains_count','asc');
              }
              else if($req == 'domain_count_dcnd') 
              {
                $sql .= " ORDER BY l.domains_count DESC";
                //$query->orderBy('leads.domains_count','desc');
              }
          }

          //$sql .= " LIMIT ".$request->pagination. ' OFFSET '.$low_limit;
          //dd($sql);
          //$data = DB::select(DB::raw($sql));

          
          $leads = DB::select(DB::raw($sql));
          $totalLeads = sizeof($leads);
          $take = $request->pagination;
          $page = isset($request->page) ? $request->page : 1;
          $paginator = new \Illuminate\Pagination\LengthAwarePaginator($leads, $totalLeads, $take, $page);

          // foreach($paginator as $v)
          // {
          //   echo($v->registrant_email . '<br>');
          // }
          $take ;
          $skip ;
          $take = $request->pagination;
          $skip = ($page-1)*$take;
          $i=0;
          $x=0;
          $leads_string = ' ';
          $data = array();
          $totalDomains = 0;
          foreach($paginator as $each)
          {
            $totalDomains += $each->domains_count; 
            $i++;
            if($i>$skip && $i<=$take)
            {
              $data[$x]['registrant_email'] = $each->registrant_email;
              $data[$x]['name'] = $each->registrant_fname.' '.$each->registrant_lname;
              $data[$x]['registrant_country'] = $each->registrant_country;
              $data[$x]['registrant_company'] = $each->registrant_company;
              $data[$x]['registrant_phone'] = $each->registrant_phone;
              $data[$x]['unlocked_num'] = $each->unlocked_num;
              $data[$x]['domains_count'] = $each->domains_count;
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
          }
          

          if($leads_string == '')
            $no_data = 1;
          else
            $no_data = 0;

          $leads_string = "(".$leads_string.")";
          //dd($leads_string);
         


          // going to second level table
          if($no_data == 0)
          {

          
              $sql = " SELECT ed.domain_name, ed.domain_ext, ed.registrant_email ,di.domains_create_date,vp.number_type FROM `each_domain` ed 
              INNER JOIN domains_info as di ON di.domain_name = ed.domain_name 
              INNER JOIN valid_phone as vp ON vp.registrant_email = ed.registrant_email 
              WHERE ed.registrant_email 
              IN ".$leads_string;
              //$flag = 0;
              $date_flag = 0;

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
              // echo $sql;
              // dd('---');

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

              $domains = DB::select(DB::raw($sql));

              $domain_list = array();


              foreach($data as $k=>$v) // setting up the leads array
              {
                //dd($v['registrant_email']);
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

          }
          
          //$data = new Paginator($data, $request->pagination);
          //dd($data);
          
          //dd($domain_list);


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
        

        

        
        //dd($allrecords->count());

        $string_leads = serialize($leads_arr);
        // exit();
        $end = microtime(true)-$start;
        echo "time : ".$end."<br>";

            if(\Auth::user()->user_type == 2)
            {
                return view('home.admin.admin_search',[

                    'record'      =>$data,
                    'paginator'   =>$paginator,
                    'totalLeads'  =>$totalLeads,
                    'totalDomains'=>$totalDomains,
                    'domain_list' =>$domain_list,
                    'leadArr'     =>$leads_arr,
                    'string_leads'=>$string_leads,
                    'users_array' =>$users_array,
                    'query_time'  =>$end
                  ]);      
            }


            return view('home.search' , 
                  ['record' => $data, 
                  'leadArr'=>$leads_arr , 
                  'string_leads'=>$string_leads,
                  'totalDomains'=>100,
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
