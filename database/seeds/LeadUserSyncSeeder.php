<?php

use Illuminate\Database\Seeder;
use App\LeadUser;

class LeadUserSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadUsers = LeadUser::whereNull('domain_name')->get();
        foreach($leadUsers as $eachLeadUser) {
            $array['domain_name']       = $eachLeadUser->lead->each_domain->first()->domain_name;
            $array['registrant_fname']  = $eachLeadUser->lead->registrant_fname;
            $array['registrant_lname']  = $eachLeadUser->lead->registrant_lname;
            $array['registrant_country']  = $eachLeadUser->lead->registrant_country;
            $array['registrant_company']  = $eachLeadUser->lead->registrant_company;
            $array['registrant_phone']  = $eachLeadUser->lead->registrant_phone;
            $phone = $eachLeadUser->valid_phones->first();
            $array['number_type']        = $phone ? $phone->number_type : null;
            $array['domains_create_date'] = $eachLeadUser->lead->each_domain->first()->domains_info->domains_create_date;
            LeadUser::where('id', $eachLeadUser->id)->update($array);
        }
    }
}
