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
use App\Helpers\ImportCsvHelper;
use Log;
class ImportExport extends Controller
{

  public function uploadOldLeads(Request $request) {
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

  public function importExcel(Request $request) {
    $import = new ImportCsvHelper();
    $arr = $import->importExcel($request);
    unset($import);
    return $arr;
  }
}
