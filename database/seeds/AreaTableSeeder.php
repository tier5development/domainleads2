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
        //DB::unprepared(file_get_contents($path));
        $passw = 'toor';//env('DB_PASSWORD');
        $db =    'domainleads'; //env('DB_DATABASE');
        exec('mysql -u root -p'.$passw.' '.$db.' < '.$path);
    
    }
}
