<?php

use \App\Area;
use \App\AreaCode;

return[

	'dummy'            => 1,
	'Area_state' 			  => Area::pluck('state','prefix')->toArray(),
	'Area_major_city' 		  => Area::pluck('major_city','prefix')->toArray(),
	'Area_codes_primary_city' => AreaCode::pluck('primary_city','prefix')->toArray(),
	'Area_codes_county' 	  => AreaCode::pluck('county','prefix')->toArray(),
	'Area_codes_carrier_name' => AreaCode::pluck('company','prefix')->toArray(),
	'Area_codes_number_type'  => AreaCode::pluck('usage','prefix')->toArray(),

];