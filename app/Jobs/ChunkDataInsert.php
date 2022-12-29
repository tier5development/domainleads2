<?php

namespace App\Jobs;

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

        $importCsvHelper = new ImportCsvHelper();

        foreach ($array as $key => $data) {
            try {
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

                    $this->leads_array[$lead->registrant_email] = $lead->domains_count;
                    Log::info('lead inserted '. $lead->registrant_email);
                } else {
                    Log::error('duplicate ragistrant_email in leads'. $data[17]);
                }

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
                }
            } catch (\Exception $e) {
                Log::error('In line ' . $e->getLine() . 'error ' . $e);
                die;
            }
        }

        if (count($this->updated_leads_array) > 0) {
            // update increase domains leads
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
