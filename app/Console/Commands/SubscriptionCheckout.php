<?php

namespace App\Console\Commands;

use App\Payment;
use App\PaymentMethod;
use App\Sales;
use App\Utils\PaymentUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use stdClass;

class SubscriptionCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
       $daily = $this->updateAllSubscriptions();
    }

    public function updateAllSubscriptions(){
        try {
            $now = Carbon::parse(now('America/Costa_Rica'));
            $this->info('Iniciando comando ' . $now);
            $activeSubscriptions = Sales::where('status', 1)->get();
            // $this->info('Subscripciones activas' . $activeSubscriptions->count());
            foreach ($activeSubscriptions as $activeSubscription) {
                $this->info('Comprobando subscripcion ' . $activeSubscription['id']);
                $nextPaymentDate = Carbon::parse($activeSubscription['next_payment_date']);
                if ($nextPaymentDate <= $now) {
                    $activeSubscription->status = 2;
                }
                if ($nextPaymentDate->addDays(3) <= $now) {
                    $activeSubscription->status = 4;
                }

                $activeSubscription->save();
            }
            $this->info('Finalizo comando');
        }catch( \Exception $ex ) {
            Log::error("Error en correr comando" . $ex->getMessage());
        }
    }
}
