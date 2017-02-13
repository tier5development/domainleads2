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
        //DB::unprepared(file_get_contents($path));
        $passw = 'root' ; //env('DB_PASSWORD');
        $db =    'domainleads2' ;//env('DB_DATABASE');

        exec('mysql -u root -p'.$passw.' '.$db.' < '.$path);
    }
}
