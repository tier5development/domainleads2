<?php

namespace App\Console;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \Route;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\WhoisProxy::class,
        \App\Console\Commands\WhoIsProxyExpiring::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        // $schedule->call(function(){
        //     date_default_timezone_set('Asia/Kolkata');
        //     $date = date('Y-m-d',time());
        //     $ch = curl_init();
        //     $url = env('APP_URL');

        //     \Log::info($url.'/update_metadata_today/'.$date);

        //     \Log::info(env('DB_PASSWORD')." process:: ".getmypid());
        //     curl_setopt($ch, CURLOPT_URL, $url.'/update_metadata_today/'.$date);
        //     curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //     $head = curl_exec($ch); 

        //     $result = json_decode($head,true);

        //     //\Log::info('from schedule ++++++>> STATUS : '.$result['status']);
        //     \Log::info($result);

        // })->dailyAt('16:40');
        
        // $schedule->call(function(){
        //     try
        //     {
        //         //2017-02-06 ===>> small date

        //         \Log::info('calling scheduler');
        //         date_default_timezone_set('Asia/Kolkata');
        //         $date = date('Y-m-d',time()-3600*24);

        //         $ch = curl_init();
        //         $url = env('APP_URL');

        //         \Log::info($url.'/importExeclfromCron/'.$date);

        //         \Log::info(env('DB_PASSWORD')." process:: ".getmypid());
        //         curl_setopt($ch, CURLOPT_URL, $url.'/importExeclfromCron/'.$date);
        //         curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //         $head = curl_exec($ch); 

        //         $result = json_decode($head,true);

        //         \Log::info('from schedule ++++++>> STATUS : '.$result['status']);
        //         \Log::info($result);
        //     }
        //     catch(\Exception $e)
        //     {
        //         \Log::info('from catch block'.$e);
        //     }
        //     \Log::info('::time::');
           
        // })->hourly();
       


        // $schedule->call(function(){
        //     try
        //     {
        //        \Log::info('calling scheduler--for domain verification');
        //         date_default_timezone_set('Asia/Kolkata');

        //         $ch = curl_init();
        //         $url = env('APP_URL');

        //         \Log::info($url.'/verify_domains');
        //         curl_setopt($ch, CURLOPT_URL, $url.'/verify_domains');
        //         curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //         $head = curl_exec($ch); 
        //         $result = json_decode($head,true);
        //         \Log::info('from schedule ++++++>> task details: '.json_encode($result));
        //     }
        //     catch(\Exception $e)
        //     {
        //        \Log::info($e->getMessage());
        //     }
        //     \Log::info('::time::');
        // })->dailyAt('18:50');
        //\Log::info(date('Y-m-d',time()));
    

        $schedule->command('whois:proxy')->dailyAt('2:00')->withoutOverlapping();
        $schedule->command('whois:expired-domains')->dailyAt('5:00')->withoutOverlapping();
        
        \Log::info(date('Y-m-d',time())); 
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
