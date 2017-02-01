<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadUser extends Model
{
    protected $table='leadusers';


    public function each_domain()
    {
    	return $this->belongsTo('each_domains' , 'domain_name' , 'domain_name');
    }

    public function user()
    {
    	return $this->belongsTo('users' , 'user_id' , 'id');
    }

}
