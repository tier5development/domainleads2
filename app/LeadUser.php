<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadUser extends Model
{
    protected $table='leadusers';


    // public function leads()
    // {
    // 	return $this->hasMany('App\Lead','registrant_email','registrant_email');
    // }

    // public function user()
    // {
    // 	return $this->hasMany('App\User' , 'user_id' , 'id');
    // }

}
