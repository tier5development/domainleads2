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
class WhoIsProxyExpiring extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'whois:expired-domains {days=0 : The default days duration}';

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
    $days = (int) $this->argument('days');
    if($days == 0) {
      /**
       * Download the latest file
       */
      $currentDate = date('Y-m-d',time()+(3600*24)*29);
      $whoxyURLexpired = "https://www.whoxy.com/expiring-domain-names/download.php?key=59341394fac3331&file=".$currentDate.".zip";
      $importHelper = new ImportCsvHelper();
      $importHelper->importExpiredDomainsZip($currentDate, $whoxyURLexpired);
    } else {
      for($i = 0 ; $i <= $days ; $i++) {
        $currentDate = date('Y-m-d',time()+(3600*24)*$i);
        $whoxyURLexpired = "https://www.whoxy.com/expiring-domain-names/download.php?key=59341394fac3331&file=".$currentDate.".zip";
        $importHelper = new ImportCsvHelper();
        $importHelper->importExpiredDomainsZip($currentDate, $whoxyURLexpired);
      }
    }
  }
}
