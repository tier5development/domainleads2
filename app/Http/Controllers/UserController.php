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
        // dd($request->all());
        if(Auth::check() && Auth::user()->user_type <= 3) {
            $date = $request->has('date') ? $request->date : null;
            $data['perpage'] = $request->has('perpage') ? $request->perpage : 10;
            $user = Auth::user();
            $data['leads'] = LeadUser::where('user_id', $user->id);
            if($date) {
                // dd($date);
                $data['leads'] = $data['leads']->whereDate('created_at', $date);
            }
            $data['leads'] = $data['leads']->orderBy('id', 'ASC')->paginate($data['perpage']);
            $data['title'] = 'Unlocked leads | Domainleads';
            $data['user'] = Auth::user();
            // return view('home.search.my-unlocked-domains', $data);
            return view('new_version.leads.my-unlocked-leads', $data);
        } else {
            return redirect('search');
        }
    }

    // public function downloadUnlockedLeads(Request $request) {
    //     $date = $request->has('date') ? $request->date : null;
    //     $result = DB::table('leads')
    //         ->join('leadusers', 'leads.registrant_email', '=', 'leadusers.registrant_email')
    //         ->join('each_domain', function($join) {
    //           $join->on('each_domain.registrant_email', '=', 'leads.registrant_email');
    //         })->join('domains_info', 'each_domain.domain_name', '=', 'domains_info.domain_name')
    //         ->leftJoin('valid_phone', 'leads.registrant_email', '=', 'valid_phone.registrant_email')
    //         ->select('leads.registrant_email', 'leads.registrant_fname', 'registrant_lname'
    //           ,'leads.registrant_company', 'leads.registrant_phone' 
    //           ,'domains_info.created_at'
    //           ,'each_domain.domain_name'
    //           ,'valid_phone.number_type'
    //           ,'leadusers.id')
    //           ->where('leadusers.user_id', \Auth::user()->id);
    //           if($date) {
    //             $result = $result->whereDate('leadusers.created_at', $date);
    //           }
    //           $result = $result->groupBy('leads.registrant_email')
    //           ->orderBy('leadusers.id','ASC')
    //           ->get();
    //     $exportArray = [];
    //     foreach($result as $each) {
    //       $temp['email_id'] = $each->registrant_email;
    //       $temp['first_name'] = $each->registrant_fname;
    //       $temp['last_name'] = $each->registrant_lname;
    //       $temp['website'] = $each->domain_name;
    //       $temp['phone'] = $each->registrant_phone;
    //       $temp['number_type'] = $each->number_type;
    //       $temp['created_at'] = $each->created_at;
    //       $exportArray[] = $temp;
    //     }
      
    //     return Excel::create('domainleads', function($excel) use ($exportArray) {
    //       $excel->sheet('mySheet', function($sheet) use ($exportArray){
    //         $sheet->fromArray($exportArray);
    //       });
    //     })->download('csv');
    // }


    public function downloadUnlockedLeads(Request $request) {
        $date = $request->has('date') ? $request->date : null;
        if(!\Auth::check()) {
            return redirect('search');
        }

        $result = LeadUser::where('user_id', \Auth::user()->id);
        if($date) {
            $result = $result->whereDate('created_at', $date);
        }
        $result = $result->orderBy('id','ASC')->get();
        $exportArray = [];
        foreach($result as $i=>$each) {
          $temp['Sl no'] = $i+1;
          $temp['email_id'] = $each->registrant_email;
          $temp['first_name'] = $each->registrant_fname;
          $temp['last_name'] = $each->registrant_lname;
          $temp['website'] = $each->domain_name;
          $temp['phone'] = $each->registrant_phone;
          $temp['number_type'] = $each->number_type;
          $temp['domains_create_date'] = $each->domains_create_date == null ? '' : date('m-d-Y', strtotime($each->domains_create_date));
          $temp['expiry_date'] = $each->expiry_date == null ? '' : date('m-d-Y', strtotime($each->expiry_date));
          $temp['created_at'] = $each->created_at ? $each->created_at->format('m-d-Y') : '';
          $exportArray[] = $temp;
        }
        return Excel::create('domainleads', function($excel) use ($exportArray) {
          $excel->sheet('mySheet', function($sheet) use ($exportArray){
            $sheet->fromArray($exportArray);
          });
        })->download('csv');
    }

    public function fetchUsageData() {
        try {

            $user = Auth::user();


        } catch(\Exception $e) {

        }
    }
}

