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
use App\Jobs\ProcessSubscriptionPayments;

class SubscriptionPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:payment';

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
        $daily = $this->dailySubscriptionsPayment();
    }

    public function dailySubscriptionsPayment(){
        try{
            Log::info('Iniciando comando de pagos pendientes');
            $paymentUtils = new PaymentUtils();
            $date = Carbon::parse(now('America/Costa_Rica'));
            
            $bnStatus = $paymentUtils->statusBNAPI();
            if($bnStatus['apiStatus'] == 'Successful') {

                $unpaidSubscriptions = Sales::where('status', 2)->where('is_subscription', true)->get();
                foreach($unpaidSubscriptions as $sale){
                    sleep(2);
                    ProcessSubscriptionPayments::dispatch($sale)->onQueue('payments');
                }
                
            }else{
                Log::error("Error conectando a API Klap: ".$bnStatus['apiStatus']);
            }
            return true;
        }catch( \Exception $ex ) {
            Log::error("Error " . $ex->getMessage());
        }
    }
}
