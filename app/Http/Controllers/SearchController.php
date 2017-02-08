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
use DB;
use Hash;
use Auth;

class SearchController extends Controller
{
    public function search()
    {
    	$record = array();
    	return view('home.search' , ['record'=>$record]);
    }
    public function postSearch(Request $request)
    {
       
        
       //dd($request->all()); 
        //$eachDomain = EachDomain::with('leads','domains_info');

        $allrecords = Lead::with('each_domain');

        //dd($l->first()->each_domain->first()->domains_info->first()->domain_name);
        //dd($eachDomain->first()->leads->registrant_email);
        //= EachDomain::pluck('domain_name','registrant_email')->toArray();
        //$domainInfoarr      = array();
        $leads_reg_country  ;//= Lead::pluck('registrant_email')->toArray();
        $leads_reg_state    ;//= Lead::pluck('registrant_email')->toArray();
        $domainInfo;
        $leads       = null;

        foreach($request->all() as $key => $req)
        {           
            if(!is_null($request->$key))
            {


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



                // if($key == 'domain_name')
                // {
                //      $eachDomain = $eachDomain->where($key, 'like', '%'.$req.'%');
                // }

                 
                // else if($key == 'registrant_country')
                // {
                //     //$leads_reg_country = $eachDomain->leads()->where($key,$req);

                //     $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
                //        $query->where('registrant_country', $req);
                //       });
                // }
                // else if($key == 'registrant_state')
                // {
                //     //$leads_reg_country = $eachDomain->leads()->where($key,$req);

                //     $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
                //        $query->where('registrant_state', $req);
                //       });
                // }
            }  
        }

        //$allrecords = $allrecords->paginate(50);

        dd($allrecords->first()->each_domain->first()->domains_info->first());

        


        // $reg_email_arr = $eachDomain->pluck('registrant_email')->toArray();

        // $leaduser = LeadUser::where('user_id',\Auth::user()->id)->whereIn('registrant_email',$reg_email_arr);

        // dd(count($leaduser->get()));



        
        

    }




    //dd($eachDomainarr);
        //dd($domainInfoarr);
        //dd($leads_reg_country);
        //dd($leads_reg_state);

        //$eachDomainarr      = array_flip($eachDomainarr);
        // $leads_reg_country  = array_flip($leads_reg_country);
        // //dd($leads_reg_country);
        // //$leads_reg_state    = array_flip($leads_reg_state);
        // $collection         = array_intersect_key($eachDomainarr,$leads_reg_country);
        // //dd($collection);
        // $collection_ = array();

        // foreach($collection as $key=>$each)
        // {
        //     array_push($collection_, $key);
        // }
        // //dd($collection_);
        // $ll = Lead::whereIn('registrant_email',$collection_);
        //dd($ll->first());

        // $x = EachDomain::join('leads','each_domain.registrant_email','=','leads.registrant_email')
        //                 ->join('domains_info','each_domain.domain_name','=','domains_info.domain_name')
        // ;

        // //select('leads.* , each_domain.* ');

        // $y = $x->distinct()->get(['each_domain.registrant_email'
        //                 ,'leads.registrant_country','leads.registrant_state','leads.registrant_company'  
        //                 ,'leads.registrant_email'
        //                 ,'each_domain.domain_name','each_domain.domain_ext','each_domain.unlocked_num'
        //                 ,'domains_info.domains_create_date','domains_info.domains_update_date']);





















   //  public function postSearch(Request $request)
   //  {
   // 	  // "domain_name" => "kadhf"
	  // // "registrant_country" => "adfo;uoiigh"
	  // // "registrant_state" => "adfh"
	  // // "registered_date" => "01/02/2017"
	  // // "_com" => ".com"
	  // // "_net" => ".net"
	  // // "_org" => ".org"
	  // // "_io" => ".io"
	  // // "cell_number" => "cell number"
	  // // "landline_number" => "landline number"
	  // // "_token" => "iqOlMI1d6WBjCbZJmCpNYK20VnWJhy0acmOCbUba"
	  // // "Submit" => "Submit"
    	
    	
   //  $eachDomain;
   //  $leads;
		  	
