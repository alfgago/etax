<?php

namespace App\Http\Controllers;

use App\Company;
use App\EtaxProducts;
use App\Coupon;
use App\Invoice;
use App\Mail\SubscriptionPaymentFailure;
use App\Payment;
use App\Sales;
use App\Subscription;
use App\PaymentMethod;
use App\SubscriptionPlan;
use App\AvailableInvoices;
use App\Team;
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

    public function CompanyDisponible(){
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $company = currentCompanyModel();
        $user_id = $company->user_id;
        $companies_tengo = Company::where('user_id',$user_id)->where('status',1)->count();
        $sale = Sales::where('company_id',$company->id)->first();
        $plan = $sale->etax_product_id;
        $porduct_etax = SubscriptionPlan::where('id',$plan)->first();
        $companies_puedo = $porduct_etax->num_companies;
        $companies_puedo += -1 ;
        if($companies_tengo >  $companies_puedo  ){
            $companies = Company::where('user_id',$user_id)->where('id','!=',$company->id)->where('status',1)->get();
            foreach ($companies as $company) {
                $companies_puedo += -1 ;
                if($companies_puedo < 0){
                    Company::where('id', $company->id)
                    ->update(['status' => 0],['updated_at',$start_date]);
                    $companies = Company::where('user_id',$user_id)->get();
                }

            }
            $companies_puedo = $porduct_etax->num_companies;
            return view('payment.companySelect')->with('companies',$companies)->with('companies_puedo',$companies_puedo);
        }
        return redirect('/')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
    }
 
    public function SeleccionEmpresas(Request $request){
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

    public function FacturasDisponibles(){
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $mes = $start_date->format("m");
        $mes = intval($mes);
        $year = $start_date->format("Y");
        $company = currentCompanyModel();
        $sale = Sales::where('company_id',$company->id)->first();
        $plan = $sale->etax_product_id;
        $porduct_etax = SubscriptionPlan::where('id',$plan)->first();
        $num_invoices = $porduct_etax->num_invoices;
        $AvailableInvoices = AvailableInvoices::where('company_id',$company->id)->where('month',$mes)->where('year',$year)->first();
        if($AvailableInvoices != null){
            if($num_invoices > $AvailableInvoices->monthly_quota){
                AvailableInvoices::where('id', $AvailableInvoices->id)
                    ->update(['monthly_quota' => $num_invoices],['updated_at',$start_date]);
            }
        }else{
            AvailableInvoices::insert([
                ['company_id' => $company->id, 'monthly_quota' => $num_invoices, 
                'month' => $mes, 'year' => $year, 
                'current_month_sent' => 0, 'created_at' => $start_date, 'updated_at' => $start_date]
            ]);

        }
    }

    public function confirmPayment(Request $request){
        
        try{
            $user = auth()->user();
            $paymentUtils = new PaymentUtils();
            
            $request->number = preg_replace('/\s+/', '',  $request->number);

            if($request->plan_sel == "c"){
                $plan_tier = "Pro ($user->id)";
                $cantidad = $request->num_companies;
                $total_extras = 0;
                if($cantidad > 25){
                   $total_extras = ($cantidad - 25) * 8;
                   $cantidad = 25;
                }
                if($cantidad > 10){
                   $total_extras += ($cantidad - 10) * 10;
                   $cantidad = 10;
                }
                $monthly_price = $cantidad * 14.999;
                $six_price = $cantidad * 13.740;
                $annual_price = $cantidad * 12.491;
                $monthly_price += $total_extras;
                $six_price += $total_extras;
                $annual_price += $total_extras;
                $six_price = $six_price * 6;
                $annual_price = $annual_price * 12;
                $plan = SubscriptionPlan::updateOrCreate(
                    [
                        'plan_tier' => $plan_tier
                    ],
                    [
                        'plan_type' => 'Contador',
                        'num_companies' => $request->num_companies,
                        'num_users' => 10,
                        'num_invoices' => 10000,
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
                        'annual_price' => round($annual_price,2)
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
                }
            }
            
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
            
            $cards = array(
                $request->number
            );
            $cardYear = substr($request->expiry, -2);
            $cardMonth = substr($request->expiry, 0 , 2);
            
            //Cupon para pruebas, hace pagos por $1 sin IVA. Deberian ser anuladas luego.
            if( $request->coupon == "!!CUPON1!!" ) {
                $subtotal = 1;
                $amount = 1;
                $descuento = 0;
                $iv = 0;
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
            if($bnStatus['apiStatus'] != 'Successful'){
                $mensaje = 'Hubo un error procesando el pago. Por favor contacte a nuestro centro de servicios o vuelva a intentar en unos minutos.';
                return redirect('wizard')->withError($mensaje)->withInput();
            }
            
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
                    'payment_date' => Carbon::parse(now('America/Costa_Rica')),
                    'amount' => $amount
                ]
            );
            
            $data = new stdClass();
            $data->description = "Pago inicial plan etax $subscriptionPlan->name";
            $data->amount = $amount;
            $data->user_name = $user->user_name;
            
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
                $invoiceData->subtotal = $subtotal;
                $invoiceData->iva_amount = $iv;
                $invoiceData->discount_reason = $razonDescuento;

                $item = new stdClass();
                $item->total = $amount;
                $item->code = $sale->etax_product_id;
                $item->name = $sale->plan->name . " / $recurrency meses";
                $item->descuento = $montoDescontado;
                $item->discount_reason = $razonDescuento;
                $item->cantidad = 1;
                $item->iva_amount = $iv;
                $item->unit_price = $costo;
                $item->subtotal = $subtotal;
                $item->total = $amount;
                
                $invoiceData->items = [$item];
                $factura = $paymentUtils->crearFacturaClienteEtax($invoiceData);
                
                if($factura){
                    $this->FacturasDisponibles();
                    return $this->CompanyDisponible();
                }
            } else {
                $mensaje = 'El pago ha sido denegado';
                return redirect()->back()->withError($mensaje)->withInput();
            }
        
        }catch( \Throwable $e ){
            Log::error( "Error en suscripciones ". $e->getMessage() );
            return redirect()->back()->withError("Hubo un error al realizar la suscripción. Por favor reintente o contacte a soporte.")->withInput();
        }
        
    }

    public function comprarFacturas(Request $request){
        
        $date = Carbon::parse(now('America/Costa_Rica'));
        $company = currentCompanyModel();
        $available_company_invoices = !$company->additional_invoices ? $available_company_invoices = 0 : $company->additional_invoices;
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
        
        $paymentUtils = new PaymentUtils();
        if(isset($request->payment_method)){
            $pagoProducto = $paymentUtils->comprarProductos($request, $product, $amount);
            if($pagoProducto){
                $user = auth()->user();
                
                $client = \App\Client::where('company_id', $company->id)->where('id_number', $request->id_number)->first();
                
                $invoiceData = new stdClass();
                $invoiceData->client_code = $request->id_number;
                $invoiceData->client_id_number = $request->id_number;
                if($client){
                    $invoiceData->client_id = $client->id;
                }else{
                    $invoiceData->client_id = '-1';
                }
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
                $invoiceData->es_exento = false;
                $invoiceData->email = $request->email;
                $invoiceData->expiry = $request->expiry;
                $invoiceData->amount = $amount;
                $invoiceData->subtotal = $subtotal;
                $invoiceData->iva_amount = $iv;
                $invoiceData->discount_reason = null;

                $item = new stdClass();
                $item->total = $amount;
                $item->code = $product->id;
                $item->name = $product->name;
                $item->descuento = 0;
                $item->discount_reason = null;
                $item->cantidad = 1;
                $item->iva_amount = $iv;
                $item->unit_price = $subtotal;
                $item->subtotal = $subtotal;
                $item->total = $amount;

                $invoiceData->items = [$item];
                $procesoFactura = $paymentUtils->crearFacturaClienteEtax($invoiceData);
                
                $company->additional_invoices = $additional_invoices;
                $company->save();
                
                return redirect('/empresas/comprar-facturas-vista')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
            }else{
                return redirect('/empresas/comprar-facturas-vista')->withErrors('No pudo procesarse el pago');
            }
        }else{
            return redirect('/empresas/comprar-facturas-vista')->withErrors('Debe seleccionar un método de pago');
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
        
        
        $this->FacturasDisponibles();
        return $this->CompanyDisponible();
        
           
    }

    public function dailySubscriptionsPayment(){
        $paymentUtils = new PaymentUtils();
        $date = Carbon::parse(now('America/Costa_Rica'));
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful') {

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
                
                if($paymentMethod){
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
                    $data->description = 'Pago suscripción eTax';
                    $data->amount = $amount;
                    $data->user_name = $sale->user->username;

                    if( ! isset($payment->charge_token) ) {
                        $chargeIncluded = $paymentUtils->paymentIncludeCharge($data);
                        $chargeTokenId = $chargeIncluded['chargeTokenId'];
                        $payment->charge_token = $chargeTokenId;
                        $payment->save();
                    }

                    $data->chargeTokenId = $payment->charge_token;
                    $data->cardTokenId = $paymentMethod->token_bn;

                    $appliedCharge = $paymentUtils->paymentApplyCharge($data);
                    /**********************************************/
                    
                    if ($appliedCharge['apiStatus'] == "Successful") {
                        $payment->proof = $appliedCharge['retrievalRefNo'];
                        $payment->payment_status = 2;
                        $payment->save();

                        $sale->next_payment_date = $date->addMonths($sale->recurrency);
                        $sale->status = 1;
                        $sale->save();
                        
                        $invoiceData = new stdClass();
                        $invoiceData->client_code = $company->id_number;
                        $invoiceData->client_id_number = $company->id_number;
                        $invoiceData->client_id = $company->id_number;
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
                        $invoiceData->address = $company->address;
                        $invoiceData->phone = $company->phone;
                        $invoiceData->es_exento = false;
                        $invoiceData->email = $company->email;
                        $invoiceData->expiry = $company->expiry;
                        $invoiceData->amount = $amount;
                        $invoiceData->subtotal = $subtotal;
                        $invoiceData->iva_amount = $iv;
                        $invoiceData->discount_reason = null;

                        $item = new stdClass();
                        $item->total = $amount;
                        $item->code = $sale->etax_product_id;
                        $item->name = $sale->plan->name . " / $sale->recurrency meses";
                        $item->descuento = 0;
                        $item->discount_reason = null;
                        $item->cantidad = 1;
                        $item->iva_amount = $iv;
                        $item->unit_price = $subtotal;
                        $item->subtotal = $subtotal;
                        $item->total = $amount;

                        $invoiceData->items = [$item];
                        $factura = $paymentUtils->crearFacturaClienteEtax($invoiceData);
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
        }
        return true;
    }

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
