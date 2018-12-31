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
        $passw = config('settings.DB.PASS'); // env('DB_PASSWORD');
        $db =   config('settings.DB.DATABASE'); // env('DB_DATABASE');
        $user = config('settings.DB.USER'); // env('DB_USERNAME');
        $host = config('settings.DB.HOST'); // env('DB_HOST');
        exec('mysql -h '.$host.' -u '.$user.' -p'.$passw.' '.$db.' < '.$path);
    }
}
