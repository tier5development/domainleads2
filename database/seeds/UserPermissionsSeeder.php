<?php

use Illuminate\Database\Seeder;
use App\User;
class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::where('user_type', '3')->update(['user_type' => '4']);
        User::where('user_type', '2')->update(['user_type' => '3']);
    }
}
