<?php

namespace App\Jobs;

use App\CSV;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChunkData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;

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
        try {
            $path = public_path('unzipFiles/'. $this->file);
            $csv_array = array_map('str_getcsv', file($path));
            unset($csv_array[0]);

            // chunking the whole array
            $chunk_array = array_chunk($csv_array, 2000);
            $total_chunk = count($chunk_array);

            // add file name in csv(model)/csv_record(table)
            $csv = new CSV();
            $csv->file_name = $this->file;
            $csv->save();
            Log::info('csv_record inserted : '. $csv->id .''. $csv->file_name .')');

            foreach ($chunk_array as $key=>$array) {
                $name = '('. $key .')-'. $this->file;
                $this->saveArrayInCSV($name, $array);
                Log::debug('Loop/chunk no '. $key);
                ChunkDataInsert::dispatch($name, $key, $total_chunk, null)->onQueue('insert');
            }
        } catch (Exception $e) {
            log::error('In line ' . $e->getLine() . 'error ' . $e);
        }
    }

    /**
     *  convert an array into csv file
     *  save this file to storage folder
     *  @path 'app/temp/'
     */
    public function saveArrayInCSV($filename, $array)
    {
        try {
            $path = storage_path('app/temp/'.$filename);
            Log::info('temp file path : '. $path);
            $fw = fopen($path, 'w');
            foreach ($array as $key => $data) {
                fputcsv($fw, $data);
            }
            fclose($fw);

            return true;
        } catch (\Exception $e) {
            log::error('In saveArrayInCSV method ' . $e->getLine() . 'error ' . $e);
        }
    }
}
