<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChkWebsite extends Model
{
    protected $table='chkWebsite';

    public function domain_name()
    {
    	return $this->hasOne('App\EachDomain' , 'domain_name' , 'domain_name');
    }

    

    public function user()
    {
    	return $this->hasOne('App\User');
    }
}
