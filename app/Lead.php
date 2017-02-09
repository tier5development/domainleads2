<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table='leads';

    // public function domainleads()
    // {
    //     return $this->join('each_domain','each_domain.domain_name','=','leads.domain_name');
    // }

    // public function filterby($hash)
    // {
    //     return $this->whereIn('domain_name',$hash);
    // }

    public function each_domain()
    {
    	return $this->hasMany('App\EachDomain' , 'registrant_email' , 'registrant_email');
    }

    public function domains_info()
    {
        return $this->hasManyThrough(
            'App\DomainInfo', 'App\EachDomain',
            'domain_name', 'domain_name', 'registrant_email'
        );
    }

    public function valid_phone()
    {
        return $this->hasOne('App\ValidatedPhone','registrant_email','registrant_email');
    }

    public function users()
    {
        return $this->belongsToMany('App\User','leadusers','registrant_email','registrant_email');
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

 //     public function domains_info()
 //    {
 //    	return $this->hasOne('domains_info' , 'domain_name' , 'domain_name');
 //    } 

	// public function domains_billing()
 //    {
 //    	return $this->hasOne('domains_billing' , 'domain_name' , 'domain_name');
 //    }

	// public function domains_administrative()
 //    {
 //    	return $this->hasOne('domains_administrative' , 'domain_name' , 'domain_name');
 //    }

}
