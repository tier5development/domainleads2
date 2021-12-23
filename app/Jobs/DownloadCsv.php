<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\UserCsvDownloads;
use Excel;

class DownloadCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $reqData = array();
    public $id;
    public function __construct($reqData, $id)
    {   
        $this->data = $reqData;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $csvData = array();
      foreach ($this->data as $key => $val){
        print_r($val);
        $csvData = $val;
      }
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $name = 'domainleads-'.md5(rand());
        Excel::create($name, function($excel) use ($csvData) {
          $excel->sheet('mySheet', function($sheet) use ($csvData){
            $sheet->fromArray($csvData);
          });
        })->store('xls', public_path('excel/'.$date));
        $file_path = config('settings.APPLICATION-DOMAIN').'/public/excel/'.$date.'/'.$name.'.xls';
        // save the download file in database with path.
        $saveData = new UserCsvDownloads();
        $saveData->user_id = $this->id;
        $saveData->file_name = $name;
        $saveData->file_path = $file_path;
        $saveData->save();
        \Log::info("Data Saved for download");
    } 
}
