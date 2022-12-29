<?php

namespace App\Jobs;

use App\EachDomain;
use App\Helpers\ImportCsvHelper;
use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ChunkDataInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    public $leads = [];
    public $each_doamins = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->leads = Lead::pluck('registrant_email')->toArray();
        $this->each_doamins = EachDomain::pluck('domain_name')->toArray();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = storage_path('app/temp/'. $this->file);
        $array = array_map('str_getcsv', file($path));

        $importCsvHelper = new ImportCsvHelper();

        foreach ($array as $key => $data) {
            // validated email
            if(!$this->validateEmail($data[17])) {
                continue;
            }

            // check lead aleardy exist in $this->lead array
            if ()

            // check domain_name aleardy exist in $this->each_doamin array
                // insert other table
        }
    }

    private function validateEmail($email) {
        // This is standard laravel rule for email validation
        return preg_match('/^.+@.+$/i', $email);
    }
}
