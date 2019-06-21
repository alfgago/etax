<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //Invoice Queue
        $schedule->command('queue:work '.config('etax.queue_connections') .' --tries=3 --delay=3 --sleep=1 --queue=invoices')
            ->timezone(config('app.timezone'))->everyThirtyMinutes();
        //Emails Queue Restart
        $schedule->command('queue:restart')->timezone(config('app.timezone'))->daily();
        $schedule->command('invoice:resend')->timezone(config('app.timezone'))->hourly();
        $schedule->command('telescope:prune')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
