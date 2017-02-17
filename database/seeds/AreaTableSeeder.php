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
        exec('mysql -u root -p'.$passw.' '.$db.' < '.$path);
    
    }
}
