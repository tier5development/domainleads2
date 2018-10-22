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
    public function myUnlockedDominas(Request $request) {
        if(Auth::check() && Auth::user()->user_type == 1) {
            $date = $request->has('date') ? $request->date : null;
            $data['perpage'] = $request->has('perpage') ? $request->perpage : 20;
            $user = Auth::user();
            $data['leads'] = LeadUser::where('user_id', $user->id);
            if($date) {
                // dd($date);
                $data['leads'] = $data['leads']->whereDate('created_at', $date);
            }
            $data['leads'] = $data['leads']->paginate($data['perpage']);
            $data['title'] = 'Unlocked leads | Domainleads';
            return view('home.search.my-unlocked-domains', $data);
        } else {
            return redirect('search');
        }
    }
}
