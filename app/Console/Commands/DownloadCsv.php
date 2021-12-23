<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\UserCsvDownloads;
use App\CsvDownload;
use Excel;

class DownloadCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Download the Learge csv files in background and show it to user download section";

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
        \Log::info("welcome to download csv!");
        $downloadData  = CsvDownload::where('status', 1)->get();
        $csvData = array();
        if(count($downloadData)){
            \Log::info("find download data...");
            foreach($downloadData as $data){
                $csvData = unserialize($data->download_data);
                $user_id = $data->user_id;
                \Log::info("Starting Download!");
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                $name = 'domainleads-'.md5(rand());
                // creating csv file
                Excel::create($name, function($excel) use ($csvData) {
                $excel->sheet('mySheet', function($sheet) use ($csvData){
                    $sheet->fromArray($csvData);
                });
                })->store('xls', public_path('excel/'.$date));
                $file_path = config('settings.APPLICATION-DOMAIN').'/public/excel/'.$date.'/'.$name.'.xls';
                // save the download file in database with path.
                $saveData = new UserCsvDownloads();
                $saveData->user_id = $user_id;
                $saveData->file_name = $name;
                $saveData->file_path = $file_path;
                $saveData->save();
                \Log::info("Data Saved for download");
                // update the csv download table
                CsvDownload::where('id', $data['id'])->update(['status' => 2]);
                \Log::info("Csv download table updated");
            }
        }
    }
}