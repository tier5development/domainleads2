<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
use \App\CSV;
use \App\Lead;
use \App\EachDomain;
use App\SocketMeta;
use App\Events\UsageInfo;

use App\Helpers\ImportCsvHelper;
use Log, PharData;
class ImportExport extends Controller
{

  public function uploadOldLeads(Request $request) {
    // sleep(5);
    // Log::info('ajkd');
    // return response()->json(['status' => 200]);
    $arr = $this->importExcel($request);
    return response()->json(['status' => 200]);
  }

  public function importExport() {
    try {
      if(\Auth::check()) return view('importExport');  
      return redirect('/home');
    } catch(\Exception $e) {
      return redirect('/home')->with('error', $e->getMessage());
    }
  }

  public function importBulkZip(Request $request) {
    // dd($request->all());
    // $file = $request->file('import_file');
    // dd($file->geT)
    // $phar = new PharData('myphar.tar');
    // dd($file);
    $upload = $request->file('import_file');
    if($upload == null) {
      return redirect()->back()->with('error', 'Please select a tar.gz file');
    }
    $filepath = $upload->getRealPath();
    $original_file_name = $request->import_file->getClientOriginalName();
    $storeLocation = (getcwd().'/public/unzipFiles/');
    move_uploaded_file($filepath, $storeLocation.$original_file_name);
    $cmd = 'cd '.getcwd().'/public/unzipFiles/ && tar -xf '.$original_file_name.' && rm -r '.$original_file_name.' -y';
    exec($cmd, $output, $result);

    $files = glob(getcwd()."/public/unzipFiles/*.csv");
    foreach($files as $key => $file) {
      try {
        if (($handle = fopen($file, "r")) !== FALSE) {
            print_r($this->importExcelFromFile($handle, array_reverse(explode('/', $file))[0]));
        } else {
            echo "Could not open file: " . $file;
        }
      } catch(\Exception $e) {
        print_r($e->getMessage());
        echo '<br><br>';
      }
    }
  }

  public function importExcelFromFile($file, $name)
  {
    try {
      $start = microtime(true);
      $csv_exists = CSV::where('file_name', $name);
      if($csv_exists->first() == null) {
        $total_leads_before_insertion = Lead::count();
        $total_domains_before_insertion = EachDomain::count();

        echo('***** Insertion started ********');
        $import = new ImportCsvHelper();
        $arr = $import->insertion_Execl($file);
        fclose($file);
        echo('***** Insertion ended *******');
        $leads_inserted   = Lead::count()-$total_leads_before_insertion;
        $domains_inserted = EachDomain::count()-$total_domains_before_insertion;
        $end = microtime(true) - $start;

        $obj = new CSV();
        $obj->file_name         = $name;
        $obj->leads_inserted    = $leads_inserted;
        $obj->domains_inserted  = $domains_inserted;
        $obj->status            = 2;
        $obj->query_time        = $end;
        $obj->save();
        $socketMeta = SocketMeta::first();
        $socketMeta->leads_added_last_day = $leads_inserted;
        $socketMeta->save();
        event(new UsageInfo());

        return \Response::json(array('TOTAL TIME TAKEN:'=>$end." seconds",
                                  'leads_inserted'=>$leads_inserted,
                                  'domains_inserted'=>$domains_inserted,
                                  'status'=>200,
                                  'filename'=>$original_file_name));

      } else {
          return \Response::json(array('insertion_time'=>'null',
                                         'message'=>'This file is inserted already::'.$original_file_name,
                                         'status'=>500));
      }
    } catch(\Exception $e) {
      return \Response::json(array('insertion_time' =>  'n/a',
                                         'message'  =>  ' ERROR : '.$e->getMessage().' LINE : '.$e->getLine(),
                                         'status'   =>  500));
    }
  }

  public function importExcel(Request $request) {
    $import = new ImportCsvHelper();
    $arr = $import->importExcel($request);
    unset($import);
    return $arr;
  }
}
