<?php

use Illuminate\Database\Seeder;

class CurlErrorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $curl_errors = custom_curl_errors();
        $data = array();
        foreach($curl_errors as $key=>$val)
        {
			array_push($data,array('curl_error'=>$key,'err_reason'=>$val));
        }
        \App\CurlError::insert($data);
    }
}
