<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call(AreaTableSeeder::class);
        $this->call(AreaCodesTableSeeder::class);
        $this->call(DummyUser::class);
        $this->call(CurlErrorSeeder::class);
        $this->call(LeadUserSyncSeeder::class);
    }
}
