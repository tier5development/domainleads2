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
use Auth, View, Response, Log, Throwable;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;
use Input, DateTime;
use Illuminate\Pagination\Paginator;
use \Carbon\Carbon as Carbon;
use App\Helpers\UserHelper;
use App\SocketMeta;
use App\Events\UsageInfo;
use App\CsvDownload;
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

public function downloadExcel2(Request $request) {
}

public function totalLeadsUnlockedToday() {
  return response()->json(UserHelper::getUsageMatrix());
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
      $reqData[$key]['country']    = $each->leads->registrant_country;
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

public function downloadCsv(Request $request)
{
  try {
	\Log::info(' Download CSV and export :: ');
  set_time_limit(60000);
  ini_set('memory_limit', '-1');
  ini_set('max_execution_time', '0');

    if(!$request->has('meta_id')) {
      return redirect()->back()->with('fail', 'No records found to export.');
    }
    $phone_type_array = array();
    if(isset($request->cell) && $request->cell != null)
      array_push($phone_type_array, 'Cell Number');
    if(isset($request->landline) && $request->landline != null)
      array_push($phone_type_array, 'Landline');
    $start = microtime(true);
    if($request->has('all') && $request->all == 1) {
      \Log::info(" ::Start Download All Data -->");
      $reqData = $this->all_lead_domains_set($request,$phone_type_array,$request->meta_id, null, null);
      // save the data with user id in the database
      $save_data = new CsvDownload();
      $save_data->user_id = $request->uid;
      $save_data->download_data = serialize($reqData);
      $save_data->status = 1;
      $save_data->save();
      \Log::info("Download data saved successfully");
      return Response::json([
        'status'  =>  true,
        'path'    =>  null,
        'err'     =>  null,
        'message' => 'We are creating your file to download the file please visit Downloads section after sometime!'
      ]);
    } else {
      \Log::info("Download Paginated Data");
      $limit = $request->totalPagination;
      $offset = $request->currentPage;
      $reqData = $this->all_lead_domains_set($request,$phone_type_array,$request->meta_id, $limit, $offset);
      // Storing the xls file in server for user to download
      $date = \Carbon\Carbon::now()->format('Y-m-d');
      $name = 'domainleads-'.md5(rand());
      Excel::create($name, function($excel) use ($reqData) {
        $excel->sheet('mySheet', function($sheet) use ($reqData){
          $sheet->fromArray($reqData);
        });
      })->store('xls', public_path('excel/'.$date));
      return Response::json([
        'status'  =>  true,
        'path'    =>  config('settings.APPLICATION-DOMAIN').'/public/excel/'.$date.'/'.$name.'.xls',
        'err'     =>  null,
        'message' => null
      ]);
    } 
  } catch(Throwable $e) {
	\Log::info("ERROR:::-->".$e->getMessage()." LINE : ".$e->getLine()."Err File : ".$e->getFile() );
    return Response::json([
      'status'  =>  true, 
      'path'    =>  null, 
      'err'     =>  "ERR : ".$e->getMessage()." LINE : ".$e->getLine()."Err File : ".$e->getFile(),
      'message' => 'We are creating your file to download the file please visit Downloads section after sometime!'
    ]);
  }
}

  private function all_lead_domains_set(Request $request,$phone_type_array,$meta_id, $limit = null, $offset = null)
  {
    $sql    = "SELECT leads,compression_level from search_metadata where id = ".$meta_id;
    $data   = DB::select(DB::raw($sql));
    $leads  = $this->uncompress($data[0]->leads,$data[0]->compression_level);
    $data   = null;
    $offset = $offset != null && is_numeric($offset) && $offset > 0 ? $offset : 1;
    $offset = ($offset - 1) * $limit;
    //$limit  = $limit == null ? $request->totalLeads : $limit;

    if($limit == null)
    {
      $count_leads = explode(',',$leads);
      $limit = sizeof($count_leads);
    }

    $domain_ext_arr = is_array($request->domain_ext)
                      ? $request->domain_ext
                      : explode(',',$request->domain_ext);

    if($domain_ext_arr[0] == '') $domain_ext_arr = [];
    
    $leads_str  = $this->paginated_raw_leads($leads,$limit,$offset);
    $leads  = $this->raw_leads($leads_str);
    $array  = $this->leadsPerPage_Search($leads);
    $param  = ['domain_name'=>$request->domain_name
             ,'domain_ext' =>(sizeof($domain_ext_arr) == 0 ? null : $domain_ext_arr)
             ,'domains_create_date'=> $request->domains_create_date
             ,'domains_create_date2'=>$request->domains_create_date2];

    \Log::info(' domainleads api :: '.print_r($param, true));

    $data   = $array['data'];
    $domains= $this->domainsPerPage_Search($request, $param,$phone_type_array,$array['leads_string']);
    $data   = $this->domains_output_Search($data,$domains);

    $reqData = array();
    $key=0;
    $hash = array();
	\Log::info("output done!");    
    foreach($data as $i=>$val) {
      $name = explode(' ',$val['registrant_name']);
      $reqData[$i]['first_name'] = isset($name[0]) ? $name[0] : '';
      $reqData[$i]['first_name'] = str_replace('=','\=',$reqData[$i]['first_name']);

      $reqData[$i]['last_name']  = isset($name[1]) ? $name[1] : '';
      $reqData[$i]['last_name'] = str_replace('=','\=',$reqData[$i]['last_name']);
      
      $reqData[$i]['country']   = $val['registrant_country'];
      $reqData[$i]['country']   = str_replace('=','\=',$reqData[$i]['country']);

      $reqData[$i]['website']    = $val['domain_name'];
      $reqData[$i]['website']   = str_replace('=','\=',$reqData[$i]['website']);

      $reqData[$i]['domains_create_date'] = convertToMDY($val['domains_create_date']);
      $reqData[$i]['domains_create_date'] = str_replace('=','\=',$reqData[$i]['domains_create_date']);

      $reqData[$i]['expiry_date']= convertToMDY($val['expiry_date']);
      $reqData[$i]['expiry_date'] = str_replace('=','\=',$reqData[$i]['expiry_date']);

      $reqData[$i]['phone']      = str_replace('.', '-', $val['registrant_phone']);
      $reqData[$i]['phone']      = str_replace('=', '\=', $reqData[$i]['phone']);

      $reqData[$i]['email_id']    = $val['registrant_email'];
      $reqData[$i]['email_id']    = str_replace('=', '\=', $reqData[$i]['email_id']);

      $reqData[$i]['company']    = $val['registrant_company'];
      $reqData[$i]['company']    = str_replace('=', '\=', $reqData[$i]['company']);

    }

    // dd($reqData);
    return $reqData;
  }

    public function createWordpressForDomain(Request $request)
    {

      $domain_name= $request->domain_name;
      $registrant_email= $request->registrant_email;
      $user_id= $request->user_id;
      $client = new Client(); //GuzzleHttp\Client
      $client->setDefaultOption('verify', false);
                $result = $client->get('http://api.tier5.website/api/make_free_wp_website/'.$domain_name);
               $domain_data = json_decode($result->getBody()->getContents(), true);
      //echo $domain_data['message'];

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
    public function lead_domains($email, Request $request)
    {
      try {
        if(!Auth::check()) {
          return redirect('loginPage');
        }

        $pagination = $request->has('pagination') ? $request->pagination : 10; 
        $email = decrypt($email);
        $lead = Lead::where('registrant_email', $email)->first();
        if(!$lead) {
          // $oldReq = $request->all();
          // $oldReq = isset($oldReq['request']) ? $oldReq['request'] : [];
          // $req =  new Request($oldReq);
          // return redirect()->back()->with('error', 'This lead is deleted')->withInput($req->all());

          return redirect()->back()->with('error', 'This lead is deleted');
        }

        // $leadsUnlocked = LeadUser::where('registrant_email', $email)->where('user_id', \Auth::user()->id)->first();
        // if(!$leadsUnlocked && \Auth::user()->user_type == 1) {
        //   $oldReq = $request->all();
        //   $oldReq = isset($oldReq['request']) ? $oldReq['request'] : [];
        //   $req =  new Request($oldReq);
        //   return redirect()->back()->with('error', 'You don\'t have access of this data')->withInput($req->all());
        // }
        
        $alldomains = EachDomain::with('wordpress_env')->where('registrant_email',$email);
        $alldomains = $alldomains->paginate($pagination);
        $user = Auth::user();
        $users_array = LeadUser::where('user_id',$user->id)->pluck('domain_name')->toArray();
        $users_array = array_flip($users_array);
        $restricted = true;
        $user = Auth::user();
        if($user->user_type > config('settings.PLAN.L1')) {
          $restricted = false;
        }
        return view('new_version.search.lead-domains',['alldomain'=>$alldomains , 'email'=>$email, 'user'=>$user, 'users_array' => $users_array ,'restricted' => $restricted, 'pagination' => $pagination]);
        // return view('home.lead_domains',['alldomain'=>$alldomains , 'email'=>$email]);
        
        // return view('home.',['alldomain'=>$alldomains , 'email'=>$email]);
      } catch(\Exception $e) {
        return redirect()->back()->with('error', 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine());
      }
    }

    public function unlockFromLeads(Request $request) {
      try {

        $key = $request->key;
        if(!Auth::check()) {
          return Response::json(array('status'=>false , 'message' => 'Please login once again!'));
        }
        $count = LeadUser::where('user_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->count();
        $limit = 0;
        
        // if(Auth::user()->user_type == 1) {
        //   // $limit = config('settings.LEVEL1-USER');
        //   $limit = 
        // } else if(Auth::user()->user_type == 2) {
        //   $limit = config('settings.LEVEL2-USER');
        // }

        if(Auth::user()->user_type < config('settings.PLAN.L1')) {
          $limit = config('settings.PLAN.'.Auth::user()->user_type)[0];
        }

        if($count >= $limit && $limit > 0) {
          $array['status'] = false;
          $array['message'] = 'Per day limit exceeded! Please contact administrator to upgrade usage.';
          $array['leadsUnlocked'] = $count;
          return Response::json($array);
        }

        $domainName = $request->has('domain_name') ? $request->domain_name : null;
        $data = Lead::where('registrant_email', $request->registrant_email)->first();
        $domain = $data->each_domain->filter(function($each, $key) use($domainName) {
          return $each->domain_name == $domainName ? $each : null;
        })->first();

        $leaduser = new LeadUser();
        $leaduser->user_id = $request->user_id;
        $leaduser->registrant_email = $request->registrant_email;
        
        $leaduser->registrant_country = $data->registrant_country;
        $leaduser->registrant_fname   = $data->registrant_fname;
        $leaduser->registrant_lname   = $data->registrant_lname;
        $leaduser->registrant_phone   = $data->registrant_phone;
        $leaduser->number_type        = $data->valid_phone ? $data->valid_phone->number_type : null;
        $leaduser->registrant_company = $data->registrant_company;
        $leaduser->domain_name        = count($domain) == 0 ? $data->each_domain->first()->domain_name : $domain->domain_name;
        $leaduser->domains_create_date = count($domain) == 0 ? $data->each_domain->first()->domains_info->first()->domains_create_date 
                                        : $domain->domains_info->domains_create_date;
        $leaduser->expiry_date = count($domain) == 0 ? $data->each_domain->first()->domains_info->first()->expiry_date 
                                        : $domain->domains_info->expiry_date;

        $data->unlocked_num++;
        if($data->save() && $leaduser->save())
        {
          $socketMeta = SocketMeta::first();
          $socketMeta->leads_unlocked++;
          $socketMeta->save();
          event(new UsageInfo());
          // Compose a view to render the html
          $usageMatrix = UserHelper::getUsageMatrix();
          $country_codes = country_codes();
          $country_abr = isset($country_codes[ucwords(strtolower($leaduser->registrant_country))]) 
          ? strtoupper($country_codes[ucwords(strtolower($leaduser->registrant_country))]) : null;
          
          
          $view = View::make('new_version.shared.lead-domain-row-component', [
            'each' => $domain, 
            'key' => $key, 
            'restricted' => false, 
            'email' => $data->registrant_email, 
            'country_abr'=>$country_abr])->render();

          return Response::json([
            'view'    =>  $view,
            'status'  =>  true,
            'message' =>  'success',
            'usageMatrix' => $usageMatrix
          ]);
        }
        // Previous
        // return \Response::json(array('status'=>false,'message' => 'Cannot connect with db, try again later', 'leadsUnlocked' => $count));
        Response::json([
          'view'    =>  null,
          'status'  =>  false,
          'message' =>  'Cannot connect with db, try again later',
          'usageMatrix' => isset($usageMatrix) ? $usageMatrix : null
        ]);
      } catch(\Exception $e) {
        // Previous
        // return \Response::json(array('status'=>false,'message' => 'Error : '.$e->getMessage().' LINE : '.$e->getLine()));
        return Response::json([
          'view'    =>  null,
          'status'  =>  false,
          'message' => 'Error : '.$e->getMessage().' LINE : '.$e->getLine(),
          'usageMatrix' => isset($usageMatrix) ? $usageMatrix : null
        ]);
      }
    }

    //when a common user unlocks a user
    //fired with ajax
    public function unlockleed(Request $request)
    {
      try {

        $key = $request->key;
        if(!Auth::check()) {
          return Response::json(array('status'=>false , 'message' => 'Please login once again!'));
        }
        $count = LeadUser::where('user_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->count();
        $limit = 0;
        $phone = base64_decode($request->ph);
        $phone_type = base64_decode($request->ph_type);
        // if(Auth::user()->user_type == 1) {
        //   $limit = config('settings.LEVEL1-USER');
        // } else if(Auth::user()->user_type == 2) {
        //   $limit = config('settings.LEVEL2-USER');
        // }
        if(Auth::user()->user_type < config('settings.PLAN.L1')) {
          $limit = config('settings.PLAN.'.Auth::user()->user_type)[0];
        }

        if($count >= $limit && $limit > 0) {
          $array['status'] = false;
          $array['message'] = 'Per day limit exceeded! Please contact administrator to upgrade usage.';
          $array['leadsUnlocked'] = $count;
          return Response::json($array);
        }

        $domainName = $request->has('domain_name') ? $request->domain_name : null;
        $data = Lead::where('registrant_email',$request->registrant_email)->first();
        $domain = $data->each_domain->filter(function($each, $key) use($domainName) {
          return $each->domain_name == $domainName ? $each : null;
        })->first();


        $leaduser = new LeadUser();
        $leaduser->user_id = $request->user_id;
        $leaduser->registrant_email = $request->registrant_email;
        
        $leaduser->registrant_country = $data->registrant_country;
        $leaduser->registrant_fname   = $data->registrant_fname;
        $leaduser->registrant_lname   = $data->registrant_lname;
        $leaduser->registrant_phone   = $phone; //$data->registrant_phone;
        $leaduser->number_type        = $phone_type;// $data->valid_phone ? $data->valid_phone->number_type : null;
        $leaduser->registrant_company = $data->registrant_company;
        $leaduser->domain_name        = count($domain) == 0 ? $data->each_domain->first()->domain_name : $domain->domain_name;
        $leaduser->domains_create_date = count($domain) == 0 ? $data->each_domain->first()->domains_info->first()->domains_create_date 
                                        : $domain->domains_info->domains_create_date;
        $leaduser->expiry_date = count($domain) == 0 ? $data->each_domain->first()->domains_info->first()->expiry_date 
                                        : $domain->domains_info->expiry_date;

        $data->unlocked_num++;
        if($data->save() && $leaduser->save())
        {

          $socketMeta = SocketMeta::first();
          $socketMeta->leads_unlocked++;
          $socketMeta->save();
          event(new UsageInfo());

          $array = array();
          $array['leadsUnlocked'] = $count + 1;
          $array['status'] = true;
          $array['message'] = 'Success';
          $array['id']     = $data->id;
          $array['registrant_email']    = $leaduser->registrant_email;
          $array['registrant_name']     = $leaduser->registrant_fname." ".$data->registrant_lname;
          $array['registrant_phone']    =  $leaduser->registrant_phone;
          $array['registrant_company']  = $leaduser->registrant_company;
          $array['domain_name']         = $leaduser->domain_name;
          $array['domain_name_masked']  = customMaskDomain($leaduser->domain_name);
          $array['domains_create_date'] = date('d/m/Y', strtotime($leaduser->domains_create_date));
          $array['expiry_date']         = date('d/m/Y', strtotime($leaduser->expiry_date));
          $array['unlocked_num']        = $data->unlocked_num;
          $array['registrant_country']  = $leaduser->registrant_country;
          $array['domains_count']       = $data->domains_count;
          $array['restricted']          = false;
          $array['number_type']         = $leaduser->number_type;
          //$array['total_domain_count']  = $lead->each_domain;

          // previous
          // return \Response::json($array);

          // Compose a view to render the html
          $usageMatrix = UserHelper::getUsageMatrix();
          $country_codes = country_codes();
          $country_abr = isset($country_codes[ucwords(strtolower($array['registrant_country']))]) 
          ? strtoupper($country_codes[ucwords(strtolower($array['registrant_country']))]) : null;

          $view = View::make('new_version.shared.search-row-component', ['each' => $array, 'key' => $key, 'restricted' => false, 'country_abr'=>$country_abr])->render();
          return Response::json([
            'view'    =>  $view,
            'status'  =>  true,
            'message' =>  'success',
            'usageMatrix' => $usageMatrix
          ]);
        }
        // Previous
        // return \Response::json(array('status'=>false,'message' => 'Cannot connect with db, try again later', 'leadsUnlocked' => $count));
        Response::json([
          'view'    =>  null,
          'status'  =>  false,
          'message' =>  'Cannot connect with db, try again later',
          'usageMatrix' => isset($usageMatrix) ? $usageMatrix : null
        ]);
      } catch(\Exception $e) {
        // Previous
        // return \Response::json(array('status'=>false,'message' => 'Error : '.$e->getMessage().' LINE : '.$e->getLine()));
        return Response::json([
          'view'    =>  null,
          'status'  =>  false,
          'message' => 'Error : '.$e->getMessage().' LINE : '.$e->getLine(),
          'usageMatrix' => isset($usageMatrix) ? $usageMatrix : null
        ]);
      }
    }

    public function update_metadata_today($date)
    {
      $sql = "SELECT * from search_metadata ORDER BY query_time,search_priority DESC";
      $data = DB::select(DB::raw($sql));
      $i=0;

      $last_csv_insert_date = DB::select(DB::raw('SELECT MAX(date(created_at)) as created FROM `csv_record`'));
      $last_csv_insert_date   = strtotime($last_csv_insert_date[0]->created);

      //if($date != $last_csv_insert_date[0])
      // return 'updated';



      foreach ($data as $key => $value)
      {
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

        // TODO :: CHECK
        
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
            else if($key == 'domain_ext' && $req != null && $domain_ext_str != '()')
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

        try{
          $t1 = microtime(true);
          $leads = DB::select(DB::raw($sql));
          $t2 = microtime(true);
          $query_time = $t2-$t1;

          $this->counting_leads_domains($leads); //update the new count
          //$this->count_total_pages($request->pagination);
          $status = $this->update_metadata_full($value->id,$this->compress($this->leads_id) ,$query_time);
        }
        catch(\Exception $e)
        {
          \Log::info($e->getMessage());
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
      if(strlen($leads_str) == 0) {
        return [];
      }
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
      $leads = DB::select(DB::raw($sql));
      return $leads;
    }
    /* search leads on basis of the search parameters given

    complexity of query [with inner join]: O(l+ed+di+vp) + O(log(l+ed+di+vp))
                        [with left join] : O(l+ed+di)*O(log(vp)) + O(log((l+ed+di)*log(vp)))
                        [without joining valid phone]
                                         : O(l+ed+di) + O(log(l+ed+di))

    */
    private function leads_Search(Request $request)
    {
      // TODO :: CHECK
      $date_flag = 0;
      $phone_type_array = $this->phone_type_array;
      $domain_ext_str   = $this->domain_ext_str; 
      $this->setMysqlVars();
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
            , di.expiry_date";
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
                else if($key == 'domain_ext' && $domain_ext_str != '()')
                {
                    $sql .= " and ed.domain_ext IN ".$domain_ext_str." ";
                }
                // else if(($key == 'domains_create_date' || $key == 'domains_create_date2')
                //   && $date_flag == 0)
                // {
                //     $date_flag = 1;
                //     $dates_array = generateDateRange($request->domains_create_date,
                //                     $request->domains_create_date2);

                //     if(isset($dates_array))
                //     {
                //       if(sizeof($dates_array) == 1) $sql .= " and di.domains_create_date = '"
                //                                          .$dates_array[0]."' ";

                //       else if(sizeof($dates_array) == 2) $sql .= " and di.domains_create_date >= '"
                //                                         .$dates_array[0]."' and di.domains_create_date <= '"
                //                                         .$dates_array[1]."'";
                //     }
                // }
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
                // else if($key == 'mode' && $req == 'getting expired')
                // {
                //     $sql .= " and di.expiry_date >= date(now()) and
                //               di.expiry_date <= date(now()+interval 30 day)";
                // }
              }
            }
            
            if($request->mode == 'newly_registered' || $request->mode == null) {
              $dates_array = generateDateRange($request->domains_create_date, $request->domains_create_date2);
              if(isset($dates_array)) {
                if(sizeof($dates_array) == 1) {
                  $sql .= " and di.domains_create_date = '" .$dates_array[0]."' ";
                } else if(sizeof($dates_array) == 2) {
                  $sql .= " and di.domains_create_date >= '" .$dates_array[0]."' and di.domains_create_date <= '".$dates_array[1]."'";
                }
              }
            } else if($request->mode == 'getting_expired') {
              $dates_array = generateDateRange($request->domains_expired_date, $request->domains_expired_date2);
              
              if(isset($dates_array)) {
                if(sizeof($dates_array) == 1) {
                  $sql .= " and di.expiry_date = '" .$dates_array[0]."' ";
                } else if(sizeof($dates_array) == 2) {
                  $sql .= " and di.expiry_date >= '" .$dates_array[0]."' and di.expiry_date <= '".$dates_array[1]."'";
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
            
            $sql .= " GROUP BY l.registrant_email";

            if(isset($request->sort))
            {
                $req = $request->sort;
                if($req == 'unlocked_asnd')  $sql .= " ORDER BY l.unlocked_num ASC ";
                else if($req == 'unlocked_dcnd') $sql .= " ORDER BY l.unlocked_num DESC ";
                else if($req == 'domain_count_asnd')  $sql .= " ORDER BY l.domains_count ASC ";
                else if($req == 'domain_count_dcnd')  $sql .= " ORDER BY l.domains_count DESC";
            }
            
            // echo $sql;die();
	    // Log::info("LEADS QUERY AS NOT CACHED : ".$sql);
            $leads = DB::select(DB::raw($sql));
            
            return $leads;
    }

    private function leadsPerPage_Search($leads)
    {
        
        $leads_string = '';
        $totalLeads = count($leads);
        // $leadsid_per_page   = array();
        $i=$x=$z=$totalDomains=$lastz=0;
        $data = array();
        for($i=0 ; $i<$totalLeads ; $i++)
        {
          if(isset($leads[$i]))
          {
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

            if($leads_string == '') $leads_string .= '"'.$leads[$i]->registrant_email.'"';
            else                     $leads_string .= ',"'.$leads[$i]->registrant_email.'"';
          }
        }
        $leads_string = "(".$leads_string.")";
        return array('data'=>$data,'leads_string'=>$leads_string);
    }

    /*
      Given a set of string of registrant_email in string perform select query on domains table
      based on -- domain_name , domain_ext , and domains_create_date fields
    */
    private function domainsPerPage_Search(Request $request, $param, $phone_type_array ,$leads_string)
    {
      $phone_type_array = $this->phone_type_array;
      $domain_ext_str   = $this->domain_ext_str;
      $date_flag = 0;

      if($leads_string != '()')
      {
        $sql = " SELECT ed.domain_name, ed.domain_ext, ed.registrant_email ,di.domains_create_date,di.expiry_date,vp.number_type, vp.phone_number FROM `each_domain` ed
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
          }
        }
        
        if($request->mode == 'newly_registered' || $request->mode == null)
        {
          $dates_array = generateDateRange($param['domains_create_date'], $param['domains_create_date2']);
          if(isset($dates_array))
          {
            if(sizeof($dates_array) == 1) $sql .= " and di.domains_create_date = '"
                                                .$dates_array[0]."' ";

            else if(sizeof($dates_array) == 2) $sql .= " and di.domains_create_date >= '"
                                                    .$dates_array[0]
                                                    ."' and di.domains_create_date <= '"
                                                    .$dates_array[1]."'";
          }
        } else if($request->mode == 'getting_expired') {
          
          $dates_array = generateDateRange($request->domains_expired_date, $request->domains_expired_date2);
          if(isset($dates_array)) {
            if(sizeof($dates_array) == 1) $sql .= " and di.expiry_date = '".$dates_array[0]."' ";

            else if(sizeof($dates_array) == 2) $sql .= " and di.expiry_date >= '".$dates_array[0]."' and di.expiry_date <= '".$dates_array[1]."'";
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
        //echo $sql; exit();
	// Log::info("query : ".$sql);
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
      // dd($request->all());
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
      $offset = isset($request->page)
                  ? $request->page
                  : 1;

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
          // else if(($key == 'domains_create_date' || $key == 'domains_create_date2')
          //   && $date_flag == 0)
          // {
          //   $date_flag = 1;
          //   $dates_array = generateDateRange($request->domains_create_date,
          //                   $request->domains_create_date2);

          //   if(isset($dates_array))
          //   {
          //     if(sizeof($dates_array) == 1)
          //     {
          //       $sql .= " and domains_create_date1 = '".$dates_array[0]."' ";
          //       $input['domains_create_date'] = true;
          //     }
          //     else if(sizeof($dates_array) == 2)
          //     {
          //       $sql .= " and domains_create_date1 = '".$dates_array[0]."' and domains_create_date2 = '".$dates_array[1]."'";
          //       $input['domains_create_date'] = true;
          //       $input['domains_create_date2'] = true;
          //     }
          //   }
          // }
          else if($key == 'leadsunlocked_no')
          {
              if($gt_ls_leadsunlocked_no == '') {
                continue;
              } 
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
          // else if($key == 'mode' && $req == 'getting_expired')
          // {
          //     $sql .= " and expiry_date >= date(now()) and
          //              expiry_date <= date(now()+interval 30 day)";
          // }
          // else if($key == 'mode' && $req == 'newly_registered')
          // {
          //     $sql .= " and expiry_date >= date(now())";
          // }
        }
      }

      // Filter by newly registered domains or expiring domains
      
      if($request->mode == 'newly_registered' || $request->mode == null) {
        $dates_array = generateDateRange($request->domains_create_date, $request->domains_create_date2);
        if(isset($dates_array)) {
          if(sizeof($dates_array) == 1) {
            $sql .= " and domains_create_date1 = '".$dates_array[0]."' ";
            $input['domains_create_date'] = true;
          } else if(sizeof($dates_array) == 2) {
            $sql .= " and domains_create_date1 = '".$dates_array[0]."' and domains_create_date2 = '".$dates_array[1]."' ";
            $input['domains_create_date'] = true;
            $input['domains_create_date2'] = true;
          }
        }
      } else if($request->mode == 'getting_expired') {
        $dates_array = generateDateRange($request->domains_expired_date, $request->domains_expired_date2);
        if(isset($dates_array)) {
          if(sizeof($dates_array) == 1) {
            $sql .= " and expiry_date = '".$dates_array[0]."' ";
            $input['expiry_date'] = true;
          } else if(sizeof($dates_array) == 2) {
            $sql .= " and expiry_date = '".$dates_array[0]."' and expiry_date2 = '".$dates_array[1]."' ";
            $input['expiry_date'] = true;
            $input['expiry_date2'] = true;
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
              case 'expiry_date' : 
                    if(!$input['expiry_date2'])
                    $sql .= " and expiry_date IS NULL and expiry_date2 IS NULL";
                    else 
                    $sql .= " and expiry_date IS NULL";
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

      // echo($sql);exit();
      $meta_data_leads = DB::select(DB::raw($sql));
      
      // No leads are cached so actual search is implemented
      if($meta_data_leads == null) {
        Log::info('no cached search ');
        $t1 = microtime(true);
        $leads = $this->leads_Search($request);
        $t2 = microtime(true);
        $query_time = $t2-$t1;
        
        $this->counting_leads_domains($leads);
        $this->count_total_pages($request->pagination);
        

        if(sizeof($leads) > 0) {
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
            \Log::info('error in checkMetadata_Search ***** ');
            // dd('error in checkMetadata_Search');
        } else {
         return null;
        }
      } else {
        
        $last_csv_insert_time = DB::select(DB::raw('SELECT MAX(created_at) as created FROM `csv_record`'));
        $last_query_update_time = strtotime($meta_data_leads[0]->updated_at);
        $last_csv_insert_time   = strtotime($last_csv_insert_time[0]->created);
        Log::debug('last_csv_insert_time: '. $last_csv_insert_time .' | $meta_data_id : '. $meta_data_leads[0]->id);
        
        if($last_query_update_time > $last_csv_insert_time)
        {
          Log::info('cached search - but update first');
          $this->update_metadata_partial($meta_data_leads[0]->id);
          $raw_leads_id = $this->uncompress($meta_data_leads[0]->leads
                                  ,$meta_data_leads[0]->compression_level);

          $this->totalDomains = $meta_data_leads[0]->totalDomains;
          $this->totalLeads   = $meta_data_leads[0]->totalLeads;
          $this->count_total_pages($request->pagination);

          $raw_leads_id = $this->paginated_raw_leads($raw_leads_id,$limit,$offset);
          return $this->raw_leads($raw_leads_id);
        } else {
          Log::info('cached search');
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
      if($offset == 1) $offset--;

      for($i=$offset ; $i<$limit+$offset; $i++)
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
      if($pagination == null || $pagination == 0 || $pagination == '')
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
      // dd('error in update_metadata_partial');
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
      // dd('error in update_metadata_full');
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

      if($request->mode == 'newly_registered' || $request->mode == null) {
        $meta->domains_create_date1    = isset($dates_array[0]) ? $dates_array[0] : null;
        $meta->domains_create_date2    = isset($dates_array[1]) ? $dates_array[1] : null;
      } else if($request->mode == 'getting_expired') {
        $meta->expiry_date    = isset($dates_array[0]) ? $dates_array[0] : null;
        $meta->expiry_date2    = isset($dates_array[1]) ? $dates_array[1] : null;
      }
      
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
      // dd('problem in insert metadata function');
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
        $this->domain_ext_str = '('.trim($this->domain_ext_str).')';
      }
    }

    private function ajax_paginated_search_algo(Request $request) {
      $sql    = "SELECT leads,compression_level from search_metadata
                where id = ".$request->meta_id;
      $data   = DB::select(DB::raw($sql));
      $leads  = $this->uncompress($data[0]->leads,$data[0]->compression_level);
      $data   = null;
      $offset = ($request->thisPage-1) * $request->pagination;
      $limit  = $request->pagination;

      $leads_str  = $this->paginated_raw_leads($leads,$limit,$offset);
      $leads  = $this->raw_leads($leads_str);
      $array  = $this->leadsPerPage_Search($leads);
      $param  = ['domain_name'=>$request->domain_name
               ,'domain_ext' =>$request->domain_ext
               ,'domains_create_date'=>$request->domains_create_date
               ,'domains_create_date2'=>$request->domains_create_date2];
      $data   = $array['data'];
      $domains= $this->domainsPerPage_Search($request, $param,$request->phone_type_array,$array['leads_string']);
      $data = $this->domains_output_Search($data,$domains);

      //dd($data);
      unset($domain_list);
      unset($leads);
      unset($sql);
      unset($param);
      unset($domains);
      return $data;
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
      $data = $this->ajax_paginated_search_algo($request);
      $time = microtime(true) - $start;
      return \Response::json(array('data'=>$data , 'time'=>$time));
    }

    public function ajax_search_paginated_subadmin(Request $request) {
      try {
        if(!\Auth::check()) {
          return \Response::json(['status' => false, 'message' => 'Session expired' ,'view' => null]);  
        }
        $start = microtime(true);
        $request['domain_ext'] = $this->tldExtToArray($request->domain_ext);
        $data = $this->ajax_paginated_search_algo($request);
        $result['record'] = $data;
        $result['page']   = $request->thisPage;
        $result['meta_id'] = $this->meta_id;
        $result['totalLeads'] = $this->totalLeads;
        $result['totalDomains'] = $this->totalDomains;
        $result['totalPage'] = $this->totalPage;
        // $result['domain_list'] = isset($domain_list) ? $domain_list : null;
        $result['query_time'] = microtime(true) - $start;
        $result['time'] = microtime(true) - $start;

        $result['restricted'] = true;
        $user = Auth::user();
        // if($user->user_type == 4 || $user->user_type == 3) {
        //   // $result['restricted'] = false;

        // }

        if($user->user_type > config('settings.PLAN.L1')) {
          $result['restricted'] = false;
        }

        // $result['oldReq'] = Session::has('oldReq') ? Session::get('oldReq') : null;

        $user_id = $user->id;
        $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
        $users_array = array_flip($users_array);
        $result['users_array'] = $users_array;
        $result['user'] = $user;
        // Previous data
        // $view = View::make('home.search.searchTable', $result)->render();
        // return \Response::json(['status' => true, 'message' => 'Success' ,'view' => $view]);

        $view = View::make('new_version.shared.search-results-table', $result)->render();
        return \Response::json(['status' => true, 'message' => 'Success' ,'view' => $view]);

      } catch(\Exception $e) {
        return \Response::json(['status' => false, 'message' => 'ERROR : '.$e->getMessage().' LINE : '.$e->getLine() ,'view' => null]);
      }
    }


    // Binding 2 tables - leads - each_domain -> dataset into 1 array
    private function domains_output_Search($data , $domains)
    {
      // dd($data, $domains);
      if($domains == null || $data == null) {
        return [];
      }

      $domain_list = array();

      foreach($data as $k=>$v)  $domain_list[$v['registrant_email']]['checked'] = false;
      
      foreach($domains as $k=>$v)
      {
        if(!($domain_list[$v->registrant_email]['checked'])) {
          $domain_list[$v->registrant_email]['checked']             = true;
          $domain_list[$v->registrant_email]['domain_name']         = $v->domain_name;
          $domain_list[$v->registrant_email]['domain_ext']          = $v->domain_ext;
          $domain_list[$v->registrant_email]['domains_create_date'] = $v->domains_create_date;
          $domain_list[$v->registrant_email]['number_type']         = $v->number_type;
          $domain_list[$v->registrant_email]['expiry_date']         = $v->expiry_date;
          $domain_list[$v->registrant_email]['phone_number']        = $v->phone_number;
          // $domain_list[$v->registrant_email]['all_numbers'][]       = $v->phone_number;
        } 
        // else if($v->phone_number) {
        //   $domain_list[$v->registrant_email]['all_numbers'][]       = $v->phone_number;
        // }
      }
      // dd($domain_list);
      foreach ($data as $key => $value)
      {
        
        // $phone = explode('.',$value['registrant_phone']);
        // $phone = isset($phone[1]) ? $phone[1] : $phone[0];

        //Logic changed to show original phone
        // $phone = $value['registrant_phone'];
        // $data[$key]['registrant_phone']     = $phone;

        $data[$key]['registrant_phone']     = isset($domain_list[$value['registrant_email']]['domain_name']) && isset($domain_list[$value['registrant_email']]['phone_number'])
                                                ? $domain_list[$value['registrant_email']]['phone_number']
                                                : $value['registrant_phone'];

        // $data[$key]['all_numbers']          = isset($domain_list[$value['registrant_email']]['domain_name']) 
        //                                         ? $domain_list[$value['registrant_email']]['all_numbers']
        //                                         : [];

        $data[$key]['domain_name']          = isset($domain_list[$value['registrant_email']]['domain_name'])
                                              ? $domain_list[$value['registrant_email']]['domain_name']
                                              : null;

        $data[$key]['domain_name_masked']   = customMaskDomain($data[$key]['domain_name']);


        $data[$key]['domain_ext']           = isset($domain_list[$value['registrant_email']]['domain_ext'])
                                              ? $domain_list[$value['registrant_email']]['domain_ext']
                                              : null;
        $data[$key]['domains_create_date']  = isset($domain_list[$value['registrant_email']]['domains_create_date'])
                                              ? date('d/m/Y', strtotime($domain_list[$value['registrant_email']]['domains_create_date']))
                                              : null;
        
        $data[$key]['expiry_date']          = isset($domain_list[$value['registrant_email']]['expiry_date'])
                                              ? date('d/m/Y', strtotime($domain_list[$value['registrant_email']]['expiry_date']))
                                              : null;
                                      
        $data[$key]['number_type']          = isset($domain_list[$value['registrant_email']]
                                              ['number_type'])
                                              ? $domain_list[$value['registrant_email']]['number_type']
                                              : null;

        $data[$key]['email_link']           = encrypt($data[$key]['registrant_email']);

        $data[$key]['registrant_country']   = $data[$key]['registrant_country'];
      }
      // dd($data);
      return $data;
    }

    private function setMysqlVars() {
      $sql = $sql = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
      return DB::select(DB::raw($sql));
    }

    private function search_algo(Request $request)
    {
      $start = microtime(true);
      if($request->has('registrant_country')) {
        $request['registrant_country'] = getCountryName($request->registrant_country);
      }
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
        $domains  = $this->domainsPerPage_Search($request, $param,$this->phone_type_array ,$leads_string);
        $data     = $this->domains_output_Search($data,$domains);
      }

      $end = microtime(true)-$start;
      $return =  [ 'record'         => $data,
              'page'                => 1,
              'meta_id'             => $this->meta_id,
              'totalLeads'          => $this->totalLeads,
              'totalDomains'        => $this->totalDomains,
              'totalPage'           => $this->totalPage,
              'pagination'          => $request->has('pagination') ? $request->pagination : 10,
              'domain_list'         => isset($domain_list) ? $domain_list : null,
              'query_time'          => $end
            ];
      return $return;
    }

    public function search_api(Request $request)
    {
        $status = 'ok';
        $result = null;
        try {
           
          //$request['pagination'] = 10;
          $start  = microtime(true);
          $offset = $request->offset;
          $limit  = $request->has("limit") && $request->limit > 0 ? $request->limit : 10;
          if($request->has('domain_ext')) {
            $request['domain_ext'] = $this->tldExtToArray($request->domain_ext);
          }
          
          $result = $this->search_algo($request);
          // Session::put('oldReq', $request->all());
          $end    = microtime(true)-$start;
          
          $phone_type_array = array();
          if(isset($request->cell) && $request->cell != null)
            array_push($phone_type_array, 'Cell Number');

          if(isset($request->landline) && $request->landline != null)
            array_push($phone_type_array, 'Landline');

          if($this->meta_id == null) {
            return response()->json(array('result' => null, 'status' => $status, 'nextUrl' => null));
          }
          $result = $this->all_lead_domains_set($request,$phone_type_array,$this->meta_id, $limit, $offset);
        } catch(\Exception $e) {
          $status = $e->getMessage();
        }
        
        if(count($result) < $limit) {
          $url = null;
        } else {
          if($request->has('domain_ext')) {
            $request['domain_ext'] = $request->has('domain_ext') ? $this->tldArrayToExt($request->domain_ext) : '';
          }
          $newRequest = $request->all();
          $newRequest['offset'] = $offset + 1;
          $url = explode('?', \Request::url())[0].getQueryParamsCustom($newRequest);
        }
        return response()->json(array('result' => $result, 'status' => $status, 'nextUrl' => $url));
    }

    private function tldExtToArray($tldStr) {
      if(strtolower(gettype($tldStr)) == 'array') {
        return $tldStr;
      }
      $arr = array_filter(explode(',', $tldStr));
      return $arr;
    }

    private function tldArrayToExt($tldArr) {
      if(strtolower(gettype($tldArr)) == 'string') {
        return $tldArr;
      }
      $ext = trim(implode(',', $tldArr));
      return $ext;
    }

    public function search(Request $request)
    {
      
      ini_set('max_execution_time', 346000);
      if(Auth::check())
      {
        if($request->all())
        {
          // dd($request->all());
          // dd($request->all(), $request->has('pagination')); 
          $request['pagination']  = $request->has('pagination') ? $request->pagination : 10;
          $request['domain_ext']  = $this->tldExtToArray($request->domain_ext);
          Log::info('inp : ', $request->all());

          $start = microtime(true);
          $result = $this->search_algo($request);
          $end = microtime(true)-$start;

          $user = Auth::user();
          if($user->user_type > config('settings.PLAN.L1')) {
            $result['restricted']   = false;
            $result['user']         = $user;
            $result['restricted']   = false;
            $request['domain_ext']  = $request->has('domain_ext') ? $this->tldArrayToExt($request->domain_ext) : '';
            Session::forget('oldReq');
            Session::put('oldReq', $request->all());
            return view('new_version.search.search-results', $result);
            // return view('home.admin.admin_search',$result);
          }

          // $return['totalUnlockAbility']  = 'unlimited';
          // user type 1 can unlock 50 leads
          // user type 2 can unlock 50 leads
          // user type 3 can unlock 150 leads
          
          $result['totalUnlockAbility']  =  config('settings.PLAN.'.$user->user_type)[0] < 0 
                                            ? 'unlimited' 
                                            : config('settings.PLAN.'.$user->user_type)[0];  //config('settings.LEVEL'.Auth::user()->user_type.'-USER');

          $user_id = Auth::user()->id;
          $users_array = LeadUser::where('user_id',$user_id)->pluck('registrant_email')->toArray();
          $users_array = array_flip($users_array);
          $result['users_array'] = $users_array;
          $result['restricted'] = true;
          $result['user'] = Auth::user();
          
          $request['domain_ext'] = $request->has('domain_ext') ? $this->tldArrayToExt($request->domain_ext) : '';
          Session::forget('oldReq');
          Session::put('oldReq', $request->all());
          
          // return view('home.search.search',$result);
          return view('new_version.search.search-results',$result);
        
        } else {
          Session::forget('emailID_list');
          Session::forget('oldReq');
          $allrecords = null;
          $leadArr = null;
          $totalDomains = null;
          $user = Auth::user();
          $allExtensions = ['au','ar','an', 'ca', 'com', 'co', 'ch', 'de', 'es', 'jp', 'edu', 'fr', 'gov', 'io', 'in', 'it', 'info', 'jobs', 'mil', 'mobi', 'net', 'nl', 'no', 'org', 'onion', 'ru', 'us', 'uk', 'se', 'travel', 'pro'];
          return view('new_version.dashboard.index', ['record' => null , 'leadArr' => null , 'totalDomains' => null, 'user' => $user, 'allExtensions' => $allExtensions]);
        }
      } else {
        return redirect('home');
      }
    }

    public function getOldestDate() {
      $results = DB::select('SELECT MIN( domains_create_date ) AS min_date FROM domains_info');
      if ($results) {
        return $results;
      } else {
        return null;
      }
    }
}
