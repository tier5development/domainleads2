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
use Auth, View;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Session;
use Excel;
use Input;
use Illuminate\Pagination\Paginator;
use \Carbon\Carbon as Carbon;


class UserController extends Controller
{
    public function myUnlockedLeads(Request $request) {
        if(Auth::check() && Auth::user()->user_type == 1) {
            $date = $request->has('date') ? $request->date : null;
            $data['perpage'] = $request->has('perpage') ? $request->perpage : 20;
            $user = Auth::user();
            $data['leads'] = LeadUser::where('user_id', $user->id);
            if($date) {
                // dd($date);
                $data['leads'] = $data['leads']->whereDate('created_at', $date);
            }
            $data['leads'] = $data['leads']->orderBy('id', 'ASC')->paginate($data['perpage']);
            $data['title'] = 'Unlocked leads | Domainleads';
            return view('home.search.my-unlocked-domains', $data);
        } else {
            return redirect('search');
        }
    }

    public function downloadUnlockedLeads(Request $request) {
        $result = DB::table('leads')
            ->join('leadusers', 'leads.registrant_email', '=', 'leadusers.registrant_email')
            ->join('each_domain', function($join) {
              $join->on('each_domain.registrant_email', '=', 'leads.registrant_email');
            })->join('domains_info', 'each_domain.domain_name', '=', 'domains_info.domain_name')
            ->leftJoin('valid_phone', 'leads.registrant_email', '=', 'valid_phone.registrant_email')
            ->select('leads.registrant_email', 'leads.registrant_fname', 'registrant_lname'
              ,'leads.registrant_company', 'leads.registrant_phone' 
              ,'domains_info.created_at'
              ,'each_domain.domain_name'
              ,'valid_phone.number_type'
              ,'leadusers.id')
              ->where('leadusers.user_id', \Auth::user()->id)
              ->groupBy('leads.registrant_email')
              ->orderBy('leadusers.id','ASC')
              ->get();
        $exportArray = [];
        foreach($result as $each) {
          $temp['email_id'] = $each->registrant_email;
          $temp['first_name'] = $each->registrant_fname;
          $temp['last_name'] = $each->registrant_lname;
          $temp['website'] = $each->domain_name;
          $temp['phone'] = $each->registrant_phone;
          $temp['number_type'] = $each->number_type;
          $temp['created_at'] = $each->created_at;
          $exportArray[] = $temp;
        }
      
        return Excel::create('domainleads', function($excel) use ($exportArray) {
          $excel->sheet('mySheet', function($sheet) use ($exportArray){
            $sheet->fromArray($exportArray);
          });
        })->download('csv');
    }
}

