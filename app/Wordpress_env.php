<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wordpress_env extends Model
{
    protected $table='wordpress';

    public function domain_name()
    {
    	return $this->hasOne('App\Wordpress_env' , 'domain_name' , 'domain_name');
    }

    

    public function user()
    {
    	return $this->hasOne('App\User');
    }
}
