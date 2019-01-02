<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Lead;
use \App\EachDomain;
use \App\CSV;
use Zipper;

use App\Helpers\ImportCsvHelper;

class WhoisProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whois:proxy {days=1: The number of days backward to go}';

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
        $days = (int) $this->argument('days');
        $this->extractAndStore($days);
    }

    private function extractAndStore($dy = 1) {
      $currentDate = date('Y-m-d',time()-(3600*24*$dy));
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
      } catch (\Exception $exception) {
        \Log::info('from :: Error :: '.$exception->getMessage());
      }
    }
}
