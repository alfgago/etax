<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Company;
use App\CybersourcePaymentProcessor;
use App\EtaxProducts;
use App\Coupon;
use App\Invoice;
use App\KlapPaymentProcessor;
use App\Mail\SubscriptionPaymentFailure;
use App\Payment;
use App\PaymentProcessor;
use App\Sales;
use App\Subscription;
use App\PaymentMethod;
use App\SubscriptionPlan;
use App\AvailableInvoices;
use App\Team;
use Carbon\Carbon;
use CybsSoapClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Utils\BridgeHaciendaApi;
use App\Utils\PaymentUtils;
use Illuminate\Database\Eloquent\Builder;

/**
 * @group Controller - Pagos
 *
 * Funciones de PaymentController. Todos los request de pagos deberían pasar por aquí, pero el pago en sí debería ser en Payment Utils.
 */
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
        return view('payment/payment-history')->with('payments', $payments);
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
                return $payment->sale->plan->name;
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
    /**
     *getDocReference
     *
     *
     */
    private function getDocReference($docType) {
        $lastSale = Company::find(1)->last_invoice_ref_number + 1;
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }
    /**
     *getDocumentKey
     *
     *
     */
    private function getDocumentKey($docType) {
        $company = Company::find(1);
        $invoice = new Invoice();
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef($company->last_invoice_ref_number + 1);

        return $key;
    }
    /**
     * paymentCheckout
     *
     *
     */
    public function paymentCheckout(){
        $sale = getCurrentSubscription();
        return view('payment/paymentCard')->with('sale', $sale);
    }
    /**
     *storeClient
     *
     *
     */
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

        $user = auth()->user();
        Activity::dispatch(
            $user,
            $cliente,
            [
                'company_id' => $cliente->company_id,
                'id' => $cliente->id
            ],
            "Cliente creado exitosamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return $cliente;
    }
    /**
     *companyDisponible
     *
     *
     */
    public function companyDisponible(){
        try{
            $start_date = Carbon::parse(now('America/Costa_Rica'));
            $company = currentCompanyModel();
            $user_id = $company->user_id;
            $activeCompanies = Company::where('user_id',$user_id)->where('status', 1)->count();
            $sale = Sales::where('company_id', $company->id)->where('is_subscription', true)->first();
            $plan = $sale->plan;
            $availableCompanies = $plan->num_companies;
            $availableCompanies += -1 ;

            if($activeCompanies > $availableCompanies){
                $companies = Company::where('user_id',$user_id)->where('id','!=',$company->id)->where('status',1)->get();
                foreach ($companies as $company) {
                    $availableCompanies += -1 ;
                    if($availableCompanies < 0){
                        Company::where('id', $company->id)
                        ->update(['status' => 0],['updated_at',$start_date]);
                        $companies = Company::where('user_id',$user_id)->get();
                    }

                }
                $availableCompanies = $plan->num_companies;
                return view('payment.companySelect')->with('companies',$companies)->with('companies_puedo',$availableCompanies)->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
            }
        }catch(\Throwable $e){
            Log::error($e->getMessage());
        }
        return redirect('/')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
    }
    /**
     * seleccionEmpresas
     *
     *
     */
    public function seleccionEmpresas(Request $request){
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $company = currentCompanyModel();
        $user_id = $company->user_id;
        $company_id = $company->id;
        Company::where('user_id',$user_id)->whereNotIn('id', $request->empresas)->update(['status' => 0],['updated_at',$start_date]);
        Company::where('user_id',$user_id)->whereIn('id', $request->empresas)->update(['status' => 1],['updated_at',$start_date]);
        if (!in_array($company_id, $request->empresas)) {
            $companyId = intval($request->empresas[0]);
            $team = Team::where( 'company_id', $companyId )->first();
            auth()->user()->switchTeam( $team );
        }
        return redirect('/')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');

    }
    /**
     * facturasDisponibles
     *
     *
     */
    public function facturasDisponibles(){
        try{
            $start_date = Carbon::parse(now('America/Costa_Rica'));
            $month = $start_date->month;
            $year = $start_date->year;

            $company = currentCompanyModel();
            $sale = Sales::where('company_id',$company->id)->where('is_subscription', true)->first();

            $plan = $sale->plan;
            $numInvoices = $plan->num_invoices;

            $availableInvoices = $company->getAvailableInvoices( $year, $month );
            $availableInvoices->monthly_quota = $numInvoices;
            $availableInvoices->save();

        }catch(\Throwable $e){
            Log::error($e->getMessage());
        }
    }
    /**
     *confirmPayment
     *
     *
     */
    public function confirmPayment(Request $request){
        try{
            $user = auth()->user();
            //$paymentProcessor = new PaymentProcessor();
            $paymentGateway = new CybersourcePaymentProcessor();
            $request->number = preg_replace('/\s+/', '',  $request->number);
            $ip = $paymentGateway->getUserIpAddr();
            $request->request->add(['IpAddress' => $ip]);

            if($request->plan_sel == "c"){
                $coupons = Coupon::where('code', $request->coupon)->where('type',1)->count();
                $precio_25 = 8;
                $precio_10 = 10;
                $precio_mes = 14.999;
                $precio_6 = 13.740;
                $precio_anual = 12.491;
                $precio_contabilidad = 0;
                if($coupons > 0){
                    $coupon = Coupon::where('code', $request->coupon)->where('type',1)->first();
                    $precio_contabilidad = $coupon->amount;
                    $precio_25 = $coupon->amount;
                    $precio_10 = $coupon->amount;
                    $precio_mes = $coupon->amount;
                    $precio_6 = $coupon->amount;
                    $precio_anual = $coupon->amount;
                }
                $plan_tier = "Pro ($user->id)";
                $cantidad = $request->num_companies;
                $total_extras = 0;
                if($cantidad > 25){
                   $total_extras = ($cantidad - 25) * $precio_25;
                   $cantidad = 25;
                }
                if($cantidad > 10){
                   $total_extras = $total_extras + (($cantidad - 10) * $precio_10);
                   $cantidad = 10;
                }
                $monthly_price = $cantidad * $precio_mes;
                $six_price = $cantidad * $precio_6;
                $annual_price = $cantidad * $precio_anual;
                $monthly_price += $total_extras;
                $six_price += $total_extras;
                $annual_price += $total_extras;

                $plan = SubscriptionPlan::updateOrCreate(
                    [
                        'plan_tier' => $plan_tier
                    ],
                    [
                        'plan_type' => 'Contador',
                        'num_companies' => $request->num_companies,
                        'num_users' => 10,
                        'num_invoices' => 2000,
                        'ticket_sla' => 1,
                        'call_center_vip' => 1,
                        'setup_help' => 1,
                        'multicurrency' => 1,
                        'e_invoicing' => 1,
                        'pre_invoicing' => 1,
                        'vat_declaration' => 1,
                        'basic_reports' => 1,
                        'intermediate_reports' => 1,
                        'advanced_reports' => 1,
                        'monthly_price' => round($monthly_price,2),
                        'six_price' => round($six_price,2),
                        'annual_price' => round($annual_price,2),
                        'price' =>round($precio_contabilidad,2)
                    ]
                );
                $request->product_id = $plan->id;
            }
            $razonDescuento = null;
            //El descuento por defecto es cero.
            $descuento = 0;
            //Aplica descuento del Banco Nacional
            if( $request->bncupon ) {
                $descuento = 0.1;
                $razonDescuento = "Cupón BN";
            }

            $cuponId = null;
            //Si tiene un cupon adicional, este aplica sobre el de la tarjeta del BN.
            if ( isset($request->coupon) ) {
                $cuponConsultado = Coupon::where('code', $request->coupon)->first();
                if ( isset($cuponConsultado) ) {
                    if( $cuponConsultado->code == '$$$ETAX100DESCUENTO!' || $cuponConsultado->code == '$$$ETAXTRANSFERENCIA!' ){
                        return $this->skipPaymentCoupon( $request, $cuponConsultado );
                    }
                    $descuento = $descuento + ( ($cuponConsultado->discount_percentage) / 100 );
                    $razonDescuento = "Cupón $cuponConsultado->code";
                    if( $request->bncupon ) {
                        $razonDescuento = "Cupón BN + $cuponConsultado->code";
                    }
                    $cuponId = $cuponConsultado->id;
                }
            }
            $request->razonDescuento = $razonDescuento ?? null;
            $discountReason = $razonDescuento ?? null;
            //Crea/actualiza el sale de suscripción
            $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );

            //Revisa recurrencia para definir el costo.
            $recurrency = $request->recurrency;
            $subscriptionPlan = $sale->plan;
            switch ($recurrency) {
                case 1:
                    $costo = $subscriptionPlan->monthly_price;
                    $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addMonths(1);
                    $descriptionMessage = 'Mensual';
                    break;
                case 6:
                    $costo = $subscriptionPlan->six_price * 6;
                    $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addMonths(6);
                    $descriptionMessage = 'Semestral';
                    break;
                case 12:
                    $costo = $subscriptionPlan->annual_price * 12;
                    $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addMonths(12);
                    $descriptionMessage = 'Anual';
                    break;
            }

            //Calcula el monto con descuentos aplicados.
            $montoDescontado = $costo * $descuento;
            $subtotal = ($costo - $montoDescontado);
            $iv = $subtotal * 0.13;
            $amount = $subtotal + $iv;
            $montoDescontado = round( $montoDescontado, 2 );
            $descuento = round( $descuento, 2 );
            $subtotal = round( $subtotal, 2 );
            $iv = round( $iv, 2 );
            $amount = round( $amount, 2 );

            $request->montoDescontado = $montoDescontado;
            //Cupon para pruebas, hace pagos por $1 sin IVA. Deberian ser anuladas luego.
            if( $request->coupon == "!!CUPON1!!" ) {
                $subtotal = 1;
                $amount = 1;
                $descuento = 0;
                $iv = 0;
            }
            //Datos para cybersource

            $request->request->add(['referenceCode' => $request->product_id]);
            $request->request->add(['amount' => $amount]);
            $request->request->add(['subtotal' => $subtotal]);
            $request->request->add(['iva_amount' => $iv]);
            $request->request->add(['montoDescontado' => $montoDescontado]);
            $request->request->add(['razonDescuento' => $discountReason]);
            $request->request->add(['descuento' => $descuento]);

            $cardData = $paymentGateway->getCardNameType($request->number);
            $request->request->add(['cardType' => $cardData->type]);

            //Agrega la tarjeta. Retorna el paymentMethod
            $paymentMethod = $paymentGateway->createCardToken($request);

            $payment = Payment::updateOrCreate(
                [
                    'sale_id' => $sale->id,
                    'payment_status' => 1,
                ],
                [
                    'payment_method_id' => $paymentMethod->id,
                    'payment_date' => Carbon::parse(now('America/Costa_Rica')),
                    'amount' => $amount,
                    'coupon_id' => $cuponId
                ]
            );

            if($payment->payment_gateway === 'klap' || $payment->payment_gateway === ''){
                $payment->payment_gateway = 'cybersource';
                $payment->charge_token = null;
                $payment->save();
            }

            $request->request->add(['token_bn' => $paymentMethod->token_bn]);

            //Si no hay un charge token, significa que no ha sido aplicado. Entonces va y lo aplica
            if( ! isset($payment->charge_token) ) {
                $chargeProof = $paymentGateway->pay($request);
                if($chargeProof){
                    $payment->charge_token = $chargeProof;
                    $payment->save();
                }
            }

            if ( $chargeProof ) {
                $payment->proof = $payment->charge_token;
                $payment->payment_status = 2;
                $payment->save();

                $sale->status = 1;
                $sale->next_payment_date = $nextPaymentDate;
                $sale->save();

                $request->request->add(['item_code' => $subscriptionPlan->id]);
                $request->request->add(['item_name' => $sale->plan->getName() . " / $recurrency meses"]);

                $invoiceData = $paymentGateway->setInvoiceInfo($request);
                $factura = $paymentGateway->crearFacturaClienteEtax($invoiceData);
                if($factura){
                    $this->facturasDisponibles();
                    return redirect('/')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
                }else{
                    $mensaje = 'El pago fue realizado, pero hubo un error al generar su factura. Por favor contacte a soporte para más información.';
                    return redirect('/')->withError($mensaje)->withInput();
                }
            }

            $mensaje = 'El pago ha sido denegado';
            return redirect()->back()->withError($mensaje)->withInput();

        }catch( \Throwable $e ){
            Log::error( "Error en suscripciones ". $e );
            return redirect()->back()->withError("Hubo un error al realizar la suscripción. Por favor reintente o contacte a soporte.")->withInput();
        }

    }
    /**
     * comprarFacturas
     *
     *
     */
    public function comprarFacturas(Request $request){
        $company = currentCompanyModel();
        $available_company_invoices = $company->additional_invoices ?? 0;
        $product_id = $request->product_id;

        $product = EtaxProducts::find($product_id);
        switch ($product_id){
            case 9:
                $additional_invoices = $available_company_invoices + 5;
            break;
            case 10:
                $additional_invoices = $available_company_invoices + 25;
            break;
            case 11:
                $additional_invoices = $available_company_invoices + 50;
            break;
            case 12:
                $additional_invoices = $available_company_invoices + 250;
            break;
            case 13:
                $additional_invoices = $available_company_invoices + 2000;
            break;
            case 14:
                $additional_invoices = $available_company_invoices + 5000;
            break;
        }
        $subtotal = $product->price;
        $iv = $subtotal * 0.13;
        $amount = $subtotal + $iv;
        $request->request->add(['amount' => $amount]);
        $request->request->add(['referenceCode' => $product_id]);
        $request->request->add(['product_name' => $product->name]);

        $paymentInfo = explode('- ', $request->payment_method);
        $request->request->add(['payment_method' => $paymentInfo[0]]);
        //$paymentProcessor = new PaymentProcessor();

        $paymentGateway = PaymentProcessor::selectPaymentGateway($paymentInfo[1] ?? null);
        if($paymentGateway) {
            $ip = $paymentGateway->getUserIpAddr();
            $request->request->add(['IpAddress' => $ip]);
            $pagoProducto = $paymentGateway->comprarProductos($request);
            if($pagoProducto) {
                $client = \App\Client::where('company_id', 1)->where('id_number', $request->id_number)->first();
                $request->request->add(['client_code' => $request->id_number]);
                $request->request->add(['client_id_number' => $request->id_number]);
                if($client){
                    $client_id = $client->id;
                }else{
                    $client_id = '-1';
                }

                $request->request->add(['client_id' => $client_id]);
                $request->request->add(['subtotal' => $subtotal]);
                $request->request->add(['unit_price' => $subtotal]);
                $request->request->add(['iva_amount' => $iv]);
                $request->request->add(['amount' => $amount]);
                $request->request->add(['total' => $amount]);
                $request->request->add(['item_code' => $product->id]);
                $request->request->add(['item_name' => $product->name]);
                $request->request->add(['descuento' => 0]);
                $request->request->add(['expiry' => Carbon::parse(now('America/Costa_Rica'))->addMonths(1)]);
                $request->request->add(['es_exento' => false]);
                $request->request->add(['discount_reason' => null]);
                $request->request->add(['tipo_persona' => $client->tipo_persona ?? 'F']);

                $invoiceData = $paymentGateway->setInvoiceInfo($request);
                $procesoFactura = $paymentGateway->crearFacturaClienteEtax($invoiceData);

                $company->additional_invoices = $additional_invoices;
                $company->save();
                $userId = auth()->user()->id;
                Cache::forget("cache-currentcompany-$userId");

                return redirect('/empresas/comprar-facturas-vista')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
            }else{
                return redirect('/empresas/comprar-facturas-vista')->withErrors('No pudo procesarse el pago');
            }
        } else {
            return redirect('/empresas/comprar-facturas-vista')->withErrors('Debe seleccionar un método de pago');
        }
    }
    /**
     *comprarContabilidades
     *
     *
     */
    public function comprarContabilidades(Request $request){
        try{
            if(!$request->payment_method){
                return redirect()->back()->withErrors('Debe seleccionar un método de pago');
            }
            $paymentMethod = PaymentMethod::where('id', $request->payment_method)->first();
            //$paymentProcessor = new PaymentProcessor();
            $paymentGateway = PaymentProcessor::selectPaymentGateway($paymentMethod->payment_gateway);
            $ip = $paymentGateway->getUserIpAddr();
            $request->request->add(['IpAddress' => $ip]);
            if(isset($paymentGateway)){
                $company = currentCompanyModel();
                $sale = Sales::join('subscription_plans','subscription_plans.id','sales.etax_product_id')->where('company_id', $company->id)
                ->where('is_subscription', 1)->first();

                $cantidad = $sale->num_companies + $request->contabilidades;
                $precio_25 = 8;
                $precio_10 = 10;
                $precio_mes = 14.999;
                $precio_seis = 13.740;
                $precio_year = 12.491;
                if($sale->price != 0){
                    $precio_25 = $sale->price;
                    $precio_10 = $sale->price;
                    $precio_mes = $sale->price;
                    $precio_seis = $sale->price;
                    $precio_year = $sale->price;
                }
                $total_extras = 0;
                if($cantidad > 25){
                    $total_extras = ($cantidad - 25) * $precio_25;
                    $cantidad = 25;
                }
                if($cantidad > 10){
                   $total_extras += ($cantidad - 10) * $precio_10;
                   $cantidad = 10;
                }
                $monthly_price = $cantidad * $precio_mes;
                $six_price = $cantidad * $precio_seis;
                $annual_price = $cantidad * $precio_year;
                $monthly_price += $total_extras;
                $six_price += $total_extras;
                $annual_price += $total_extras;
                $six_price = $six_price * 6;
                $annual_price = $annual_price * 12;
                $cantidad = $sale->num_companies + $request->contabilidades;
                SubscriptionPlan::where('id', $sale->etax_product_id)
                    ->update(['num_companies' => $cantidad],['monthly_price' => $monthly_price],['six_price' => $six_price],['annual_price' => $annual_price]);
                $existentes = $sale->num_companies;
                $total = 0;
                $total_extras = 0;

                if($cantidad > 25){
                    $total_extras = ($cantidad - $existentes ) * $precio_25;
                    $cantidad = 25;
                }
                if($cantidad > 10){
                    $total_extras += ($cantidad - $existentes ) * $precio_10;
                    $cantidad = 10;
                }
                $diff = (float)$request->diff;
                if($sale->recurrency == 1){
                    $total_extras = $total_extras / 31 * $diff;
                    $total = $total_extras;
                }
                if($sale->recurrency == 6){
                    $total_extras = $total_extras / 133 * $diff;
                    $total = $total_extras * 6;
                }
                if($sale->recurrency == 12){
                    $total_extras = $total_extras / 366 * $diff;
                    $total = $total_extras * 12;
                }
                $iva_amount = $total * 0.13;
                $amount = $iva_amount + $total_extras;
                $request->request->add(['subtotal' => $total_extras]);
                $request->request->add(['token_bn' => $paymentMethod->token_bn]);
                $request->request->add(['unit_price' => $total_extras]);
                $request->request->add(['iva_amount' => $iva_amount]);
                $request->request->add(['amount' => $amount]);
                $request->request->add(['amount' => $amount]);
                $request->request->add(['total' => $request->amount]);
                $request->request->add(['item_code' => $sale->etax_product_id]);
                $request->request->add(['product_id' => $sale->etax_product_id]);
                $request->request->add(['item_name' => "Contabilidades extra"]);
                $request->request->add(['descuento' => 0]);
                $request->request->add(['expiry' => Carbon::parse(now('America/Costa_Rica'))->addMonths(1)]);
                $request->request->add(['es_exento' => false]);
                $request->request->add(['discount_reason' => null]);
                $request->request->add(['etax_product_id' => 16]);
                $request->request->add(['referenceCode' => 16]);

                $client = \App\Client::where('company_id', $company->id)->where('id_number', $request->id_number)->first();
                $request->request->add(['client_code' => $client->id]);
                $request->request->add(['client_id_number' => $client->id_number]);
                $request->request->add(['client_id' => $client->id_number]);
                $request->request->add(['tipo_persona' => $client->tipo_persona]);

                $chargeCreated = $paymentGateway->comprarProductos($request);
                if($chargeCreated){
                    $invoiceData = $paymentGateway->setInvoiceInfo($request);
                    $procesoFactura = $paymentGateway->crearFacturaClienteEtax($invoiceData);
                    $company->save();
                    return redirect()->back()->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
                }else{
                    return redirect()->back()->withErrors('No se pudo procesar el pago');
                }
            }else{
                return redirect()->back()->withErrors('No se pudo procesar el pago');
            }
        }catch ( \Exception $e){
            Log::error('Error al anular facturar -->'.$e);
            return redirect()->back()->withErrors('Hubo un error con el pago');
        }
    }
    /**
     * skipPaymentCoupon
     *
     *
     */
    public function skipPaymentCoupon( $request, $coupon ) {
        $user = auth()->user();
        $nextPaymentDate = Carbon::parse(now('America/Costa_Rica'))->addMonths($request->recurrency);
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
                'proof' => "Transferencia",
                'payment_gateway' => 'bank'
            ]
        );

        $this->facturasDisponibles();
        return $this->companyDisponible();
    }
    /**
     * dailySubscriptionsPayment
     *
     *
     */
    public function dailySubscriptionsPayment(){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $unpaidSubscriptions = Sales::where('status', 2)->where('recurrency', '!=', '0')->get();
        foreach($unpaidSubscriptions as $sale){
            $subtotal = $sale->plan->monthly_price;
            switch ($sale->recurrency){
                case 1:
                    $subtotal = $sale->plan->monthly_price;
                    break;
                case 6:
                    $subtotal = $sale->plan->six_price;
                    break;
                case 12:
                    $subtotal = $sale->plan->annual_price;
                    break;
            }
            $iv = $subtotal * 0.13;
            $amount = $subtotal + $iv;

            $paymentMethod = PaymentMethod::where('user_id', $sale->user->id)->where('default_card', true)->first();
            $company = $sale->company;
            //$paymentProcessor = new PaymentProcessor();
            $paymentGateway = PaymentProcessor::selectPaymentGateway($paymentMethod->payment_gateway);
            if($paymentGateway){
                $data = new stdClass();
                $data->description = 'Pago suscripción eTax';
                $data->amount = $amount;
                $data->user_id = $sale->user->id;
                $data->user_name = $sale->user->username;
                $data->cardTokenId = $paymentMethod->token_bn;

                if( ! isset($payment->charge_token) ) {
                    $payment = $paymentGateway->createPayment($data);
                }
                if (gettype($data) == 'array') {
                    $data->chargeTokenId = $payment['charge_token'];
                } else if (gettype($data) == 'object'){
                    $data->chargeTokenId = $payment->ccCaptureReply->reconciliationID;
                }
                $data->token_bn = $paymentMethod->token_bn;
                $data->referenceCode = $sale->plan->id;

                $appliedCharge = $paymentGateway->pay($data);

                if (gettype($appliedCharge) == 'array') {
                    $payment->proof = $appliedCharge['retrievalRefNo'];
                } else if (gettype($appliedCharge) == 'object'){
                    $payment->proof = $appliedCharge->requestID;
                }

                if ($payment->proof) {
                    $payment->payment_status = 2;
                    $payment->save();

                    $sale->next_payment_date = $date->addMonths($sale->recurrency);
                    $sale->status = 1;
                    $sale->save();

                    $company->item_code = $sale->etax_product_id;
                    $company->item_name = $sale->plan->name . " / $sale->recurrency meses";
                    $company->amount = $amount;
                    $company->subtotal = $subtotal;
                    $company->iva_amount = $iv;
                    $company->discount_reason = null;
                    $company->descuento = 0;
                    $company->discount_reason = null;
                    $company->iva_amount = $iv;
                    $company->unit_price = $subtotal;
                    $company->subtotal = $subtotal;
                    $company->total = $amount;

                    $invoiceData = $paymentGateway->setInvoiceInfo($company);

                    $factura = $paymentGateway->crearFacturaClienteEtax($invoiceData);
                }else{
                    \Mail::to($company->email)->send(new \App\Mail\SubscriptionPaymentFailure(
                        [
                            'name' => $company->name . ' ' . $company->last_name,
                            'product' => $sale->plan->plan_type,
                            'card' => $paymentMethod->masked_card
                        ]
                    ));
                }
            }else{
                \Mail::to($company->email)->send(new \App\Mail\SubscriptionPaymentFailure(
                    [
                        'name' => $company->name . ' ' . $company->last_name,
                        'product' => $sale->plan->plan_type,
                        'card' => "No indica"
                    ]
                ));
            }
        }
        return true;
    }
    /**
     * updateAllSubscriptions
     *
     *
     */
    public function updateAllSubscriptions(){
        $activeSubscriptions = \App\Sales::where('status', 1)->get();
        $now = \Carbon\Carbon::now();
        foreach($activeSubscriptions as $activeSubscription){
            $nextPaymentDate = Carbon\Carbon::parse($activeSubscription['next_payment_date']);
            if( $nextPaymentDate <= $now ){
                $activeSubscription->status = 2;
            }
            if( $nextPaymentDate->addDays(3) <= $now ){
                $activeSubscription->status = 4;
            }

            $activeSubscription->save();
        }
    }
    /**
     * pendingCharges
     *
     *
     */
    public function pendingCharges(){
        $user = auth()->user();

        $charges = Payment::whereHas('sale', function ($query) use($user) {
            $query->where('user_id', $user->id);
        })->get();

        if($charges) {
            return view('/payment/pendingCharges')->with('charges', $charges);
        }else{
            return redirect()->back()->withErrors('No se pueden ejecutar consultas en este momento');
        }
    }
    /**
     *pagarCargo
     *
     *
     */
    public function pagarCargo($paymentId){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $company = currentCompanyModel();
        $user = auth()->user();

        //$paymentProcessor = new PaymentProcessor();
        $paymentMethod = PaymentMethod::where('user_id', $user->id)->where('default_card', true)->first();
        $paymentGateway = PaymentProcessor::selectPaymentGateway($paymentMethod->payment_gateway);

        if(!$paymentMethod){
            $paymentMethod = PaymentMethod::where('user_id', $user->id)->first();
        }
        $payment = Payment::find($paymentId);
        if($paymentMethod->payment_gateway === $payment->payment_gateway){
            $payment->payment_date = $date;
            $payment->payment_method_id = $paymentMethod->id;
            $payment->save();

            $sale = Sales::find($payment->sale_id);

            $amount = $payment->amount;
            $subtotal = $amount / 1.13;
            $iv = $amount - $subtotal;

            $data = new stdClass();
            $data->referenceCode = $sale->etax_product_id;
            $data->description = $sale->saleDescription();
            $data->user_name = $user->user_name;
            $data->amount = $amount;

            //Si no hay un charge token, significa que no ha sido aplicado. Entonces va y lo aplica
            if( ! isset($payment->charge_token) || $payment->charge_token == 'N/A' || $payment->charge_token == '' ) {
                $chargeIncluded = $paymentGateway->createPayment($data);
                if (gettype($chargeIncluded) == 'array') {
                    if($chargeIncluded['apiStatus'] == "Successful"){
                        $appliedCharge_Id = $chargeIncluded['retrievalRefNo'];
                    }
                } else if (gettype($chargeIncluded) == 'object'){
                    if($chargeIncluded->decision == 'ACCEPT'){
                        $appliedCharge_Id = $chargeIncluded->requestID;
                    }
                }
                $chargeTokenId = $appliedCharge_Id;
                $payment->charge_token = $chargeTokenId;
                $payment->save();
            }

            $data->chargeTokenId = $payment->charge_token;
            $data->token_bn = $paymentMethod->token_bn;

            $appliedCharge = $paymentGateway->pay($data);
            if (gettype($data) == 'array') {
                if($appliedCharge['apiStatus'] == "Successful"){
                    $paymentAccepted = true;
                    $appliedChargeId = $appliedCharge['retrievalRefNo'];
                }
            } else if (gettype($data) == 'object'){
                if($appliedCharge->decision == 'ACCEPT'){
                    $paymentAccepted = true;
                    $appliedChargeId = $appliedCharge->requestID;
                }
            }

            if ( $paymentAccepted ) {
                $payment->proof = $appliedChargeId;
                $payment->payment_status = 2;
                $payment->save();

                $sale->next_payment_date = Carbon::parse(now('America/Costa_Rica'))->addMonths($sale->recurrency);
                $sale->status = 1;
                $sale->save();

                $company->item_code = $sale->id;
                $company->item_name = $sale->saleDescription();
                $company->amount = $amount;
                $company->subtotal = $subtotal;
                $company->iva_amount = $iv;
                $company->discount_reason = null;
                $company->descuento = 0;
                $company->discount_reason = null;
                $company->iva_amount = $iv;
                $company->unit_price = $subtotal;
                $company->subtotal = $subtotal;
                $company->total = $amount;

                $invoiceData = $paymentGateway->setInvoiceInfo($company);
                $factura = $paymentGateway->crearFacturaClienteEtax($invoiceData);
                return redirect()->back()->withMessage('Pago procesado');
            }else{
                return redirect()->back()->withErrors('El pago no pudo ser procesado. Por favor intente más tarde.');
            }
        }else{
            return redirect()->back()->withErrors('Debe seleccionar un método de pago por defecto');
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
