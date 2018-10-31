<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Lead;
use \App\EachDomain;
use \App\CSV;
use Zipper;

class WhoIsProxyExpiring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whois:expired-domains';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download expired domains in db from whois';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currentDate = date('Y-m-d',time()-3600*24);
        $whoxyURLexpired = "https://www.whoxy.com/expiring-domain-names/download.php?key=59341394fac3331&file=".$currentDate.".zip";
        $startTime = microtime(true);

        try {
        $checkFileExist = CSV::where('file_name',$currentDate.".csv")->first();
        if($checkFileExist == null) {
          $downloadDir = public_path();
          $getDownloadData = file_get_contents($whoxyURLexpired);
          file_put_contents($downloadDir.'/zipFiles/'.$currentDate.'.zip',$getDownloadData);
          Zipper::make($downloadDir.'/zipFiles/'.$currentDate.'.zip')->extractTo($downloadDir.'/unzipFiles/');
          $csvFilePath = $downloadDir.'/unzipFiles/'.$currentDate.'.csv';
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
          $csvObj->file_name          = $currentDate.".csv";
          $csvObj->leads_inserted     = $leads_inserted;
          $csvObj->domains_inserted   = $domains_inserted;
          $csvObj->status             = 2;
          $csvObj->query_time         = $endTime;
          $csvObj->save();
          unlink($csvFilePath);
          unlink($downloadDir.'/zipFiles/'.$currentDate.'.zip');
          unset($import);
          return \Response::json(array('TOTAL TIME TAKEN:'=>$endTime." seconds",
                                      'leads_inserted'=>$leads_inserted,
                                      'domains_inserted'=>$domains_inserted,
                                      'status'=>200,
                                      'message'=>'success',
                                      'filename'=>$currentDate.".csv"));
        } else {
          return \Response::json(array('insertion_time'=>'null',
                                    'message'=>'This file is inserted already::'.$currentDate.".csv",
                                    'status'=>500));
          \Log::info('from :: Error :: '.$exception->getMessage());
        }
      } catch (Exception $exception) {
        \Log::info('from :: Error :: '.$exception->getMessage());
      }
    }
}
