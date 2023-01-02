<?php

namespace App\Jobs;

use App\DomainAdministrative;
use App\DomainBilling;
use App\DomainFeedback;
use App\DomainInfo;
use App\DomainNameServer;
use App\DomainStatus;
use App\DomainTechnical;
use App\EachDomain;
use App\Helpers\ImportCsvHelper;
use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ChunkDataInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    public $leads_array = [];
    public $domains_array = [];
    public $updated_leads_array = [];
    public $lead_count_updated = false;

    public $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    public $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $leads_registrat_email = Lead::pluck('registrant_email')->toArray();
        $leads_domains_count = Lead::pluck('domains_count')->toArray();
        $this->leads_array = array_combine($leads_registrat_email, $leads_domains_count);

        $domain_name = EachDomain::pluck('domain_name')->toArray();
        $domain_registrant_email = EachDomain::pluck('registrant_email')->toArray();
        $this->domains_array = array_combine($domain_name, $domain_registrant_email);

        $path = storage_path('app/temp/'. $this->file);
        $array = array_map('str_getcsv', file($path));

        // $importCsvHelper = new ImportCsvHelper();

        foreach ($array as $key => $data) {
            Log::info('=======================================================================================================');
            try {
                $this->lead_count_updated = false;
                // validated email
                if(!$this->validateEmail($data[17])) {
                    continue;
                }

                // validate domain name
                $validate_domain = $this->validateDomain($data[1]);
                if ($validate_domain['status'] == false) {
                    continue;
                } else {
                    $domain_name = $validate_domain['name'];
                    $domain_ext = $validate_domain['ext'];
                }

                // Leads
                // check lead aleardy exist in $this->lead array
                if (!array_key_exists($data[17], $this->leads_array)) {
                    $lead = new Lead();
                    // firstname, lastname
                    $name = explode(' ', $data[10]);
                    $lead->registrant_fname = isset($name[0]) ? $name[0] : " ";
                    $lead->registrant_lname = isset($name[1]) ? $name[1] : " ";
                    $lead->registrant_email = $data[17];
                    $lead->registrant_company = $data[11];
                    $lead->registrant_address = $data[12];
                    $lead->registrant_city = $data[13];
                    $lead->registrant_state = $data[14];
                    $lead->registrant_zip = $data[15];
                    $lead->registrant_country = getCountryName($data[16]);
                    $lead->registrant_phone = $data[18];
                    $lead->phone_validated = 'yes';
                    $lead->domains_count = 1;
                    $lead->save();

                    $this->lead_count_updated = true;
                    $this->leads_array[$lead->registrant_email] = $lead->domains_count;
                    Log::info('lead inserted '. $lead->registrant_email);
                } else {
                    Log::error('duplicate ragistrant_email in leads'. $data[17]);
                }

                // EachDomain
                // check domain_name aleardy exist in $this->each_doamin array
                if (!array_key_exists($data[1], $this->domains_array)) {
                    $each_domain = new EachDomain();
                    $each_domain->domain_name = $domain_name;
                    $each_domain->domain_ext = $domain_ext;
                    $each_domain->save();

                    // check registrant_email exist or not
                    $this->increaseDomainCount($data[17]);
                } else {
                    Log::error('duplicate domain name in each_domain'. $data[1]);
                    continue;
                }

                // domain_administrative
                $domain_administrative = new DomainAdministrative();
                $domain_administrative->administrative_name = $data[20];
                $domain_administrative->administrative_company = $data[21];
                $domain_administrative->administrative_address = $data[22];
                $domain_administrative->administrative_city = $data[23];
                $domain_administrative->administrative_state = $data[24];
                $domain_administrative->administrative_zip = $data[25];
                $domain_administrative->administrative_country = $data[26];
                $domain_administrative->administrative_email = $data[27];
                $domain_administrative->administrative_phone = $data[28];
                $domain_administrative->administrative_fax = $data[29];
                $domain_administrative->domain_name = $domain_name;
                $domain_administrative->save();

                // domains_billing
                $domains_billing = new DomainBilling();
                $domains_billing->billing_name = $data[40];
                $domains_billing->billing_company = $data[41];
                $domains_billing->billing_address = $data[42];
                $domains_billing->billing_city = $data[43];
                $domains_billing->billing_state = $data[44];
                $domains_billing->billing_zip = $data[45];
                $domains_billing->billing_country = $data[46];
                $domains_billing->billing_email = $data[47];
                $domains_billing->billing_phone = $data[48];
                $domains_billing->billing_fax = $data[49];
                $domains_billing->domain_name = $domain_name;
                $domains_billing->save();

                // domains_info
                $domains_info = new DomainInfo();
                $domains_info->query_time = $data[2];
                $domains_info->domain_created_date = $data[3];
                $domains_info->domain_updated_date = $data[4];
                $domains_info->expiry_date = $data[5];
                $domains_info->domain_registrar_id = $data[6];
                $domains_info->domain_registrar_name = $data[7];
                $domains_info->domain_registrar_whois = $data[8];
                $domains_info->domain_registrar_url = $data[9];
                $domains_info->domain_name = $domain_name;
                $domains_info->save();

                // domains_nameserver
                $domains_nameserver = new DomainNameServer();
                $domains_nameserver->name_server_1 = $data[50];
                $domains_nameserver->name_server_2 = $data[51];
                $domains_nameserver->name_server_3 = $data[52];
                $domains_nameserver->name_server_4 = $data[53];
                $domains_nameserver->domain_name = $domain_name;
                $domains_nameserver->save();

                // domains_status
                $domains_status = new DomainStatus();
                $domains_status->name_status_1 = $data[54];
                $domains_status->name_status_2 = $data[55];
                $domains_status->name_status_3 = $data[56];
                $domains_status->name_status_4 = $data[57];
                $domains_status->domain_name = $domain_name;
                $domains_status->save();

                // domains_technical
                $domains_technical = new DomainTechnical();
                $domains_technical->technical_name = $data[30];
                $domains_technical->technical_company = $data[31];
                $domains_technical->technical_address = $data[32];
                $domains_technical->technical_city = $data[33];
                $domains_technical->technical_state = $data[34];
                $domains_technical->technical_zip = $data[35];
                $domains_technical->technical_country = $data[36];
                $domains_technical->technical_email = $data[37];
                $domains_technical->technical_phone = $data[38];
                $domains_technical->technical_fax = $data[39];
                $domains_technical->domain_name = $domain_name;
            } catch (\Exception $e) {
                Log::error('In line ' . $e->getLine() . 'error ' . $e);
                die;
            }
            Log::info('=======================================================================================================');
        }

        
        if (count($this->updated_leads_array) > 0) {
            // update increase domains leads
            foreach ($this->updated_leads_array as $ragistrant_email => $domain_count) {
                Lead::where('registrant_email', $ragistrant_email)->update([
                    'domain_count' => $domain_count
                ]);
            }
        }
        unlink($path);
    }

    private function validateEmail($email) {
        // This is standard laravel rule for email validation
        return preg_match('/^.+@.+$/i', $email);
    }

    private function validateDomain($domain_name) {
        $rg_em = str_replace($this->search, $this->replace, $domain_name);
        $rec = str_replace($this->search, $this->replace, $domain_name);
        $d_ext = explode("." , $rec);
        $ext = $d_ext[sizeof($d_ext)-1];

        if(strlen($rg_em) > 110 || strlen($rec)>100 || strlen($ext)>30) {
            $response['status'] = false;
        } else {
            $response['status'] = true;
            $response['name'] = $rec;
            $response['ext'] = $ext;
        }

        return $response;
    }

    private function increaseDomainCount($email) {
        if (!$this->lead_count_updated) {
            if (array_key_exists($email, $this->updated_leads_array)) {
                $this->updated_leads_array[$email]++;
            } else {
                $count = $this->leads_array[$email]++;
                Log::debug('count '. $count);
                Log::debug('email '. $email);
                $this->updated_leads_array[$email] = $count;
            }
        }
    }
}
