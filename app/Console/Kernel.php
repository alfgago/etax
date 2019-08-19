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
        //Invoice Queue
        $schedule->command('queue:work '.config('etax.queue_connections') .' --tries=3 --delay=3 --sleep=1 --queue=invoices')->timezone(config('app.timezone'))->everyThirtyMinutes()->runInBackground();
        $schedule->command('queue:work '.config('etax.queue_connections') .' --tries=3 --delay=3 --sleep=1 --queue=receptions')->timezone(config('app.timezone'))->everyTenMinutes()->runInBackground();
        //Emails Queue Restart
        $schedule->command('invoice:resend')->timezone(config('app.timezone'))->everyThirtyMinutes()->runInBackground();
        $schedule->command('creditnote:resend')->timezone(config('app.timezone'))->hourly()->runInBackground();
        $schedule->command('reception:resend')->timezone(config('app.timezone'))->everyFifteenMinutes()->runInBackground();
        //Comandos de checkout
        $schedule->command('subscription:checkout')->timezone(config('app.timezone'))->dailyAt('01:30')->runInBackground();
        $schedule->command('subscription:payment')->timezone(config('app.timezone'))->twiceDaily(2, 5)->runInBackground(); //Una vez al día. Aveces se acumulan porque por alguna vez no correo y puede haber doble cargo. Hya un sleep de 3s entre cobro
        //$schedule->command('subscription:payment')->timezone(config('app.timezone'))->dailyAt('09:00');
        //Comandos generales
        $schedule->command('telescope:prune')->daily()->runInBackground();
        $schedule->command('queue:restart')->timezone(config('app.timezone'))->daily();
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
