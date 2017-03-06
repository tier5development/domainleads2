<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EachDomain extends Model
{
    protected $table='each_domain';

    
    // public function domainleads()
    // {
    //     return $this->join('leads','leads.domain_name','=','each_domain.domain_name');
    // }
    
    // public function filterby($hash)
    // {
    //     return $this->whereIn('domain_name',$hash);
    // }

    public function leads()
    {
    	return $this->belongsTo('App\Lead' , 'registrant_email' , 'registrant_email');
    }

 //    public function domains_technical()
 //    {
 //    	return $this->hasOne('domains_technical' , 'domain_name' , 'domain_name');
 //    }

 //    public function domains_status()
 //    {
 //    	return $this->hasOne('domains_status' , 'domain_name' , 'domain_name');
 //    } 

 //    public function domains_nameserver()
 //    {
 //    	return $this->hasOne('domains_nameserver' , 'domain_name' , 'domain_name');
 //    }

    public function domains_info()
    {
    	return $this->hasOne('App\DomainInfo' , 'domain_name' , 'domain_name');
    }

    public function wordpress_env()
    {
        return $this->hasOne('App\Wordpress_env' , 'domain_name' , 'domain_name');
    }
    public function domains_feedback()
    {
        return $this->hasOne('App\DomainFeedback' , 'domain_name' , 'domain_name');
    }


	// public function domains_billing()
 //    {
 //    	return $this->hasOne('domains_billing' , 'domain_name' , 'domain_name');
 //    }

	// public function domains_administrative()
 //    {
 //    	return $this->hasOne('domains_administrative' , 'domain_name' , 'domain_name');
 //    }
    
}
