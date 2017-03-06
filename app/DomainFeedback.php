<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainFeedback extends Model
{
    protected $table='domains_feedback';

    public function each_domain()
    {
    	return $this->hasOne('App\EachDomain' , 'domain_name' , 'domain_name');
    }
    public function curl_errors()
    {
    	return $this->hasOne('App\CurlError' , 'curl_error' , 'curl_error');
    }
}
