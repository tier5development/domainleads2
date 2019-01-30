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
use Auth, View, Response, Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;
use Input, DateTime;
use Illuminate\Pagination\Paginator;
use \Carbon\Carbon as Carbon;
use App\Helpers\UserHelper;

class SearchControls extends Controller
{
    public function searchLeads(Request $request) {
        
        $query = Lead::join('each_domain', 'each_domain.registrant_email', '=', 'leads.registrant_email')
        ->join('domains_info', 'domains_info.domain_name', '=', 'each_domain.domain_name');
  
        foreach ($request->all() as $key => $val) {
            switch($key) {
                case 'registrant_country' : 
                  if(strlen(trim($val)) == 0) continue;
                  $query = $query->where('leads.registrant_country', 'LIKE', $val);
                  break;
  
                case 'registrant_state' : 
                  if(strlen(trim($val)) == 0) continue;
                  $query = $query->where('leads.registrant_state', 'LIKE', $val);
                  break;
  
                case 'registrant_zip' : 
                  if(strlen(trim($val)) == 0) continue;
                  $query = $query->where('leads.registrant_zip', 'LIKE', $val);
                  break;
  
                case 'domain_name' : 
                  if(strlen(trim($val)) == 0) continue;
                  $query = $query->where('each_domain.domain_name', 'LIKE', '%'.$val.'%');
                  break;
  
                case 'domain_ext' : 
                  if(strlen(trim($val)) == 0) continue;
                  $query = strlen(trim($val)) > 0 
                      ? $query->whereIn('each_domain.domain_ext', $this->tldExtToArray($val))
                      : $query;
                break;
  
                case 'mode' && strlen($val) > 0 : 
                  if(strlen(trim($val)) == 0) continue;
                  if($val == 'newly_registered' || $val == null) {
                      $datesArr = generateDateRange($request->domains_create_date, $request->domains_create_date2);
                      if(isset($datesArr)) {
                          $query = $query->where('domains_info.domains_create_date', '>=', $datesArr[0]);
                          if(sizeof($datesArr) == 2) {
                              $query = $query->where('domains_info.domains_create_date', '<=', $datesArr[1]);
                          }
                      }
                  } else if($val == 'getting_expired' || $val == null) {
                      $datesArr = generateDateRange($request->domains_expired_date, $request->domains_expired_date2);
                      if(isset($datesArr)) {
                          $query = $query->where('domains_info.expiry_date', '>=', (string)$datesArr[0]);
                          if(sizeof($datesArr) == 2) {
                              $query = $query->where('domains_info.expiry_date', '<=', (string)$datesArr[1]);
                          }
                      }
                  }
                default : break;
            }
        }
        $results = $query->distinct('leads.id')->pluck('leads.id');
    }

    private function tldExtToArray($tldStr) {
        if(strtolower(gettype($tldStr)) == 'array') {
            return $tldStr;
        }
        $arr = array_filter(explode(',', $tldStr));
        return $arr;
    }

