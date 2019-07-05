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
        $schedule->command('queue:work '.config('etax.queue_connections') .' --tries=3 --delay=3 --sleep=1 --queue=receptions')
            ->timezone(config('app.timezone'))->everyTenMinutes();
        //Emails Queue Restart
        $schedule->command('queue:restart')->timezone(config('app.timezone'))->daily();
        $schedule->command('invoice:resend')->timezone(config('app.timezone'))->hourly();
        $schedule->command('telescope:prune')->daily();

        $schedule->command('subscription:checkout')->timezone(config('app.timezone'))->dailyAt('01:30');
        $schedule->command('subscription:payment')->timezone(config('app.timezone'))->dailyAt('07:00');
        $schedule->command('subscription:payment')->timezone(config('app.timezone'))->dailyAt('10:00');
        /*$schedule->call(function () {
            $suscriptionsUpdate = PaymentController::updateAllSubscriptions();
        })->dailyAt('14:17');
        
        $schedule->call(function () {
            $makePayment = PaymentController::dailySubscriptionsPayment();
        })->dailyAt('14:18');
        
        $schedule->call(function () {
            $makePayment = PaymentController::dailySubscriptionsPayment();
        })->dailyAt('5:00');*/
        
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