   //  	foreach($request->all() as $key => $req)
   //  	{    		
   //  		if(!is_null($request->$key))
   //  		{
   //  			if($key == 'domain_name')
   //  			{
   //  				$eachDomain = EachDomain::where($key, 'like', '%'.$req.'%');
   //  			}
   //  			else if ($key == 'registered_date') 
   //  			{
   //  				if(!isset($eachDomain))
   //  				{
   //  					$eachDomain = EachDomain::where($key,$req);
   //  				}
   //  				else
   //  				{
   //  					$eachDomain = $eachDomain->where($key,$req);
   //  				}
   //  			}
   //  			else if($key == 'registrant_country')
   //  			{
   //  				// $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
   //  				// 	$query->where('registrant_country', $req);
   //  				// });

   //  				if(!isset($leads))
   //  				{
   //  					$leads = Lead::where($key,$req);
   //  				}
   //  				else
   //  				{
   //  					$leads = $leads->where($key,$req);
   //  				}
   //  			}
   //  			else if($key == 'registrant_state')
   //  			{
   //  				// $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
   //  				// 	$query->where('registrant_state', $req);
   //  				// });


   //  				if(!isset($leads))
   //  				{
   //  					$leads = Lead::where($key,$req);
   //  				}
   //  				else
   //  				{
   //  					$leads = $leads->where($key,$req);
   //  				}



   //  			}
   //  			else if ($key == '.com')
   //  			{
   //  				if(!isset($eachDomain))
   //  				{
   //  					$eachDomain = EachDomain::where('domain_ext','com');
   //  				}
   //  				else
   //  				{
   //  					$eachDomain = $eachDomain->where('domain_ext','com');
   //  				}
   //  			}
   //  			else if($key == '.org')
   //  			{

   //  				if (!isset($eachDomain)) 
   //  				{
   //  					$eachDomain = EachDomain::where('domain_ext','org');
   //  				}
   //  				else
   //  				{
   //  					$eachDomain = $eachDomain->where('domain_ext','org');
   //  				}
   //  			}
   //  			else if($key == '.net')
   //  			{
   //  				if (!isset($eachDomain)) 
   //  				{
   //  					$eachDomain = EachDomain::where('domain_ext','net');
   //  				}
   //  				else
   //  				{
   //  					$eachDomain = $eachDomain->where('domain_ext','net');
   //  				}
   //  			}
   //  			else if($key == '.io')
   //  			{
   //  				if (!isset($eachDomain)) 
   //  				{
   //  					$eachDomain = EachDomain::where('domain_ext','io');
   //  				}
   //  				else
   //  				{
   //  					$eachDomain = $eachDomain->where('domain_ext','io');
   //  				}
   //  			}
   //  			else
   //  			{

   //  			}
   //  		}
    		
   //  	}
   //  	$hash1 = array();
   //  	$hash2 = array();
   //  	if(isset($eachDomain) && !is_null($eachDomain))
   //  	{
   //  		$hash1 = $eachDomain->pluck('domain_name')->toArray();
   //  	}
    	
   //  	if(isset($leads) && !is_null($leads))
   //  	{
   //  		$hash2 = $leads->pluck('domain_name')->toArray();
   //  	}

   //  	if($hash1 & $hash2)
   //  	{
   //  		$hash = array_intersect($hash1 , $hash2);
   //  	}
   //  	else
   //  	{
    		
   //  		if(sizeof($hash1) == 0) $hash = $hash2;
   //  		else $hash = $hash1;
   //  	}

    	
    	
   //  	// $u = $leads->filterby($hash);
   //  	// dd(count($u->get()));

   //      $h = '';
   //      $length = sizeof($hash);
   //      //dd($hash);
   //      $i=0;
   //      foreach($hash as $key=>$eachhash)
   //      {
   //          if($i != 0 )
   //              $h .= ',';

   //          $h .= "'".$eachhash."'";
   //          $i++;




   //      }
   //      $users = EachDomain::join('leads','leads.domain_name','=','each_domain.domain_name');
    
   //      $record = $users->whereRaw(DB::raw("leads.domain_name in (".$h.")"))->paginate(100);
        
   //  	return view('home.search' , ['record' => $record]);
   //  	//dd(count($eachDomain->get()));
		
    	
    	
    	


   //  }
}
