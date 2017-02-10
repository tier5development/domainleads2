<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValidatedPhone extends Model
{
    protected $table='valid_phone';


    public function valid_phone()
    {
        return $this->belongsTo('App\Lead','registrant_email','registrant_email');

        
    }
}
