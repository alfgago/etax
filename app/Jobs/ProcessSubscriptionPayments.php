<?php

namespace App\Jobs;

use App\Payment;
use App\PaymentMethod;
use App\PaymentProcessor;
use App\Sales;
use App\TransactionsLog;
use App\Utils\PaymentUtils;
use Carbon\Carbon;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProcessSubscriptionPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sale = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sale)
    {
        $this->sale = $sale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (app()->environment('production')) {
            try{
                $date = Carbon::parse(now('America/Costa_Rica'));
    
                $sale = $this->sale;
                
                if ($sale->status == 1) {
                    return true;
                    Log::warning("El usuario $sale->user_id ya tiene la suscripción activa.");
                }
    
                $subscriptionPlan = $sale->plan;
                Log::info("Plan de sale =>" .$subscriptionPlan);

                if ($subscriptionPlan === null) {
                    Log::warning("El sale $sale->id no tiene producto asociado.");
                    return false;
                }
                $planName = $subscriptionPlan->getName();
                Log::info("Procesando cobro $sale->company_id");
                $subtotal = $subscriptionPlan->monthly_price;
                switch ($sale->recurrency){
                    case 1:
                        $subtotal = $subscriptionPlan->monthly_price;
                        break;
                    case 6:
                        $subtotal = $subscriptionPlan->six_price;
                        break;
                    case 12:
                        $subtotal = $subscriptionPlan->annual_price;
                        break;
                }
    
                $subtotal = round($subtotal, 2);
                $iv = $subtotal * 0.13;
                $iv = round($iv, 2);
                $amount = $subtotal + $iv;
    
                $paymentMethod = PaymentMethod::where('user_id', $sale->user_id)
                                 ->where('default_card', true)
                                 ->where('payment_gateway', 'cybersource')
                                 ->first();
                                 
                $company = $sale->company;
    
                if(!$paymentMethod) {
                    $paymentMethod = PaymentMethod::where('user_id', $sale->user_id)->first();
                }
                
                if(!$paymentMethod){
                    Log::warning("El usuario $sale->user_id no tiene un método de pago.");
                    return true;
                }
                
                $paymentGateway = PaymentProcessor::selectPaymentGateway($paymentMethod->payment_gateway);
    
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
                    $data->description = "Renovación plan etax $planName";
                    $data->amount = $amount;
                    $data->user_name = $sale->user->user_name;
                    $data->saleId = $sale->id;
                    $data->paymentMethodId = $paymentMethod->id;
    
                    //Si no hay un charge token, significa que no ha sido aplicado. Entonces va y lo aplica
                    if( ! isset($payment->charge_token) ) {
                        $chargeIncluded = $paymentGateway->createPayment($data);
                        if($chargeIncluded){
                            $chargeTokenId = $paymentGateway->getChargeProof($chargeIncluded);
                            $payment->charge_token = $chargeTokenId;
                            $payment->save();
                        }else{
                            $payment->charge_token = null;
                        }
                    }
    
                    $data->chargeTokenId = $payment->charge_token;
                    $data->cardTokenId = $paymentMethod->token_bn;
                    $data->token_bn = $paymentMethod->token_bn;
                    $data->product_id = $sale->etax_product_id;
                    $transLog = TransactionsLog::create([
                        'id_payment' => $payment->id ?? '',
                        'status' => 'processing',
                        'id_paymethod' => $paymentMethod->id ?? '',
                        'processor' => $paymentMethod->payment_gateway ?? ''
                    ]);
                    $transLog->save();
                    $chargeProof = $paymentGateway->pay($data, false, $transLog);
                    if ($chargeProof) {
                        if(!$payment->charge_token) {
                            $payment->charge_token = $chargeProof;
                        }
                        
                        $payment->proof = $chargeProof;
                        $payment->payment_status = 2;
                        $payment->save();
    
                        $sale->next_payment_date = Carbon::parse(now('America/Costa_Rica'))->addMonths($sale->recurrency);
                        $sale->status = 1;
                        $sale->save();
    
                        $invoiceData = new stdClass();
                        $invoiceData->client_code = $company->id_number;
                        $invoiceData->client_id_number = $company->id_number;
                        $invoiceData->client_id = '-1';
                        $invoiceData->tipo_persona = $company->tipo_persona;
                        $invoiceData->first_name = $company->business_name;
                        $invoiceData->last_name = null;
                        $invoiceData->last_name2 = null;
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
                        $item->name = "Renovación $planName / $sale->recurrency meses";
                        $item->descuento = 0;
                        $item->discount_reason = null;
                        $item->cantidad = 1;
                        $item->iva_amount = $iv;
                        $item->unit_price = $subtotal;
                        $item->subtotal = $subtotal;
                        $item->total = $amount;
    
                        $invoiceData->items = [$item];
                        Log::info("Creando factura de cliente");
                        $factura = $paymentGateway->crearFacturaClienteEtax($invoiceData);
                    }else{
                        Log::warning("Error en cobro ha fallado con data: " . json_encode($data) );
                    }
                }else{
                    Log::warning("Error en cobro de usuario: $sale->user_id / empresa: $sale->company_id, no se encontró tarjeta");
                }
            }catch (\Exception $e) {
                Log::error( "Error al procesar cobro recurrente: " . $e);
            }
        }
    }

}
