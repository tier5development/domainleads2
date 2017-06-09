<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\DomainAdministrative;
use App\DomainBilling;
use App\DomainInfo;
use App\DomainNameServer;
use App\DomainStatus;
use App\DomainTechnical;
use App\EachDomain;
use App\Lead;
use App\ValidatedPhone;
use App\CSV;
use Session;

class ImportCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $job_arr = array();
    public $file_name = "";
    public function __construct($data_arr)
    {   
        //dd(Session::all());
        //dd(Session::forget('original_file_name'));
        //dd(Session::has('old_name'));
        $my_file = Session::get('old_name');
        $this->file_name = $my_file;
        //dd($my_file);
        //Session::forget('old_name');
        $this->job_arr = $data_arr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $obj = new \App\Http\Controllers\ImportExport;

        $start = microtime(true);

        $total_leads_before_insertion = Lead::count();

        $total_domains_before_insertion = EachDomain::count();

        // if (Session::has('old_name')) {

        //     $my_file = Session::get('old_name');

        //     Session::forget('old_name');

        // } else {

        //     $my_file = "";
        // }
        \Log::info($this->file_name);

        $csv_exist = CSV::where('file_name',$this->file_name)->first();

        if (!$csv_exist || $csv_exist == NULL || $csv_exist == null) {
            
            
            foreach ($this->job_arr as $x => $value) {
                
                //domains adminstritive
                $d_admin = new DomainAdministrative();

                $d_admin->administrative_name = $value['administrative_name'];
                $d_admin->administrative_company = $value['administrative_company'];

                $d_admin->administrative_address = $value['administrative_address'];

                $d_admin->administrative_city = $value['administrative_city'];

                $d_admin->administrative_state = $value['administrative_state'];

                $d_admin->administrative_zip = $value['administrative_zip'];

                $d_admin->administrative_country = $value['administrative_country'];

                $d_admin->administrative_email = $value['administrative_email'];

                $d_admin->administrative_phone = $value['administrative_phone'];

                $d_admin->administrative_fax = $value['administrative_fax'];
                $d_admin->domain_name = $value['domain_name'];

                $d_admin->save(); 

                //billing

                $d_billing = new DomainBilling();

                $d_billing->billing_name = $value['billing_name'];

                $d_billing->billing_company = $value['billing_company'];

                $d_billing->billing_address = $value['billing_address'];

                $d_billing->billing_city = $value['billing_city'];

                $d_billing->billing_state = $value['billing_state'];

                $d_billing->billing_zip = $value['billing_zip'];

                $d_billing->billing_country = $value['billing_country'];

                $d_billing->billing_email = $value['billing_email'];

                $d_billing->billing_phone = $value['billing_phone'];

                $d_billing->billing_fax = $value['billing_fax'];

                $d_billing->domain_name = $value['domain_name'];

                $d_billing->save();

                //info

                $d_info = new DomainInfo();

                $d_info->query_time = $value['query_time'];

                $d_info->domains_create_date = $value['create_date'];

                $d_info->domains_update_date = $value['update_date'];

                $d_info->expiry_date = $value['expiry_date'];

                $d_info->domain_registrar_id = $value['domain_registrar_id'];
                $d_info->domain_registrar_name = $value['domain_registrar_name'];

                $d_info->domain_registrar_whois = $value['domain_registrar_whois'];

                $d_info->domain_registrar_url = $value['domain_registrar_url'];
                $d_info->domain_name = $value['domain_name'];

                $d_info->save();

                //nameserver

                $d_server = new DomainNameServer();

                $d_server->name_server_1 = $value['name_server_1'];

                $d_server->name_server_2 = $value['name_server_2'];

                $d_server->name_server_3 = $value['name_server_3'];

                $d_server->name_server_4 = $value['name_server_4'];

                $d_server->domain_name = $value['domain_name'];

                $d_server->save();

                //domain status

                $d_status = new DomainStatus();

                $d_status->domain_status_1 = $value
                ['domain_status_1'];

                $d_status->domain_status_2 = $value
                ['domain_status_2'];

                $d_status->domain_status_3 = $value
                ['domain_status_3'];

                $d_status->domain_status_4 = $value
                ['domain_status_4'];

                $d_status->domain_name = $value
                ['domain_name'];

                $d_status->save();

                //technical

                $d_tech = new DomainTechnical();

                $d_tech->technical_name = $value['technical_name'];

                $d_tech->technical_company = $value['technical_company'];

                $d_tech->technical_address = $value['technical_address'];

                $d_tech->technical_city = $value['technical_city'];

                $d_tech->technical_state = $value['technical_state'];

                $d_tech->technical_zip = $value['technical_zip'];

                $d_tech->technical_country = $value['technical_country'];

                $d_tech->technical_email = $value['technical_email'];

                $d_tech->technical_phone = $value['technical_phone'];

                $d_tech->technical_fax = $value['technical_fax'];

                $d_tech->domain_name = $value['domain_name'];

                $d_tech->save();

                //each domain

                $d_each = new EachDomain();

                $d_each->domain_name = $value['domain_name'];

                $d_each->domain_ext = explode(".", $value['domain_name'])[1];

                $d_each->unlocked_num = 0;

                $d_each->registrant_email = $value['registrant_email'];

                $d_each->save();

                $count_d = EachDomain::where('registrant_email', $value['registrant_email'])->count();

                //leads 
                $search_email = Lead::where('registrant_email',$value['registrant_email'])->first();

                $leads = new Lead();

                $leads->registrant_fname = explode(" ", $value['registrant_name'])[0];

                $leads->registrant_lname = array_key_exists(1, explode(" ", $value['registrant_name'])) ? explode(" ", $value['registrant_name'])[1] : "";

                $leads->registrant_email = $value['registrant_email'];

                $leads->registrant_company = $value['registrant_company'];

                $leads->registrant_address = $value['registrant_address'];

                $leads->registrant_city = $value['registrant_city'];

                $leads->registrant_state = $value['registrant_state'];

                $leads->registrant_zip = $value['registrant_zip'];

                $leads->registrant_country = $value['registrant_country'];

                $leads->registrant_phone = $value['registrant_phone'];

                $leads->phone_validated = "yes";

                $leads->unlocked_num = 0;

                $leads->domains_count = $count_d;

                $leads->registrant_fax = $value['registrant_fax'];

                if (!$search_email) {
                    $leads->save();
                }

                //ValidatedPhone

                $v_phone = new ValidatedPhone();
                $num_status = $obj->validateUSPhoneNumber($value['registrant_phone']);
                if ($num_status['http_code'] == 200) {

                    $v_phone->phone_number = $value['registrant_phone'];

                    $v_phone->validation_status = 'valid';

                    $v_phone->state = $num_status['state'];

                    $v_phone->major_city = $num_status['major_city'];

                    $v_phone->primary_city = $num_status['primary_city'];

                    $v_phone->county = $num_status['county'];

                    $v_phone->carrier_name = $num_status['carrier_name'];

                    $v_phone->number_type = $num_status['number_type'];

                    $v_phone->registrant_email = $value['registrant_email'];

                    $v_phone->save();
                }
            } 
            $leads_inserted   = Lead::count()-$total_leads_before_insertion;

            $domains_inserted = EachDomain::count()-$total_domains_before_insertion;

            $end = microtime(true)-$start;

            $csv = new CSV();

            $csv->file_name          = $this->file_name;

            $csv->leads_inserted    = $leads_inserted;

            $csv->domains_inserted  = $domains_inserted;

            $csv->status            = 2;

            $csv->query_time        = $end;

            $csv->save();
        } 
    }
}
