<?php

namespace App\Console\Commands;

use App\CSV;
use App\Jobs\ChunkData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:data {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert csv data to database';

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
        Log::info('Insert command work properly');

        $filename = trim($this->argument('path'));

        // $check_file = CSV::where('file_name', $filename)->count();
        // if ($check_file > 0){
        //     Log::warning("File already uploaded");
        // } else {
            $path = public_path().'/unzipFiles/'.$filename;
            if (file_exists($path)) {
                Log::info('File '. $filename .'dispatch successfully');
                ChunkData::dispatch($filename);
            } else {
                Log::error("File not exist : ". $path);
            }
        // }
    }
}
