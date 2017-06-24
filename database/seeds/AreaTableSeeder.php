<?php

use Illuminate\Database\Seeder;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
    	$path = getcwd()."/areas.sql";
        $passw = env('DB_PASSWORD');
        $db =    env('DB_DATABASE');
        $user =  env('DB_USERNAME');
        $host =  env('DB_HOST');
        exec('mysql -h '.$host.' -u '.$user.' -p'.$passw.' '.$db.' < '.$path);
    
    }
}
