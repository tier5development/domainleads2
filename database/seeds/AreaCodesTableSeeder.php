<?php

use Illuminate\Database\Seeder;

class AreaCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = getcwd()."/area_codes.sql";
        $passw = env('DB_PASSWORD');
        $db =    env('DB_DATABASE');
        $user =  env('DB_USERNAME');
        $host =  env('DB_HOST');
        exec('mysql -h '.$host.' -u '.$user.' -p'.$passw.' '.$db.' < '.$path);
    }
}
