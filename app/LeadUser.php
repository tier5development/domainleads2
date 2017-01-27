<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadUser extends Model
{
    protected $table='leadusers';


    public function each_domain()
    {
    	return $this->belongsTo('each_domains' , 'unique_hash' , 'unique_hash');
    }

    public function user()
    {
    	return $this->belongsTo('users' , 'user_id' , 'id');
    }

}
