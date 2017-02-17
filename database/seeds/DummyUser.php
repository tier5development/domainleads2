<?php

use Illuminate\Database\Seeder;

class DummyUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            
        DB::table('users')->insert([
            'name' => 'tr5',
            'email' => 'a@a.com',
            'password' => bcrypt('123456'),
            'user_type'=> 1,
            'membership_status' => 0,

        ]);
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'work@tier5.us',
            'password' => bcrypt('123456'),
            'user_type'=> 2,
            'membership_status' => 1,
        ]);

    }
}
