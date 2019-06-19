<?php

namespace App\Http\Controllers;

use App\Company;
use App\EtaxProducts;
use App\Coupon;
use App\Invoice;
use App\Payment;
use App\Sales;
use App\Subscription;
use App\PaymentMethod;
use App\SubscriptionPlan;
use Carbon\Carbon;
use CybsSoapClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Utils\BridgeHaciendaApi;
use App\Utils\PaymentUtils;


//require __DIR__ . '/../../../vendor/autoload.php';

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = auth()->user()->payments;
        return view('payment/payment-history', compact('data'))->with('payments', $payments);
        /*$user = auth()->user();
        $cantidad = PaymentMethod::where('user_id', $user->id)->get()->count();
        return view('payment/index')->with('cantidad', $cantidad);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData(){
        $payments = auth()->user()->payments;

        return datatables()->of( $payments )
            ->addColumn('sale', function(Payment $payment) {
                return $payment->sale->product->name;
            })
            ->toJson();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentCrear(){
        $sale = getCurrentSubscription();
        return view('payment/create')->with('sale', $sale);
    }

    private function getDocReference($docType) {
        $lastSale = Company::find(1)->last_invoice_ref_number + 1;
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType) {
        $company = Company::find(1);
        $invoice = new Invoice();
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef($company->last_invoice_ref_number + 1);
            
            
        return $key;
    }

    public function paymentCheckout(){
        $sale = getCurrentSubscription();
        return view('payment/paymentCard')->with('sale', $sale);
    }

    public function storeClient(Request $request){
        $cliente = new \App\Client();
        $cliente->company_id = 1;
        $cliente->tipo_persona = $request->tipo_persona;
        $cliente->id_number = $request->id_number;
        $cliente->code = $request->code;
        $cliente->first_name = $request->first_name;
        $cliente->last_name = $request->last_name;
        $cliente->last_name2 = $request->last_name2;
        $cliente->emisor_receptor = $request->emisor_receptor;
        $cliente->country = $request->country;
        $cliente->state = $request->state;
        $cliente->city = $request->city;
        $cliente->district = $request->district;
        $cliente->neighborhood = $request->neighborhood;
        $cliente->zip = $request->zip;
        $cliente->address = $request->address;
        $cliente->phone = $request->phone;
        $cliente->es_exento = $request->es_exento;
        $cliente->email = $request->email;
        $cliente->fullname = $cliente->toString();
        $cliente->billing_emails = $request->email;

        $cliente->save();

        return $cliente;
    }

    public function confirmPayment(Request $request){
        $user = auth()->user();
        $paymentUtils = new PaymentUtils();
        
        $request->number = preg_replace('/\s+/', '',  $request->number);
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        
        //El descuento por defecto es cero.
        $descuento = 0;
        //Aplica descuento del Banco Nacional
        if( $request->bncupon ) {
            $descuento = 0.1;
        }
        
        //Si tiene un cupon adicional, este aplica sobre el de la tarjeta del BN.
        if ( isset($request->coupon) ) {
            $cuponConsultado = Coupon::where('code', $request->coupon)->first();
            if ( isset($cuponConsultado) ) {
                if( $cuponConsultado->code == '$$$ETAX100DESCUENTO!' || $cuponConsultado->code == '$$$ETAXTRANSFERENCIA!' ){
                    return $this->skipPaymentCoupon( $request, $cuponConsultado );
                }
                $descuento = ($cuponConsultado->discount_percentage) / 100;
            } else {
                $descuento = 0;
            }
        }
        
        //Crea el sale de suscripción
        $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );

        //Revisa recurrencia para definir el costo.
        $recurrency = $request->recurrency;
        $subscriptionPlan = $sale->product->plan;
        switch ($recurrency) {
            case 1:
                $costo = $subscriptionPlan->monthly_price;
                $nextPaymentDate = $start_date->addMonths(1);
                $descriptionMessage = 'Mensual';
                break;
            case 6:
                $costo = $subscriptionPlan->six_price * 6;
                $nextPaymentDate = $start_date->addMonths(6);
                $descriptionMessage = 'Semestral';
                break;
            case 12:
                $costo = $subscriptionPlan->annual_price * 12;
                $nextPaymentDate = $start_date->addMonths(12);
                $descriptionMessage = 'Anual';
                break;
        }
        
        //Calcula el monto con descuentos aplicados.
        $montoDescontado = $costo * $descuento;
        $subtotal = ($costo - $montoDescontado);
        $iv = $subtotal * 0;
        $amount = $subtotal + $iv;
        $cards = array(
            $request->number
        );
        $cardYear = substr($request->expiry, -2);
        $cardMonth = substr($request->expiry, 0 , 2);
        
        //Cupon para pruebas, hace pagos por $1.
        if( $request->coupon == "!!CUPON1!!" ) {
            $amount = 1;
        }
        
        foreach ($cards as $c) {
            $check = $paymentUtils->checkCC($c, true);
            //if ($check !== false) {
                $typeCard = $check;
            //}
        }
        $last_4digits = substr($request->number, -4);
        $nameCard = $typeCard ? $typeCard : 'Visa';
        $cardDescripcion = "Tarjeta $last_4digits de usuario: " . auth()->user()->user_name;
        
        //Revisa si el API del BN esta arriba.
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            //Agrega la tarjeta al API del BN.
            $card = $paymentUtils->userCardInclusion($request->number, $cardDescripcion, $cardMonth, $cardYear, $request->cvc);
            if($card['apiStatus'] == 'Successful'){
                $last_4digits = substr($request->number, -4);
                //Se logró agregar la tarjeta, entonces hago un nuevo payment method.
                $paymentMethod = PaymentMethod::updateOrCreate([
                    'user_id' => $user->id,
                    'name' => $request->first_name_card,
                    'last_name' => $request->last_name_card,
                    'last_4digits' => $last_4digits,
                    'masked_card' => $card['maskedCard'],
                    'due_date' => $cardMonth . '/' .$cardYear,
                    'token_bn' => $card['cardTokenId'],
                    'default_card' => 0
                ]);
            } else {
                $paymentMethod = PaymentMethod::where('user_id', $user->id)
                                ->where('last_4digits', $last_4digits)
                                ->first();
                if( ! isset($paymentMethod) ) {
                    $mensaje = "El método de pago no pudo ser validado.";
                    return redirect()->back()->withError($mensaje)->withInput();
                }
            }
            
            $payment = Payment::updateOrCreate(
                [
                    'sale_id' => $sale->id,
                    'payment_status' => 1,
                ],
                [
                    'payment_method_id' => $paymentMethod->id,
                    'payment_date' => $start_date,
                    'amount' => $amount
                ]
            );
            
            $data = new stdClass();
            $data->description = 'Pago suscripción etax';
            $data->amount = $amount;
            $data->user_name = $user->user_name;
            
            //Si no hay un chage token, significa que no ha sido aplicado. Entonces va y lo aplica
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
                
                $sale->status = 1;
                $sale->next_payment_date = $nextPaymentDate;
                $sale->save();

                $invoiceData = new stdClass();
                $invoiceData->client_code = $request->id_number;
                $invoiceData->client_id_number = $request->id_number;
                $invoiceData->client_id = '-1';
                $invoiceData->tipo_persona = $request->tipo_persona;
                $invoiceData->first_name = $request->first_name;
                $invoiceData->last_name = $request->last_name;
                $invoiceData->last_name2 = $request->last_name2;
                $invoiceData->country = $request->country;
                $invoiceData->state = $request->state;
                $invoiceData->city = $request->city;
                $invoiceData->district = $request->district;
                $invoiceData->neighborhood = $request->neighborhood;
                $invoiceData->zip = $request->zip;
                $invoiceData->address = $request->address;
                $invoiceData->phone = $request->phone;
                $invoiceData->es_exento = $request->es_exento;
                $invoiceData->email = $request->email;
                $invoiceData->expiry = $request->expiry;
                $invoiceData->amount = $amount;
                $invoiceData->subtotal = $amount;

                $item = new stdClass();
                $item->total = $amount;
                $item->code = $sale->etax_product_id;
                $item->name = $sale->product->name;
                $item->descuento = $descuento;
                $item->cantidad = 1;
                
                $invoiceData->items = [$item];
                $factura = $this->crearFacturaClienteEtax($invoiceData);
                if($factura){
                    return redirect('/wizard')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
                }
            } else {
                $mensaje = 'El pago ha sido denegado';
                return redirect()->back()->withError($mensaje)->withInput();
            }
            
        }else{
            $mensaje = 'Pagos en Linea esta fuera de servicio. Dirijase a Configuraciones->Gestion de Pagos- para agregar una tarjeta';
            return redirect('wizard')->withError($mensaje)->withInput();
        }
    }

  
    public function crearFacturaClienteEtax($invoiceData){
        $apiHacienda = new BridgeHaciendaApi();
        $tokenApi = $apiHacienda->login();
        if ($tokenApi !== false) {
            $invoice = new Invoice();
            $company = Company::find(1);
            $invoice->company_id = 1;
            $document_key = $this->getDocumentKey('01');
            $document_number = $this->getDocReference('01');

            //Datos generales y para Hacienda
            $invoice->document_type = "01";
            $invoice->hacienda_status = "01";
            $invoice->payment_status = "01";
            $invoice->payment_receipt = "";
            $invoice->generation_method = "etax";
            $invoice->reference_number = $company->last_invoice_ref_number + 1;

            $data = new stdClass();
            $data->document_key = $document_key;
            $data->document_number = $document_number;
            $data->sale_condition = '01';
            $data->payment_type = "02";
            $data->retention_percent = "6";
            $data->credit_time = "0";

            $data->tipo_persona = "02";
            $data->identificacion_cliente = $invoiceData->client_id_number;
            $data->codigo_cliente = $invoiceData->client_code;
            $data->code = $invoiceData->client_code;
            $data->id_number = $invoiceData->client_id_number;

            $data->client_code = $invoiceData->client_id_number;
            $data->client_id_number = $invoiceData->client_id_number;
            $data->client_id = '-1';
            $data->tipo_persona = $invoiceData->tipo_persona;
            $data->first_name = $invoiceData->first_name;
            $data->last_name = $invoiceData->last_name;
            $data->last_name2 = $invoiceData->last_name2;
            $data->country = $invoiceData->country;
            $data->state = $invoiceData->state;
            $data->city = $invoiceData->city;
            $data->district = $invoiceData->district;
            $data->neighborhood = $invoiceData->neighborhood;
            $data->zip = $invoiceData->zip;
            $data->address = $invoiceData->address;
            $data->phone = $invoiceData->phone;
            $data->es_exento = $invoiceData->es_exento;
            $data->email = $invoiceData->email;
            $data->buy_order = '';
            $data->due_date =
            $data->other_reference = '';
            $data->currency_rate = get_rates();
            $data->description = 'Factura de Etax';
            $data->subtotal = $invoiceData->items[0]->cantidad * $invoiceData->amount;
            $data->currency = 'USD';
            $data->total = $invoiceData->items[0]->cantidad * $invoiceData->amount;
            $data->iva_amount = 0;
            $data->generated_date = Carbon::now()->format('d/m/Y');
            $data->hora = Carbon::now()->format('g:i A');
            $data->due_date = Carbon::now()->addDays(7)->format('d/m/Y');

            $item = array();

            $item['item_number'] = 1;
            $item['id'] = 0;
            $item['code'] = $invoiceData->items[0]->code;
            $item['name'] = $invoiceData->items[0]->name;
            $item['product_type'] = 'Plan';
            $item['measure_unit'] = 'Sp';
            $item['item_count'] = $invoiceData->items[0]->cantidad;
            $item['unit_price'] = $invoiceData->amount;
            $item['subtotal'] = $invoiceData->items[0]->cantidad * $invoiceData->amount;

            if($invoiceData->items[0]->descuento > 0){
                $discount_reason = 'Cupón de descuento';
                $discount = $invoiceData->items[0]->descuento;
            }else{
                $discount_reason = null;
                $discount = 0;
            }
            $item['discount_percentage'] = $invoiceData->items[0]->descuento;
            $item['discount_reason'] = $discount_reason;
            $item['discount'] = $discount;

            $item['iva_type'] = '103';
            $item['iva_percentage'] = 0;
            $item['iva_amount'] = 0;

            $item['total'] = $invoiceData->subtotal + 0;//$data->items->iva_amount;
            $item['is_identificacion_especifica'] = 0;
            $item['is_exempt'] = 0;

            $data->items = [ $item ];
            
            try{
                $invoiceDataSent = $invoice->setInvoiceData($data);
                Log::info('Suscriptor: '. $data->client_id_number . ", Nombre: " . $data->first_name . " " . $data->last_name . " " . $data->last_name2 . ", Plan:" . $invoiceData->items[0]->name );
                if ( !empty($invoiceDataSent) ) {
                    $invoice = $apiHacienda->createInvoice($invoiceDataSent, $tokenApi);
                }
            }catch(\Throwable $e){}

            $company->last_invoice_ref_number = $invoice->reference_number;
            $company->last_document = $invoice->document_number;
            $company->save();
            clearInvoiceCache($invoice);

            return true;
        } else {
            return false;
        }
    }
    
    public function skipPaymentCoupon( $request, $coupon ) {
        $user = auth()->user();
        $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addYears(1);
        $proof = "Pago por transferencia";
        if( $coupon->code == '$$$ETAX100DESCUENTO!' ){
            $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addYears(10);
            $proof = "Equipo de eTax";
        }
        
        $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );
        $sale->status = 1;
        $sale->next_payment_date = $nextPaymentDate;
        $sale->save();
        
        $paymentMethod = PaymentMethod::updateOrCreate([
            'user_id' => $user->id,
            'name' => $user->first_name,
            'last_name' => $user->last_name,
            'last_4digits' => 'N/A',
            'masked_card' => 'N/A',
            'due_date' => 'N/A',
            'token_bn' => 'N/A',
            'default_card' => 0
        ]);
      
        $payment = Payment::updateOrCreate(
            [
                'sale_id' => $sale->id,
            ],
            [
                'payment_method_id' => $paymentMethod->id,
                'coupon_id' => $coupon->id,
                'payment_date' => Carbon::parse(now('America/Costa_Rica')),
                'payment_status' => 2,
                'amount' => 0,
                'charge_token' => 'N/A',
                'proof' => $proof
            ]
        );
        
        return redirect('/wizard')->withMessage('Se aplicó el cupón exitosamente');
           
    }

    public function dailySubscriptionsPayment(){
        $paymentUtils = new PaymentUtils();
        $date = Carbon::parse(now('America/Costa_Rica'));
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful') {
            $unpaidSubscriptions = Sale::where('status', 2)->where('recurrency', '!=', '0');
            foreach($unpaidSubscriptions as $sale){
                $payment = Payment::updateOrCreate(
                    [
                        'sale_id' => $sale->id
                    ],
                    [
                        'payment_date' => $date,
                        'payment_status' => 1,
                        'amount' => $sale->price
                    ]
                );
                $paymentMethod = PaymentMethod::where('user_id', $sale->user->id)->where('default', true)->first();
                $data = new stdClass();
                $data->description = '';
                $data->amount = $sale->price;
                $data->user_name = $sale->user->username;
                $data->cardTokenId = $paymentMethod->token_bn;

                $payment = $this->paymentCharge($data);
                if ($payment['apiStatus'] == "Successful") {
                    $sale->next_payment_date = $date->addMonth($sale->recurrency);
                    $sale->status = 1;
                    $sale->save();
                    $payment->payment_status = 2;
                    $payment->save();
                    //$Invoice = InvoiceController::sendHacienda();
                }
            }
        }else{
            return false;
        }
    }

    public function updateAllSubscriptions(){
        $activeSubscriptions = Sale::where('status', 1);
        $date = Carbon::parse(now('America/Costa_Rica'));
        foreach($activeSubscriptions as $activeSubscription){
            if($date >= $activeSubscription->next_payment_date){
                $activeSubscription->status=2;
                $activeSubscription->save();
            }
        }
    }

    public function pendingCharges(){
        $paymentUtils = new PaymentUtils();
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful') {
            $charges = $paymentUtils->userRequestCharges();
            return view('/payment/pendingCharges')->with('charges', $charges);
        }else{
            return redirect()->back()->withErrors('No se pueden ejecutar consultas en este momento');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
