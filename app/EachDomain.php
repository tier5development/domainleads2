<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EachDomain extends Model
{
    protected $table='each_domain';

    public function leads()
    {
    	return $this->hasOne('leads' , 'unique_hash' , 'unique_hash');
    }

    public function domains_technical()
    {
    	return $this->hasOne('domains_technical' , 'unique_hash' , 'unique_hash');
    }

    public function domains_status()
    {
    	return $this->hasOne('domains_status' , 'unique_hash' , 'unique_hash');
    } 

    public function domains_nameserver()
    {
    	return $this->hasOne('domains_nameserver' , 'unique_hash' , 'unique_hash');
    }

     public function domains_info()
    {
    	return $this->hasOne('domains_info' , 'unique_hash' , 'unique_hash');
    } 

	public function domains_billing()
    {
    	return $this->hasOne('domains_billing' , 'unique_hash' , 'unique_hash');
    }

	public function domains_administrative()
    {
    	return $this->hasOne('domains_administrative' , 'unique_hash' , 'unique_hash');
    }
    
}
