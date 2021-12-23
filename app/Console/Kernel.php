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
        \App\Console\Commands\WhoIsProxyExpiring::class,
        \App\Console\Commands\DownloadCsv::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('whois:proxy')->dailyAt('7:37')->withoutOverlapping();
        $schedule->command('whois:expired-domains')->dailyAt('8:14')->withoutOverlapping();
        $schedule->command('download:csv')->everyTwoMinutes()->withoutOverlapping();
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
