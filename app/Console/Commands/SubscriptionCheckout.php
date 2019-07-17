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
            Log::info('Revisa si hay pagos pendientes.' . $now);
            $activeSubscriptions = Sales::whereIn('status', [1, 2])->where('is_subscription', true)->whereDate('next_payment_date', '<=', $now)->get();
            
            foreach ($activeSubscriptions as $activeSubscription) {
                $activeSubscription->status = 2;
                Log::info('Procesando estado de pago: ' . $activeSubscription->id);
                $nextPaymentDate = Carbon::parse($activeSubscription->next_payment_date);
                if ($nextPaymentDate->addDays(3) <= $now) {
                    $activeSubscription->status = 3;
                }

                $activeSubscription->save();
            }
            Log::info('Estados de pago procesados.');
        }catch( \Exception $ex ) {
            Log::error("Error en correr comando" . $ex->getMessage());
        }
    }
}
