<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
// use \App\Area;
// use \App\AreaCode;

// use \App\Lead;
// use \App\EachDomain;
// use \App\DomainInfo;
// use \App\DomainNameServer;
// use \App\DomainStatus;
// use \App\DomainTechnical;
// use \App\DomainFeedback;
// use \App\DomainBilling;
// use \App\DomainAdministrative;
// use Exception;
// use \App\LeadUser;
// use \App\ValidatedPhone;
// use \App\CSV;
// use DB;
// use Carbon\Carbon;
// use Zipper;
// use Excel;
// use App\Jobs\ImportCsv;
// use Session;

use App\Helpers\ImportCsvHelper;

class WhoisProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whois:proxy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Call Whois Proxies Removed Database to insert CSV record in domain database";

    /**
     * @var null
     */
    private $authToken = null;

    /**
     * @var null
     */
    private $adminId = null;

    /**
     * Create a new command instance.
     */
    
    public $Area_state = array();
    public $Area_major_city = array();
    public $Area_codes_primary_city = array();
    public $Area_codes_county = array();
    public $Area_codes_carrier_name = array();
    public $Area_codes_number_type = array();
    public $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    public $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    public $__leads = array();
    public $__domains = array();

  public $__clipboard = array(); // stores leads which needs to be altered in the table--on basis of domains count

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $currentDate = date('Y-m-d',time()-3600*24);
        $whoxyURL = "https://www.whoxy.com/newly-registered-domains/download.php?key=3b1205bf714563e&file=".$currentDate."_proxies.zip";
        //$whoxyURL = "http://twk.pm/6kj9ulnydc";
        $startTime = microtime(true);

      try {
        $checkFileExist = CSV::where('file_name',$currentDate."_whois-proxies-removed.csv")->first();

        if($checkFileExist == null) {
          $downloadDir = public_path();
          $getDownloadData = file_get_contents($whoxyURL);
          file_put_contents($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip',$getDownloadData);
          Zipper::make($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip')->extractTo($downloadDir.'/unzipFiles/');
          $csvFilePath = $downloadDir.'/unzipFiles/'.$currentDate.'_whois-proxies-removed.csv';
          $getCSVFile  = fopen($csvFilePath , 'r');

          $total_leads_before_insertion = Lead::count();
          $total_domains_before_insertion = EachDomain::count();

          $import = new ImportCsvHelper();
          $return = $import->insertion_Execl($getCSVFile);
          fclose($getCSVFile);

          $leads_inserted   = Lead::count() - $total_leads_before_insertion;
          $domains_inserted = EachDomain::count() - $total_domains_before_insertion;
          $endTime = microtime(true) - $startTime;

          $csvObj = new CSV();
          $csvObj->file_name          = $currentDate."_whois-proxies-removed.csv";
          $csvObj->leads_inserted     = $leads_inserted;
          $csvObj->domains_inserted   = $domains_inserted;
          $csvObj->status             = 2;
          $csvObj->query_time         = $endTime;
          $csvObj->save();
          unlink($csvFilePath);
          unlink($downloadDir.'/zipFiles/'.$currentDate.'_proxies.zip');
          unset($import);
          return \Response::json(array('TOTAL TIME TAKEN:'=>$endTime." seconds",
                                      'leads_inserted'=>$leads_inserted,
                                      'domains_inserted'=>$domains_inserted,
                                      'status'=>200,
                                      'message'=>'success',
                                      'filename'=>$currentDate."_whois-proxies-removed.csv"));
        } else {
          return \Response::json(array('insertion_time'=>'null',
                                    'message'=>'This file is inserted already::'.$currentDate."_whois-proxies-removed.csv",
                                    'status'=>500));
          \Log::info('from :: Error :: '.$exception->getMessage());
        }
      } catch (Exception $exception) {
        \Log::info('from :: Error :: '.$exception->getMessage());
      }
    }
}
