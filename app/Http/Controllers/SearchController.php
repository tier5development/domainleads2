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
        // "domain_name" => "kadhf"
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


        $eachDomain;
        $domainInfo;
        $leads;

        foreach($request->all() as $key => $req)
        {           
            if(!is_null($request->$key))
            {
                if($key == 'domain_name')
                 {
                     $eachDomain = EachDomain::where($key, 'like', '%'.$req.'%');
                     //->pluck('domain_name','registrant_email')->get();

                     //dd(count($eachDomain->get()));


                 }
                 else if ($key == 'registered_date') 
                 {
                     if(!isset($domainInfo))
                     {
                         $domainInfo = DomainInfo::where('domains_create_date' , $req);
                     }
                     else
                     {
                         $domainInfo = $domainInfo->where('domains_create_date',$req);
                     }
                 }
                 else if($key == 'registrant_country')
                 {
                     // $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
                     //  $query->where('registrant_country', $req);
                     // });

                     if(!isset($leads))
                     {
                         $leads = Lead::where($key,$req);
                     }
                     else
                     {
                         $leads = $leads->where($key,$req);
                     }
                 }
                 else if($key == 'registrant_state')
                 {
                     // $eachDomain = $eachDomain->whereHas('leads',function($query) use($key , $req){
                     //  $query->where('registrant_state', $req);
                     // });


                     if(!isset($leads))
                     {
                         $leads = Lead::where($key,$req);
                     }
                     else
                     {
                         $leads = $leads->where($key,$req);
                     }
                 }
                 else if ($key == '.com')
                 {
                     if(!isset($eachDomain))
                     {
                         $eachDomain = EachDomain::where('domain_ext','com');
                     }
                     else
                     {
                         $eachDomain = $eachDomain->where('domain_ext','com');
                     }
                 }
                 else if($key == '.org')
                 {

                     if (!isset($eachDomain)) 
                     {
                         $eachDomain = EachDomain::where('domain_ext','org');
                     }
                     else
                     {
                         $eachDomain = $eachDomain->where('domain_ext','org');
                     }
                 }
                 else if($key == '.net')
                 {
                     if (!isset($eachDomain)) 
                     {
                         $eachDomain = EachDomain::where('domain_ext','net');
                     }
                     else
                     {
                         $eachDomain = $eachDomain->where('domain_ext','net');
                     }
                 }
                 else if($key == '.io')
                 {
                     if (!isset($eachDomain)) 
                     {
                         $eachDomain = EachDomain::where('domain_ext','io');
                     }
                     else
                     {
                         $eachDomain = $eachDomain->where('domain_ext','io');
                     }
                 }

            }

             // $requiredData = DB::table('leads')
             //                    ->join('domains', 'leads.id', '=', 'domains.user_id')
             //                    ->join('validatephone', 'validatephone.user_id', '=', 'leads.id')
             //  ->select('leads.*','leads.id as leads_id','domains.id as domain_id','validatephone.*',
             //          'domains.domain_name','domains.create_date','domains.expiry_date','domains.domain_registrar_id','domains.domain_registrar_name','domains.domain_registrar_whois','domains.domain_registrar_url')



              //$users = EachDomain::join('leads','leads.domain_name','=','each_domain.domain_name')


             // $requiredData = Lead::join('each_domain','lead.registrant_email','=','each_domain.registrant_email')
                                //->join('domains_info','each_domain.registrant_email','=',);

             // $requiredData =  DB::table('leads')
             //                    ->join('each_domain', 'leads.registrant_email', '=', 'each_domain.registrant_email')
             //                    //->join('domains_info', 'leads.registrant_email', '=', 'domains_info.domain_name')
             //                    ->select('leads.*', 'each_domain.domain_name'
             //                                    ,'each_domain.domain_ext'
             //                                    //,'domains_info.domains_create_date'
             //                                    )
             //                    ->get();

            // $requiredData =  DB::table('each_domain')
            //                     ->join('leads', 'each_domain.registrant_email', '=', 'leads.registrant_email')->get();
            //                     //->select()

            // $leads = Lead::where()
            // dd(count($requiredData));

            //dd(count($eachDomain->get()));
            dd(count($leads->get()));

            // $leads_reg_country = $leads->pluck('registrant_country','registrant_state','registrant_email')->toArray();
            // dd($leads_reg_country);

            



        }
    }

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
