<?php

use Illuminate\Database\Seeder;
class SuspendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $sql = "UPDATE users as u SET u.suspended = '1', u.email = REPLACE(u.email, '_suspended', '') WHERE u.email LIKE '%_suspended';";
        $result = DB::statement(DB::raw($sql));
        dd($result);
    }
}
