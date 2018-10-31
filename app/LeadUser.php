<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadUser extends Model
{
    use SoftDeletes;
    protected $table='leadusers';

    public function lead()
    {
    	return $this->hasOne('App\Lead','registrant_email','registrant_email');
    }

    public function valid_phones()
    {
    	return $this->hasMany('App\ValidatedPhone','registrant_email','registrant_email');
    }

    public function domains_info() {
        return $this->hasOne('App\DomainInfo', 'domain_name', 'domain_name');
    }
}
