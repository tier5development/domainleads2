<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
	protected $table='areas';
	
    public function areaCodes()
    {
        return $this->hasMany('App\AreaCode');
    }
}
