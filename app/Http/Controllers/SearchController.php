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
public $leads_id;
 
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
  //dd($request->all());
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
    //dd('here');
    //dd($request->landline); 

    $phone_type_array = array();
    if(isset($request->cell) && $request->cell != null)
      array_push($phone_type_array, 'Cell Number');

    if(isset($request->landline) && $request->landline != null)  
      array_push($phone_type_array, 'Landline');

    $start = microtime(true);
    $reqData = $this->all_lead_domains_set($request,$phone_type_array,$request->meta_id);

    
    //dd($reqData);

    return Excel::create('domainleads', function($excel) use ($reqData) {

      $excel->sheet('mySheet', function($sheet) use ($reqData){
        $sheet->fromArray($reqData);
      });
    })->download($type);


    //$this->print_csv(unserialize($request->all_leads_to_export[0]),$type);

    //return;
  }
}

  private function all_lead_domains_set(Request $request,$phone_type_array,$meta_id)
                                        
  {

    $sql    = "SELECT leads,compression_level from search_metadata 
              where id = ".$meta_id;
    $data   = DB::select(DB::raw($sql));

    $leads  = $this->uncompress($data[0]->leads,$data[0]->compression_level);
    $data   = null;
    $offset = 1;
    $limit  = $request->totalLeads;


    if($limit == null)
    {
      $count_leads = explode(',',$leads);
      $limit = sizeof($count_leads);
    }
    

    $leads_str  = $this->paginated_raw_leads($leads,$limit,$offset);
    $leads  = $this->raw_leads($leads_str);
    $array  = $this->leadsPerPage_Search($leads);
    $param  = ['domain_name'=>$request->domainname
             ,'domain_ext' =>($request->domainext == null ? null : explode(',',$request->domainext))
             ,'domains_create_date'=>$request->createdate1
             ,'domains_create_date2'=>$request->createdate2];
    //dd($param);
    $data   = $array['data'];
    $domains= $this->domainsPerPage_Search($param,$phone_type_array,$array['leads_string']);
    $data = $this->domains_output_Search($data,$domains);

    $reqData = array();
    $key=0;
    $hash = array();
    foreach($data as $i=>$val)
    {
      $name = explode(' ',$val['registrant_name']);
      $reqData[$i]['first_name'] = isset($name[0]) ? $name[0] : '';
      $reqData[$i]['last_name']  = isset($name[1]) ? $name[1] : '';
      $reqData[$i]['website']    = $val['domain_name'];
      $reqData[$i]['phone']      = $val['registrant_phone'];
      $reqData[$i]['email_id'] = $val['registrant_email'];
    }

    return $reqData;
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


    //when a common user unlocks a user
    //fired with ajax
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

    

    //given a registrant email get all doains for it
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

    public function update_metadata_today($date)
    {
      //dd($date);
      $sql = "SELECT * from search_metadata ORDER BY query_time,search_priority DESC";
      $data = DB::select(DB::raw($sql));
      $i=0;


      $last_csv_insert_date = DB::select(DB::raw('SELECT MAX(date(created_at)) as created FROM `csv_record`'));
      $last_csv_insert_date   = strtotime($last_csv_insert_date[0]->created);

      //if($date != $last_csv_insert_date[0])
      // return 'updated';

      

      foreach ($data as $key => $value) 
      {
        //dd($value);
        $i++;
        $phone_flag=0;
        $number_type_str = '';
        $domain_ext_str = '';

        if($value->domain_ext != null)
        {

          $domain_ext = substr($value->domain_ext,1,-1);
          $domain_ext = explode(',', $domain_ext);
          foreach($domain_ext as $i=>$j)
          {
            if($domain_ext_str == "") $domain_ext_str .= "'".$j."'";
            else  $domain_ext_str .= ",'".$j."'"; 
          }
        }
        $domain_ext_str = "(".$domain_ext_str.")";
        //dd($domain_ext_str);

        if($value->number_type != null)
        {

          $num_type = substr($value->number_type,1,-1);
          $num_type = explode(',', $num_type);
          foreach($num_type as $i=>$j)
          {
            if($number_type_str == "") $number_type_str .= "'".$j."'";
            else  $number_type_str .= ",'".$j."'"; 
          }
        }
        $number_type_str = "(".$number_type_str.")";
        //dd($number_type_str);


        $sql = "SELECT DISTINCT l.registrant_email 
            , l.id
            , l.registrant_fname 
            , l.registrant_lname 
            , l.registrant_country 
            , l.registrant_company 
            , l.registrant_phone 
            , l.registrant_state 
            , l.registrant_zip
            , l.domains_count 
            , l.unlocked_num ";

        $phone_flag = $value->number_type == null 
                    ? 0
                    : 1;

        $sql .= $phone_flag == 1 
                ? " , vp.number_type "
                : " ";

        $sql .= "FROM leads as l 
                INNER JOIN each_domain AS ed ON ed.registrant_email = l.registrant_email
                INNER JOIN domains_info AS di ON di.domain_name = ed.domain_name ";

        
        $sql .= $phone_flag == 1 
                ? " INNER JOIN valid_phone AS vp ON vp.registrant_email = l.registrant_email "
                : " ";

        //dd($sql);
        //echo($sql);exit();
            
        $sql .=" WHERE l.registrant_email != '' ";
        $flag = 0;
        $checked = 0;
        foreach ($value as $key => $req) 
        {
            if($key == 'registrant_country' && $req != null)
            {
                $sql .= " and l.registrant_country='".$req."' ";
                
            }
            else if($key == 'registrant_state' && $req != null)
            {
                $sql .= " and l.registrant_state='".$req."' ";
            }
            else if($key == 'registrant_zip' && $req != null)
            {
                $sql .= " and l.registrant_zip = '".$req."'";
            }
            else if($key == 'domain_name' && $req != null)
            {
                $sql .= " and ed.domain_name LIKE '%".$req."%' ";
            }
            else if($key == 'domain_ext' && $req != null)
            {
                //dd($req);
                $sql .= " and ed.domain_ext IN ".$domain_ext_str." ";
            }
            else if($key == 'number_type' && $req != null)
            {
                $sql .= " and vp.number_type IN ".$number_type_str." ";
            }
            else if($key == 'domains_count' && ($value->domains_count_operator != '' || 
                $value->domains_count_operator != null) && $req != null)
            {
              $sql .= " and l.domains_count ".$value->domains_count_operator
                      ." "
                      .$req;
            }
            else if($key == 'unlocked_num' 
                  && ($value->leads_unlocked_operator != '' || 
                  $value->leads_unlocked_operator != null) && 
                  $req != null)
            {
              $sql .= " and l.unlocked_num ".$value->leads_unlocked_operator
                      ." "
                      .$req;
            }
            else if($key == 'sortby' && $req != null)
            {
              switch ($req) 
              {
                case 'unlocked_asnd': $sql .= " ORDER BY l.unlocked_num ASC ";
                  # code...
                  break;

                case 'unlocked_dcnd': $sql .= " ORDER BY l.unlocked_num DESC ";
                  # code...
                  break;

                case 'domain_count_asnd': $sql .=" ORDER BY l.domains_count ASC ";
                  # code...
                  break;

                case 'domain_count_dcnd': $sql .= " ORDER BY l.domains_count DESC";
                  # code...
                  break;
                
                default:
                  # code...
                  break;
              }
            }
            else if(($key == 'domains_create_date1' || $key == 'domains_create_date2')
               && $checked == 0)
            {
              $checked = 1;
              if($value->domains_create_date1 != null && $value->domains_create_date2 != null)
              {
                $sql .= " and di.domains_create_date >= '"
                        .$value->domains_create_date1
                        ."' and di.domains_create_date <= '"
                        .$value->domains_create_date2."' ";
              }
              else 
              {
                  if($value->domains_create_date1 != null)
                  {
                    $sql .= " and di.domains_create_date = '".$value->domains_create_date1."' ";
                  }
                  else if($value->domains_create_date2 != null)
                  {
                    $sql .= " and di.domains_create_date = '".$value->domains_create_date2."' ";
                  }
              }
            }
        }



        
        //dd($leads);

        //dd($sql);

        try{
          $t1 = microtime(true);
          $leads = DB::select(DB::raw($sql));
          $t2 = microtime(true);
          $query_time = $t2-$t1;

          $this->counting_leads_domains($leads); //update the new count
          //$this->count_total_pages($request->pagination);
          $status = $this->update_metadata_full($value->id,$this->compress($this->leads_id)
                                        ,$query_time);  
        }
        catch(\Exception $e)
        {
          \Log::info($e->getMessage());
          //dd($e->getMessage());
        }
        

        echo $status;
      }
    }


    /*
    search result for selecting leads with selected raw leads id
    complexity : n*log(l);
    n :: record per page [max=500]
    l :: total leads in leads table
    */
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
      , l.registrant_zip
      , l.domains_count 
      , l.unlocked_num 
      FROM leads as l
      WHERE l.id IN (".$leads_str.") 
      ORDER BY FIELD(id,".$leads_str.")";
      //dd($sql);
      $leads = DB::select(DB::raw($sql));
      return $leads;
    }


    /* 
      search result for different page number with ajax request
      complexity : n*log(l) + n*log(ed);
      n :: record per page
      l :: total leads in leads table
      ed:: total domains in each_domain table
    */
    public function ajax_search_paginated(Request $request)
    {
      //dd($request->all());
      $start = microtime(true);
      $sql    = "SELECT leads,compression_level from search_metadata 
                where id = ".$request->meta_id;
      $data   = DB::select(DB::raw($sql)); 
      $leads  = $this->uncompress($data[0]->leads,$data[0]->compression_level);
      $data   = null;

      //$leads  = $this->raw_leads("(".$leads.")");
      //$leads  = $this->raw_leads($leads);

      $offset = ($request->thisPage-1) * $request->pagination;
      $limit  = $request->pagination;

      // dd($leads);
      // dd($limit." ".$offset);
      $leads_str  = $this->paginated_raw_leads($leads,$limit,$offset);


      



      //dd($leads_str);
      $leads  = $this->raw_leads($leads_str);
      
      $array  = $this->leadsPerPage_Search($leads);

      $param  = ['domain_name'=>$request->domain_name
               ,'domain_ext' =>$request->domain_ext
               ,'domains_create_date'=>$request->domains_create_date
               ,'domains_create_date2'=>$request->domains_create_date2];
      $data   = $array['data'];
      $domains= $this->domainsPerPage_Search($param,$request->phone_type_array,$array['leads_string']);
      $data = $this->domains_output_Search($data,$domains);

      //dd($data);
      unset($domain_list);
      unset($leads);
      unset($sql);
      unset($param);
      unset($domains);

      $time = microtime(true) - $start;
      return \Response::json(array('data'=>$data , 'time'=>$time));

    }

    
    
    /* search leads on basis of the search parameters given 

    complexity of query [with inner join]: O(l+ed+di+vp) + O(log(l+ed+di+vp))
                        [with left join] : O(l+ed+di)*O(log(vp)) + O(log((l+ed+di)*log(vp)))
                        [without joining valid phone]
                                         : O(l+ed+di) + O(log(l+ed+di)) 
                        
    */
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
            , l.registrant_zip
            , l.domains_count 
            , l.unlocked_num ";
            $sql .= isset($phone_type_array) && sizeof($phone_type_array) > 0
              ? " , vp.number_type "
              : " ";

            $sql .="FROM leads as l INNER JOIN each_domain AS ed 
            ON ed.registrant_email = l.registrant_email
            INNER JOIN domains_info AS di 
            ON di.domain_name = ed.domain_name ";
            $sql .= isset($phone_type_array) && sizeof($phone_type_array) > 0
              ? " INNER JOIN valid_phone AS vp ON vp.registrant_email = l.registrant_email "
              : " ";
            
            $sql .=" WHERE l.registrant_email != '' and ed.registrant_email != '' ";
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
                else if($key == 'registrant_zip')
                {
                    $sql .= " and l.registrant_zip = '".$req."'";
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
                    if($gt_ls_domaincount_no == '') 
                    {
                        $sql .= " and l.domains_count > 0";
                        continue;
                    }
                    if($req=='') $req = 0;
                    $sql .= " and l.domains_count ".$gt_ls_domaincount_no." ".$req;
                }
              }
            }
            if(isset($phone_type_array) && sizeof($phone_type_array) > 0)
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
            
            $leads = DB::select(DB::raw($sql));
            
            return $leads;
    }

    private function leadsPerPage_Search($leads)
    {
        $leads_string = '';
        $totalLeads = sizeof($leads);
        $leadsid_per_page   = array(); 
        $i=$x=$z=$totalDomains=$lastz=0;
        $data = array();
        for($i=0 ; $i<sizeof($leads) ; $i++)
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
            $data[$x]['registrant_zip']     = $leads[$i]->registrant_zip;
            $data[$x]['unlocked_num']       = $leads[$i]->unlocked_num;
            $data[$x]['domains_count']      = $leads[$i]->domains_count;
            $data[$x++]['registrant_state'] = $leads[$i]->registrant_state;

            if($leads_string == '') $leads_string .= "'".$leads[$i]->registrant_email."'";
            else                     $leads_string .= ",'".$leads[$i]->registrant_email."'";
          }
        }
        $leads_string = "(".$leads_string.")";
        return array('data'=>$data,'leads_string'=>$leads_string);
    }

    /*
      Given a set of string of registrant_email in string perform select query on domains table
      based on -- domain_name , domain_ext , and domains_create_date fields 
    */
    private function domainsPerPage_Search($param, $phone_type_array ,$leads_string)
    {
      $phone_type_array = $this->phone_type_array;
      $domain_ext_str   = $this->domain_ext_str;
      $date_flag = 0;

      if($leads_string != '()')
      {
        $sql = " SELECT ed.domain_name, ed.domain_ext, ed.registrant_email ,di.domains_create_date,vp.number_type FROM `each_domain` ed 
        INNER JOIN domains_info as di  
        ON di.domain_name = ed.domain_name ";
        isset($phone_type_array) && sizeof($phone_type_array)>0
              ? $sql .= " INNER JOIN valid_phone AS vp "
              : $sql .= " LEFT JOIN valid_phone AS vp ";
        $sql .=" ON vp.registrant_email = ed.registrant_email 
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
        if(isset($phone_type_array) && sizeof($phone_type_array)>0)
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
    }


    private function estimated_input_fields()
    {
      //'cell_number','landline_number' -> condensed into phone_type
      return ['domain_name'=>false
              ,'registrant_country'=>false
              ,'registrant_state'=>false
              ,'registrant_zip'=>false
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


    /* 
      Checks searchMetadata table
      if all search fields matches and is searched before
        if last search was done before new csv file insertion
          update full search metadata and return
        else
          update partial search metadata and return
    */

    private function checkMetadata_Search(Request $request)
    {
      $input = $this->estimated_input_fields();
      $date_flag = 0;
      $phone_type_meta  = '('.implode(',',$this->phone_type_array).')';
      $domain_ext_meta  = '('.implode(',',$this->domain_ext_arr).')';
      $dates_array = array();
      $leads_unlocked_operator = '';
      $domains_count_operator  = '';

      $limit =  $request->pagination == null 
                  ? 10
                  : $request->pagination;

                  
      $offset = isset($request->page) ? $request->page :1;

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
        if(!is_null($request->$key) && $req != '')
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
          else if($key == 'registrant_zip')
          {
              $sql .= " and registrant_zip='".$req."' ";
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
          else if($key == 'leadsunlocked_no')
          {
              if($gt_ls_leadsunlocked_no == '') continue;
              else if($gt_ls_leadsunlocked_no != '' && is_null($req)) continue;
              if($req == '')  $req=0;
              $sql .= " and unlocked_num = ".$req." and leads_unlocked_operator = '".$gt_ls_leadsunlocked_no."'"; 
              $input[$key] = true;
              $input['gt_ls_leadsunlocked_no'] = true;
              $leads_unlocked_operator = $gt_ls_leadsunlocked_no;
          }
          else if($key == 'domaincount_no')
          {
              if($gt_ls_domaincount_no == '') 
              {
                continue;
              }
              else if($gt_ls_domaincount_no != '' && is_null($req)) continue;
              if($req=='') $req = 0;
              $sql .= " and domains_count = ".$req." and domains_count_operator = '".$gt_ls_domaincount_no."'";
              $input[$key] = true;
              $input['gt_ls_domaincount_no'] = true;
              $domains_count_operator = $gt_ls_domaincount_no;
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
              case 'registrant_zip'   : $sql .= " and registrant_zip IS NULL";
                    break;
              case 'domains_create_date' : 
                    if(!$input['domains_create_date2'])
                    $sql .= " and domains_create_date1 IS NULL and domains_create_date2 IS NULL";
                    else
                    $sql .= " and domains_create_date1 IS NULL";
                    break;
              case 'number_type' : $sql .=" and number_type IS NULL";
                    break;
              case 'gt_ls_domaincount_no' : $sql .= " and domains_count_operator = '>'";
                    break;
              case 'domaincount_no' : $sql .= " and domains_count = 0";
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

        $t1 = microtime(true);
        $leads = $this->leads_Search($request);
        $t2 = microtime(true);
        $query_time = $t2-$t1;

        //dd($leads);
        $this->counting_leads_domains($leads);
        $this->count_total_pages($request->pagination);
        //dd(strlen($leads_id));

        if(sizeof($leads) > 0)
        {
          if($this->insert_metadata($this->compress($this->leads_id)
                              ,$request
                              ,$phone_type_meta
                              ,$domain_ext_meta
                              ,$dates_array
                              ,$domains_count_operator
                              ,$leads_unlocked_operator
                              ,$query_time)) 
          {
            return $this->limit($leads,$limit,$offset);
            //return $leads;
          }
          else
            dd('error in checkMetadata_Search');  
        }
        else
        {
         return null;
        }

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
          $this->count_total_pages($request->pagination);
          

          $raw_leads_id = $this->paginated_raw_leads($raw_leads_id,$limit,$offset);

          return $this->raw_leads($raw_leads_id);
        }
        else
        {
          //dd('in else');

          $t1 = microtime(true);
          $leads = $this->leads_Search($request);
          $t2 = microtime(true);
          $query_time = $t2-$t1;

          $this->counting_leads_domains($leads); //update the new count
          $this->count_total_pages($request->pagination);
          if($this->update_metadata_full($meta_data_leads[0]->id,$this->compress($this->leads_id),$query_time))
          return $this->limit($leads,$limit,$offset);
        }
      }
    }

    //selects leads id as string format from dump of all leads id given
    //according to offset and limit derived from pagination
    private function paginated_raw_leads($raw_leads,$limit,$offset)
    {
      
      $array = explode(",",$raw_leads);
      $return = "";
      for($i=$offset-1 ; $i<$limit+$offset; $i++)
      {
        if(!isset($array[$i])) break;
        if($return == "") $return .= $array[$i];
        else $return .= ",".$array[$i];
      }
      return $return;
    }

    //limits the leads search set to only as required by max data per page
    private function limit($leads,$limit,$offset)
    {
      $offset = $offset-1;
      $filtered_leads = array();
      $j=0;
      for($i=$offset ; $i<$limit+$offset ; $i++)
      {
        if(!isset($leads[$i])) break;
        $filtered_leads[$j++] = $leads[$i];
      }
      return $filtered_leads;
    }

    //counts total page based on $this->totalDomains and $this->totalLeads count
    private function count_total_pages($pagination)
    {
      if($pagination==null || $pagination == '')
        $pagination = 10;

      $extraPage = (int)($this->totalLeads%$pagination);
      $extraPage = $extraPage > 0 ? 1 : 0;
      $this->totalPage = (int)($this->totalLeads/$pagination) + $extraPage;
      return;
    }

    //count total leads and domains for a set of leads 
    // format -- array -> containing stdClass
    private function counting_leads_domains($leads)
    {
        $this->leads_id = '';
        $this->totalLeads   = 0;
        $this->totalDomains = 0;
        foreach($leads as $key=>$val)
        {
          $this->totalLeads ++;
          $this->totalDomains += $val->domains_count;
          if($this->leads_id == '')
            $this->leads_id .= $val->id;
          else
            $this->leads_id .= ','.$val->id;
        }
        return;
    }

    //update search field of this metadata set increasing priority of this set
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

    //update previous meta data set fully if new records are inserted
    //after the last search date with similar parameters
    private function update_metadata_full($id,$compresed_leads,$query_time)
    {
      $meta = SearchMetadata::where('id',$id)->first();
      $meta->search_priority++;
      $meta->totalLeads         = $this->totalLeads;
      $meta->totalDomains       = $this->totalDomains;
      $meta->leads              = $compresed_leads['compressed'];
      $meta->compression_level  = $compresed_leads['compression_level'];
      $meta->query_time         = $query_time;
      if($meta->save())
      {
        $this->meta_id = $meta->id;
        return true;
      }
      dd('error in update_metadata_full');
    }


    //insert mmetadatafor a new search set -- leads id inserted in zipped format
    private function insert_metadata($compress,Request $request,$phone_type_meta
                                      ,$domain_ext_meta,$dates_array
                                      ,$domains_count_operator,$leads_unlocked_operator
                                      ,$query_time)
    {
      
      $meta = new SearchMetadata();
      $meta->domain_name             = $request->domain_name==null
                                          ? null
                                          :$request->domain_name;
      $meta->domain_ext              = $domain_ext_meta ==null || $domain_ext_meta =='()'
                                          ? null
                                          :$domain_ext_meta;
      $meta->registrant_country      = $request->registrant_country ==null 
                                          ? null
                                          :$request->registrant_country;
      $meta->registrant_state        = $request->registrant_state ==null 
                                          ? null
                                          :$request->registrant_state;
      $meta->domains_create_date1    = isset($dates_array[0])
                                          ? $dates_array[0]
                                          :null;
      $meta->domains_create_date2    = isset($dates_array[1])
                                          ? $dates_array[1]
                                          :null;
      $meta->domains_count           = $request->domaincount_no == null 
                                          ? 0 
                                          : $request->domaincount_no;
      $meta->number_type             = $phone_type_meta == '()' 
                                          ? null 
                                          : $phone_type_meta;
      $meta->sortby                  = (isset($request->sort) && !is_null($request->sort)) 
                                          ? $request->sort 
                                          : null;
      $meta->domains_count_operator  = $domains_count_operator == null
                                          ?'>'
                                          :$domains_count_operator;
      $meta->leads_unlocked_operator = $leads_unlocked_operator == null
                                          ?null
                                          :$leads_unlocked_operator;
      $meta->unlocked_num            = $request->leadsunlocked_no == null 
                                          ? null 
                                          : $request->leadsunlocked_no;
      $meta->search_priority         = 1;
      $meta->totalLeads              = $this->totalLeads;
      $meta->totalDomains            = $this->totalDomains;
      $meta->compression_level       = $compress['compression_level'];
      $meta->leads                   = $compress['compressed'];

      $meta->query_time              = $query_time;
      
      if($meta->save())
      {
        $this->meta_id = $meta->id; 
        return true;
      }
      dd('problem in insert metadata function');
    }

    /*Compress a string consisting of id of leads table , and get compression ratio*/
    private function compress($string)
    {
      $last_size = strlen($string);
      $compressed = $string;
      $ratio = 0;
      while($ratio < 255)
      {
          $min_size   = strlen(gzdeflate($compressed, 9));
          if($min_size >= $last_size)  break;
          $compressed = gzdeflate($compressed, 9);
          $last_size = $min_size;
          $ratio++;   
      }
      return array('compressed'=>$compressed
                    ,'compression_level'=>$ratio);
    }

    /* uncompress compressed string based on compression ratio */
    private function uncompress($leads,$compression_level)
    {
      while($compression_level--)  $leads = gzinflate($leads);
      return $leads;
    }

    /*setting 3 main global variables
        ---phone_type_array
        ---domain_ext_arr
        ---domain_ext_str */
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

    // Binding 2 tables - leads - each_domain -> dataset into 1 array
    private function domains_output_Search($data , $domains)
    { 
      $domain_list = array();
      foreach($data as $k=>$v)  $domain_list[$v['registrant_email']]['checked'] = false;
      
      foreach($domains as $k=>$v)
      {
        if(!($domain_list[$v->registrant_email]['checked']))
        {
          $domain_list[$v->registrant_email]['checked']             = true;
          $domain_list[$v->registrant_email]['domain_name']         = $v->domain_name;
          $domain_list[$v->registrant_email]['domain_ext']          = $v->domain_ext;
          $domain_list[$v->registrant_email]['domains_create_date'] = $v->domains_create_date;
          $domain_list[$v->registrant_email]['number_type']         = $v->number_type;
        }
      }
      foreach ($data as $key => $value) 
      {

        $phone = explode('.',$value['registrant_phone']);
        $phone = isset($phone[1]) ? $phone[1] : $phone[0];

        $data[$key]['registrant_phone']     = $phone;

        $data[$key]['domain_name']          = isset($domain_list[$value['registrant_email']]['domain_name']) 
                                              ? $domain_list[$value['registrant_email']]['domain_name']
                                              : null;
        $data[$key]['domain_ext']           = isset($domain_list[$value['registrant_email']]['domain_ext']) 
                                              ? $domain_list[$value['registrant_email']]['domain_ext']
                                              : null;
        $data[$key]['domains_create_date']  = isset($domain_list[$value['registrant_email']]
                                              ['domains_create_date']) 
                                              ? $domain_list[$value['registrant_email']]['domains_create_date']
                                              : null;
        $data[$key]['number_type']          = isset($domain_list[$value['registrant_email']]
                                              ['number_type']) 
                                              ? $domain_list[$value['registrant_email']]['number_type']
                                              : null;

        $data[$key]['email_link']           = encrypt($data[$key]['registrant_email']);
      }
      
      return $data;
    }

    private function search_algo(Request $request)
    {   
      $start = microtime(true);   
      $this->setVariables($request); //initiating MY VARIABLES
      $leads = $this->checkMetadata_Search($request);//----------check in the metadata table
      $array = $this->leadsPerPage_Search($leads);
      
      $data             = $array['data'];
      $leads_string     = $array['leads_string'];
      $totalDomains     = $this->totalDomains;
      $totalLeads       = $this->totalLeads;
      $totalPage        = $this->totalPage;
      
      if($leads_string != "()")
      {
        $param = ['domain_name'=>$request->domain_name
                 ,'domain_ext' =>$request->domain_ext
                 ,'domains_create_date'=>$request->domains_create_date
                 ,'domains_create_date2'=>$request->domains_create_date2];
        $phone_type_array = $this->phone_type_array;
        $domains = $this->domainsPerPage_Search($param,$this->phone_type_array
                                          ,$leads_string);
        $data=$this->domains_output_Search($data,$domains);
      }

          
      //$user_id = \Auth::user()->id;
      //$users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
      //$users_array = array_flip($users_array);
      //$obj_array = Wordpress_env::where('user_id',$user_id)->pluck('registrant_email')->toArray(); 
      //$obj_array = array_flip($obj_array);
    
      $end = microtime(true)-$start;

      return  [ 'record'            =>$data,
              'page'                => 1,
              'meta_id'             => $this->meta_id,
              'totalLeads'          =>$this->totalLeads,
              'totalDomains'        =>$this->totalDomains,
              'totalPage'           =>$this->totalPage,
              'domain_list'         =>isset($domain_list) ? $domain_list : null,
              'query_time'          =>$end       
            ];  

      return $data; 
    }

    public function search_api(Request $request)
    {
        //dd($request->all());
        $status = 'ok';
        $result = null;
        try{
          $start  = microtime(true);          
          $result = $this->search_algo($request);
          $end    = microtime(true)-$start;

          $phone_type_array = array();
          if(isset($request->cell) && $request->cell != null)
            array_push($phone_type_array, 'Cell Number');

          if(isset($request->landline) && $request->landline != null)  
            array_push($phone_type_array, 'Landline');


          $result = $this->all_lead_domains_set($request,$phone_type_array,$this->meta_id);
        }
        catch(\Exception $e)
        {
          $status = $e->getMessage();
        }
        
        return \Response::json(array('result'=>$result,'status'=>$status));
    }

    public function search(Request $request)
    {
      //dd($request->all());
      ini_set('max_execution_time', 346000);
      if(\Auth::check())
      {
        if($request->all())
        {
          $start = microtime(true);          
          $result = $this->search_algo($request);
          $end = microtime(true)-$start;
          //dd($this->meta_id);

          //echo "time : ".$end."<br>";
          //dd($data);

            if(\Auth::user()->user_type == 2)
            {
              return view('home.admin.admin_search',$result);      
            }

            return view('home.search' , 
                  ['record'       => $data, 
                   'page'         => 1,
                   'totalLeads'   =>$totalLeads,
                   'totalDomains' =>$totalDomains,
                   'domain_list'       =>isset($domain_list) ? $domain_list : null,
                   // 'leadArr'      =>$leads_arr , 
                   // 'string_leads'=>$string_leads,
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
