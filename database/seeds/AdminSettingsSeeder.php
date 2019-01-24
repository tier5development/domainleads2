<?php

use Illuminate\Database\Seeder;
use App\User;
class AdminSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'work@tier5.us')->first();
        $user->user_type = 5;
        $user->save();
    }
}
