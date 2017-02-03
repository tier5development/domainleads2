<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaCode extends Model
{
	protected $table='area_codes';

   	public function area()
    {
        return $this->belongsTo('App\Area');
    }
}
