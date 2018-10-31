<?php

use Illuminate\Database\Seeder;
use App\LeadUser;

class LeadUserSyncExpiredDomainsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "UPDATE `leadusers` 
                INNER JOIN domains_info ON leadusers.domain_name = domains_info.domain_name 
                SET leadusers.expiry_date = domains_info.expiry_date 
                WHERE leadusers.expiry_date is NULL";
        DB::statement($sql);
    }
}
