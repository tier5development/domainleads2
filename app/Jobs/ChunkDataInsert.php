<?php

namespace App\Jobs;

use App\CSV;
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
use App\SocketMeta;
use App\ValidatedPhone;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChunkDataInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    public $updated_leads_array = [];
    
    private $total_chunk_count;
    private $chunk_number;
    private $csv_id;

    public $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    public $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    public $import_csv_helper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $chunk_number, $total_chunk_count, $csv_id)
    {
        $this->file = $file;
        $this->chunk_number = $chunk_number + 1; // as indexing start from 0, we add 1
        $this->total_chunk_count = $total_chunk_count;
        $this->csv_id = $csv_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time', '0'); // make execution time unlimited

            $start_info = $this->insertInfo();

            $path = storage_path('app/temp/'. $this->file);
            $array = array_map('str_getcsv', file($path));

            // $importCsvHelper = new ImportCsvHelper();

            foreach ($array as $key => $data) {
                Log::info('=======================================================================================================');
                try {
                    // validated email
                    $email = strtolower($data['17']);
                    if(!$this->validateEmail($email)) {
                        Log::info("invalide ragistrant_email type : ". $email);
                        continue;
                    }

                    // validate domain name
                    $validate_domain = $this->validateDomain($data[1]);
                    if ($validate_domain['status'] == false) {
                        Log::info("invalide domain_name type : ". $data[1]);
                        continue;
                    } else {
                        $domain_name = $validate_domain['name'];
                        $domain_ext = $validate_domain['ext'];
                    }

                    /**
                     *  After validated both registrant_email and domain
                     *  validated phone number
                     *  insert it to ValidatedPhone
                     */
                    Log::info('***************************************************************************');
                    $validate_number = $this->validateNumber($data[18]);
                    if ($validate_number['status'] == true && isset($validate_number['data'])) {
                        // insert number in ValidatedPhone
                        $valid_number = new ValidatedPhone();
                        $valid_number->phone_number = $validate_number['data']['phone_number'];
                        $valid_number->validation_status = $validate_number['data']['validation_status'];
                        $valid_number->state = $validate_number['data']['state'];
                        $valid_number->major_city = $validate_number['data']['major_city'];
                        $valid_number->primary_city = $validate_number['data']['primary_city'];
                        $valid_number->county = $validate_number['data']['county'];
                        $valid_number->carrier_name = $validate_number['data']['carrier_name'];
                        $valid_number->number_type = $validate_number['data']['number_type'];
                        $valid_number->registrant_email = $email;
                        $valid_number->save();

                        Log::info('valid_number inserted '. $valid_number->phone_number);
                    } else {
                        if (isset($validate_number['isExist']) && $validate_number['isExist'] && $validate_number['existEmail'] == $email) {
                            // allow to insert data
                            // continue;
                        } else {
                            Log::info('invalide valid_number type : '. $data[18]);
                            Log::info('|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-| email : '. $email);
                            Log::debug($validate_number['message'] .' : '. $validate_number['existEmailCount']);
                            continue;
                        }
                    }
                    Log::info('***************************************************************************');

                    // EachDomain
                    // check domain_name aleardy exist in each_doamin table
                    $check_domain = $this->checkDomain($domain_name);

                    if ($check_domain['status'] == false) {
                        $each_domain = new EachDomain();
                        $each_domain->domain_name = $domain_name;
                        $each_domain->domain_ext = $domain_ext;
                        $each_domain->registrant_email = $email;
                        $each_domain->save();
                        Log::info('each_domain inserted '. $each_domain->registrant_email);
                    } else {
                        Log::error('duplicate domain name in each_domain'. $data[1]);
                        continue;
                    }

                    // check registrant_email exist or not in leads
                    $result = $this->checkLeads($email);

                    // Leads
                    // check lead aleardy exist in $this->lead array
                    if ($result['status'] == false) {
                        $lead = new Lead();
                        // firstname, lastname
                        $name = explode(' ', $data[10]);
                        $lead->registrant_fname = isset($name[0]) ? $name[0] : " ";
                        $lead->registrant_lname = isset($name[1]) ? $name[1] : " ";
                        $lead->registrant_email = $email;
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
                        Log::info('lead inserted '. $lead->id .'('. $lead->registrant_email .')');
                    } else {
                        // increase domains_count in leads
                        $this->increaseDomainCount($result['data']);
                        Log::error('duplicate ragistrant_email in leads'. $email);
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
                    Log::info('domain_administrative inserted : '. $domain_administrative->id);

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
                    Log::info('domains_billing inserted : '. $domains_billing->id);

                    // domains_info
                    $domains_info = new DomainInfo();
                    $domains_info->query_time = $data[2];
                    $domains_info->domains_create_date = $data[3];
                    $domains_info->domains_update_date = $data[4];
                    $domains_info->expiry_date = $data[5];
                    $domains_info->domain_registrar_id = $data[6];
                    $domains_info->domain_registrar_name = $data[7];
                    $domains_info->domain_registrar_whois = $data[8];
                    $domains_info->domain_registrar_url = $data[9];
                    $domains_info->domain_name = $domain_name;
                    $domains_info->save();
                    Log::info('domains_info inserted : '. $domains_info->id);

                    // domains_nameserver
                    $domains_nameserver = new DomainNameServer();
                    $domains_nameserver->name_server_1 = $data[50];
                    $domains_nameserver->name_server_2 = $data[51];
                    $domains_nameserver->name_server_3 = $data[52];
                    $domains_nameserver->name_server_4 = $data[53];
                    $domains_nameserver->domain_name = $domain_name;
                    $domains_nameserver->save();
                    Log::info('domains_nameserver inserted : '. $domains_nameserver->id);

                    // domains_status
                    $domains_status = new DomainStatus();
                    $domains_status->domain_status_1 = $data[54];
                    $domains_status->domain_status_2 = $data[55];
                    $domains_status->domain_status_3 = $data[56];
                    $domains_status->domain_status_4 = $data[57];
                    $domains_status->domain_name = $domain_name;
                    $domains_status->save();
                    Log::info('domains_status inserted : '. $domains_status->id);

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
                    $domains_technical->save();
                    Log::info('domains_technical inserted : '. $domains_technical->id);
                } catch (\Exception $e) {
                    Log::error('In line ' . $e->getLine() . 'error ' . $e);
                    Log::debug('=======================================================================================================');
                    die;
                }
                Log::info('=======================================================================================================');
            }

            
            Log::info('updated_leads/updated_leads_array updating start');
            if (count($this->updated_leads_array) > 0) {
                // update increase domains leads
                foreach ($this->updated_leads_array as $ragistrant_email => $domain_count) {
                    Log::debug($ragistrant_email .'=>'. $domain_count);
                    Lead::where('registrant_email', $ragistrant_email)->update([
                        'domains_count' => $domain_count
                    ]);
                }
            }
            Log::info('updated_leads/updated_leads_array updating end');

            $end_info = $this->insertInfo();

            $domain_inserted = $end_info['domain_count'] - $start_info['domain_count'];
            $leads_inserted = $end_info['domain_count'] - $start_info['domain_count'];
            $time = $end_info['time'] - $start_info['time']; //time taken to complete this process
            Log::debug('domain_inserted : '. $domain_inserted);
            Log::debug('leads_inserted : '. $leads_inserted);
            Log::debug('time : '. $time);

            // insert calculated data to csv
            $csv = CSV::find($this->csv_id);
            $csv->leads_inserted = $csv->leads_inserted + $leads_inserted;
            $csv->domains_inserted = $csv->domains_inserted + $domain_inserted;
            $csv->query_time = $csv->query_time + $time;
            if ($this->chunk_number == $this->total_chunk_count) {
                $csv->status = 2;
            }
            $csv->save();
            Log::info('csv_record inserted : '. $csv->id);

            // insert data in SocketMeta
            $socket_meta = SocketMeta::first();
            $socket_meta->total_domains = $socket_meta->total_domains + $domain_inserted;
            if ($this->chunk_number == 1) {
                $socket_meta->leads_added_last_day = $domain_inserted;
            } else {
                $socket_meta->leads_added_last_day = $socket_meta->leads_added_last_day + $domain_inserted;
            }
            $socket_meta->save();
            Log::info('socket_meta inserted : '. $socket_meta->id);

            unlink($path);
        } catch (\Exception $e) {
            Log::error('Error in ChunkDataInsert '. $e);
        }
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

    /**
     *  check domain_name in EachDomain model
     */
    private function checkDomain($domain_name)
    {
        $data = EachDomain::where('domain_name', $domain_name)->get();

        if (count($data) > 0) {
            $response['status'] = true;
            $response['data'] = $data[0];
        } else {
            $response['status'] = false;
            $response['data'] = null;
        }

        return $response;
    }

    /**
     *  check domain_name in EachDomain model
     */
    private function checkLeads($email)
    {
        $data = Lead::where('registrant_email', $email)->get();

        if (count($data) > 0) {
            $response['status'] = true;
            $response['data'] = $data[0];
        } else {
            $response['status'] = false;
            $response['data'] = null;
        }

        return $response;
    }

    private function increaseDomainCount(Lead $lead) {
        if (array_key_exists($lead->registrant_email, $this->updated_leads_array)) {
            $this->updated_leads_array[$lead->registrant_email]++;
        } else {
            $this->updated_leads_array[$lead->registrant_email] = $lead->domains_count + 1;
        }
    }

    private function insertInfo()
    {
        $domain_count = EachDomain::count();
        $leads_count = Lead::count();
        $time = microtime(true);

        return [
            'domain_count' => $domain_count,
            'leads_count' => $leads_count,
            'time' => $time,
        ];
    }

    private function validateNumber($number)
    {
        $response['status'] = false;
        $response['message'] = '';
        $response['data'] = null;

        try {
            Log::debug('number : '. $number);
            $import_csv_helper = new ImportCsvHelper();

            if($number == '') {
                return $response;
            }
            
            $number = preg_replace('~\D~', '', $number);

            // check number is numeric or not
            if (!is_numeric($number)) {
                $response['message'] = 'Non numeric number';
                Log::debug('number validation : '. $response['message']);
                return $response;
            }

            // Check number already exist or not
            $numberExist = ValidatedPhone::where('phone_number', $number)->count();
            $numberCount = count($numberExist);
            if ($numberCount > 0) {
                $response['message'] = 'Number already exist';
                $response['isExist'] = true;
                $response['existEmail'] = $numberExist->registrant_email;
                $response['existEmailCount'] = $numberCount;
                Log::debug('number validation : '. $response['message']);
                return $response;
            }

            // validate number
            $arr = ($import_csv_helper->validateNumber($number));

            if($arr['status'] == true) {
                $response['status'] = true;
                $response['message'] = 'success';
                $response['data'] = $arr;
            } else {
                $response['message'] = 'not success';
            }
            Log::debug('number validation from $import_csv_helper->validateNumber() : '. $arr['message']);

            return $response;
        } catch (Exception $e) {
            Log::debug('error in number validation');
            Log::error($e);

            $response['message'] = 'failed';

            return $response;
        }
    }

    /**
     *  check registrant_email already exisit or not
     *  in valid_phone table
     */
    private function checkEmailInValidatedPhone($email)
    {
        $emailCount = ValidatedPhone::where('registrant_email', $email)->count();
        if ($emailCount > 0) {
            $response['status'] = true;
            $response['count'] = $emailCount;
            $response['message'] = 'registrant_email exist';
        } else {
            $response['status'] = false;
            $response['count'] = 0;
            $response['message'] = 'registrant_email not exist';
        }

        return $response;
    }

    private function removeInvalidLeadsDomain($registrant_email)
    {
        try {
            $invalidLeads = Lead::where('registrant_email', $registrant_email)->count();

            if ($invalidLeads > 0) {
                Lead::where('registrant_email', $registrant_email)->delete();
                Log::info('remove invalid Leads');

                $invalidDomain = EachDomain::where('registrant_email', $registrant_email)->count();
                if ($invalidDomain > 0) {
                    $each_domains = EachDomain::select('domain_name')->where('registrant_email', $registrant_email)->pluck('domain_name')->toArray();
                    // foreach ($each_domains as $each_domain) {
                        EachDomain::where('registrant_email', $registrant_email)->delete();
                        Log::info('remove invalid EachDomain');

                        DomainAdministrative::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainAdministrative');

                        DomainBilling::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainBilling');

                        DomainInfo::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainInfo');

                        DomainNameServer::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainNameServer');

                        DomainStatus::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainStatus');

                        DomainTechnical::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainTechnical');

                        DomainStatus::whereIn('domain_name', $each_domains)->delete();
                        Log::info('remove invalid DomainStatus');
                    // }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in removeInvalidLeadsDomain : '. $e);
        }
    }
}
