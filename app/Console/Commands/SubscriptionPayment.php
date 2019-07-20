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
                    
                    Log::info("Procesando cobro $sale->company_id");
                    $subtotal = $sale->product->plan->monthly_price;
                    switch ($sale->recurrency){
                        case 1:
                            $subtotal = $sale->product->plan->monthly_price;
                            break;
                        case 6:
                            $subtotal = $sale->product->plan->six_price;
                            break;
                        case 12:
                            $subtotal = $sale->product->plan->annual_price;
                            break;
                    }

                    $subtotal = round($subtotal, 2);
                    $iv = $subtotal * 0.13;
                    $iv = round($iv, 2);
                    $amount = $subtotal + $iv;

                    $paymentMethod = PaymentMethod::where('user_id', $sale->user_id)->where('default_card', true)->first();
                    $company = $sale->company;
                    
                    if(!$paymentMethod){
                        $paymentMethod = PaymentMethod::where('user_id', $sale->user_id)->first();
                    }
                    
                    if($paymentMethod && $company->email){
                        $payment = Payment::updateOrCreate(
                            [
                                'sale_id' => $sale->id,
                                'payment_status' => 1,
                            ],
                            [
                                'payment_date' => $date,
                                'payment_method_id' => $paymentMethod->id,
                                'amount' => $amount
                            ]
                        );

                        $data = new stdClass();
                        $data->description = 'Pago suscripción etax';
                        $data->amount = $amount;
                        $data->user_name = $sale->user->user_name;
                        
                        //Si no hay un charge token, significa que no ha sido aplicado. Entonces va y lo aplica
                        if( ! isset($payment->charge_token) ) {
                            $chargeIncluded = $paymentUtils->paymentIncludeCharge($data);
                            $chargeTokenId = $chargeIncluded['chargeTokenId'];
                            $payment->charge_token = $chargeTokenId;
                            $payment->save();
                        }
                        
                        $data->chargeTokenId = $payment->charge_token;
                        $data->cardTokenId = $paymentMethod->token_bn;
                        
                        $appliedCharge = $paymentUtils->paymentApplyCharge($data);
                        if ($appliedCharge['apiStatus'] == "Successful") {
                            $payment->proof = $appliedCharge['retrievalRefNo'];
                            $payment->payment_status = 2;
                            $payment->save();

                            $sale->next_payment_date = $date->addMonth($sale->recurrency);
                            $sale->status = 1;
                            $sale->save();

                            $invoiceData = new stdClass();
                            $invoiceData->client_code = $company->id_number;
                            $invoiceData->client_id_number = $company->id_number;
                            $invoiceData->client_id = '-1';
                            $invoiceData->tipo_persona = $company->tipo_persona;
                            $invoiceData->first_name = $company->first_name;
                            $invoiceData->last_name = $company->last_name;
                            $invoiceData->last_name2 = $company->last_name2;
                            $invoiceData->country = $company->country;
                            $invoiceData->state = $company->state;
                            $invoiceData->city = $company->city;
                            $invoiceData->district = $company->district;
                            $invoiceData->neighborhood = $company->neighborhood;
                            $invoiceData->zip = $company->zip;
                            $invoiceData->address = $company->address ?? null;
                            $invoiceData->phone = $company->phone ?? null;
                            $invoiceData->es_exento = false;
                            $invoiceData->email = $company->email;
                            //$invoiceData->expiry = $company->expiry;
                            $invoiceData->amount = $amount;
                            $invoiceData->subtotal = $subtotal;
                            $invoiceData->iva_amount = $iv;
                            $invoiceData->discount_reason = null;

                            $item = new stdClass();
                            $item->total = $amount;
                            $item->code = $sale->etax_product_id;
                            $item->name = $sale->product->name . " / $sale->recurrency meses";
                            $item->descuento = 0;
                            $item->discount_reason = null;
                            $item->cantidad = 1;
                            $item->iva_amount = $iv;
                            $item->unit_price = $subtotal;
                            $item->subtotal = $subtotal;
                            $item->total = $amount;

                            $invoiceData->items = [$item];
                            Log::info("Creando factura de cliente");
                            $factura = $paymentUtils->crearFacturaClienteEtax($invoiceData);
                        }else{
                            Log::warning("Error en cobro: ".$appliedCharge['apiStatus']);
                            /*\Mail::to($company->email)->send(new \App\Mail\SubscriptionPaymentFailure(
                                [
                                    'name' => $company->name . ' ' . $company->last_name,
                                    'product' => $sale->product->plan->plan_type,
                                    'card' => $paymentMethod->masked_card
                                ]
                            ));*/
                        }
                    }else{
                        Log::warning("Error en cobro de usuario: $sale->user_id / empresa: $sale->company_id, no se encontró tarjeta");
                    }
                }
            }else{
                Log::error("Error en API Klap: ".$bnStatus['apiStatus']);
            }
            return true;
        }catch( \Exception $ex ) {
            Log::error("Error " . $ex->getMessage());
        }
    }
}