    private function checkMetadata_Search(Request $request)
    {
        $input = $this->estimated_input_fields();
        $date_flag = 0;
        $phone_type_meta  = $this->phone_type_array;
        $domain_ext_meta  = $this->domain_ext_arr;
        $dates_array      = array();
        $leads_unlocked_operator = '';
        $domains_count_operator  = '';
        $limit    =  $request->pagination == null ? 10 : $request->pagination;
        $offset   = isset($request->page) ? $request->page : 1;

        switch($request->gt_ls_leadsunlocked_no) {
            case 0 : $gt_ls_leadsunlocked_no=''; break;
            case 1 : $gt_ls_leadsunlocked_no='>'; break;
            case 2 : $gt_ls_leadsunlocked_no='<'; break;
            case 3 : $gt_ls_leadsunlocked_no='='; break;
            default: break;
        }

        switch ($request->gt_ls_domaincount_no) {
            case 0 : $gt_ls_domaincount_no=''; break;
            case 1 : $gt_ls_domaincount_no='>'; break;
            case 2 : $gt_ls_domaincount_no='<'; break;
            case 3 : $gt_ls_domaincount_no='='; break;
            default: break;
        }

        $sql = SearchMetadata::select('id', 'leads', 'compression_level', 'totalLeads', 'totalDomains')->where('leads', '!=', '');
        // $sql = "SELECT id,leads,compression_level,`totalLeads`,`totalDomains`,updated_at from search_metadata WHERE leads != '' ";
        foreach ($request->all() as $key => $req) {

            if(!is_null($request->$key) && $req != '') {
                if($key == 'registrant_country') {
                    $sql = $sql->where('registrant_country', 'LIKE', $req);
                    $input[$key] = true;
                }
                else if($key == 'registrant_state') {
                    $sql = $sql->where('registrant_state', 'LIKE', $req);
                    $input[$key] = true;
                }
                else if($key == 'registrant_zip') {
                    $sql = $sql->where('registrant_zip', 'LIKE', $req);
                    $input[$key] = true;
                }
                else if($key == 'domain_name') {
                    $sql->where('domain_name', '=', $req);
                    $input[$key] = true;
                }
                else if($key == 'domain_ext') {
                    if(count($domain_ext_meta) > 0) {
                        $sql->whereIn('domain_ext', '=', $domain_ext_meta);
                        $input[$key] = true;
                    }
                }

                else if($key == 'leadsunlocked_no') {
                    if($gt_ls_leadsunlocked_no == '') continue;
                    else if($gt_ls_leadsunlocked_no != '' && is_null($req)) continue;
                    if($req == '')  $req=0;

                    $sql = $sql->where('unlocked_num', $req)->where('leads_unlocked_operator', '=', $gt_ls_leadsunlocked_no);
                    // $sql .= " and unlocked_num = ".$req." and leads_unlocked_operator = '".$gt_ls_leadsunlocked_no."'";
                    $input[$key] = true;
                    $input['gt_ls_leadsunlocked_no'] = true;
                    $leads_unlocked_operator = $gt_ls_leadsunlocked_no;
                }

                else if($key == 'domaincount_no') {
                    if($gt_ls_domaincount_no == '') continue;
                    else if($gt_ls_domaincount_no != '' && is_null($req)) continue;
                    if($req=='') $req = 0;
                    $sql = $sql->where('domains_count', $req)->where('domains_count_operator', '=', $gt_ls_domaincount_no);
                    $input[$key] = true;
                    $input['gt_ls_domaincount_no'] = true;
                    $domains_count_operator = $gt_ls_domaincount_no;
                }
            }
        }

        // Filter by newly registered domains or expiring domains
        if($request->mode == 'newly_registered' || $request->mode == null) {
            $dates_array = generateDateRange($request->domains_create_date, $request->domains_create_date2);
            if(isset($dates_array)) {
                $sql = $sql->where('domains_create_date1', '=', $dates_array[0]);
                if(sizeof($dates_array) == 2) {
                    $sql = $sql->where('domains_create_date2', '=', $dates_array[1]);
                    $input['domains_create_date'] = true;
                    $input['domains_create_date2'] = true;
                }
            }
        } else if($request->mode == 'getting_expired') {
            $dates_array = generateDateRange($request->domains_expired_date, $request->domains_expired_date2);
            if(isset($dates_array)) {
                $sql = $sql->where('expiry_date', '=', $dates_array[0]);
                if(sizeof($dates_array) == 2) {
                    $sql = $sql->where('expiry_date2', '=', $dates_array[1]);
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
          // return view('home.search.search' , ['record' => null , 'leadArr'=>null , 'totalDomains'=>null]);
          // return view('home.search.search-box' , ['record' => null , 'leadArr' => null , 'totalDomains' => null, 'user' => $user]);
          return view('new_version.dashboard.index', ['record' => null , 'leadArr' => null , 'totalDomains' => null, 'user' => $user, 'allExtensions' => $allExtensions]);
        }
      } else {
        return redirect('home');
      }
    }

}
