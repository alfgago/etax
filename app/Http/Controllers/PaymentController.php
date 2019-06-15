<?php

namespace App\Http\Controllers;

use App\Company;
use App\EtaxProducts;
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
        $user = auth()->user();
        $cantidad = PaymentMethod::where('user_id', $user->id)->get()->count();
        return view('payment/index')->with('cantidad', $cantidad);
    }
    public function createView(){
        return view('payment/CreatePaymentMethod');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $paymentUtils = new PaymentUtils();
        $cards = array(
            $request->number
        );
        foreach ($cards as $c) {
            $check = $paymentUtils->checkCC($c, true);
            if ($check !== false) {
                $typeCard = $check;
            } else {
                echo "$c - Not a match";
            }
        }
        if(isset($typeCard)){
            $bnStatus = $paymentUtils->statusBNAPI();
            if ($bnStatus['apiStatus'] == 'Successful') {
                switch ($typeCard) {
                    case "Visa":
                        $cardType = '001';
                        $nameCard = "Visa";
                        break;
                    case "Mastercard":
                        $cardType = '002';
                        $nameCard = "Mastercard";
                        break;
                    case "American Express":
                        $cardType = '003';
                        $nameCard = "";
                        break;
                }
                $cardYear = substr($request->expiry, -2);
                $cardMonth = substr($request->expiry, 0, 2);
                $cardBn = new Client();
                $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                    'headers' => [
                        'Content-Type' => "application/json",
                    ],
                    'json' => ['applicationName' => 'ETAX',
                        'userName' => $user->user_name,
                        'userPassword' => 'Etax-' . $user->id . 'Klap',
                        'cardDescription' => $nameCard,
                        'primaryAccountNumber' => $request->number,
                        "expirationMonth" => $cardMonth,
                        "expirationYear" => '20' . $cardYear,
                        "verificationValue" => $request->cvc
                    ],
                    'verify' => false,
                ]);

                $card = json_decode($cardCreationResult->getBody()->getContents(), true);
                if ($card['apiStatus'] == 'Successful') {
                    $last_4digits = substr($request->number, -4);
                    $token_bn = $card['cardTokenId'];
                    $paymentMethod = PaymentMethod::create([
                        'user_id' => $user->id,
                        'name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'last_4digits' => $last_4digits,
                        'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                        'token_bn' => $token_bn
                    ]);
                    return redirect('payments');
                } else {
                    return redirect()->back()->withErrors('No se aprobó esta tarjeta');
                }
            } else {
                return redirect()->back()->withErrors('Pagos en línea está fuera de servicio en este momento. No se pudo gestionar la transacción');
            }
        }else{
            return redirect()->back()->withErrors('Solamente aceptamos Visa y Master-Card');
        }
    }
    
    /*
    */
    public function indexData(){
        $user = auth()->user();
        $query = PaymentMethod::where('user_id', $user->id);
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($paymentMethod) {
                return view('payment.actions', [
                    'data' => $paymentMethod
                ])->render();
            })
            ->editColumn('last_4digits', function(PaymentMethod $paymentMethod) {
                if($paymentMethod->default_card == 1){
                    $defaultText = ' Por defecto';
                }else{
                    $defaultText = '';
                }
                return 'Termina en ...' . $paymentMethod->last_4digits . ' ' . $defaultText;
            })
            ->rawColumns(['actions'])
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
        $paymentUtils = new PaymentUtils();
        $user = auth()->user();
        
        //Crea el sale de suscripción
        $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );
        
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $date = Carbon::now()->format('Y/m/d');
        if (isset($request->coupon)) {
            $cuponConsultado = Coupon::where('code', $request->coupon)
                ->where('used', 0)->get();
            if (isset($cuponConsultado)) {
                $descuento = ($cuponConsultado->discount_percentage) / 100;
            } else {
                $descuento = 0;
            }
        } else {
            $descuento = 0;
        }
        
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
                $numberOfPayments = '12';
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
        
        foreach ($cards as $c) {
            $check = $paymentUtils->checkCC($c, true);
            //if ($check !== false) {
                $typeCard = $check;
            //}
        }
        
        $nameCard = $typeCard ? $typeCard : 'Visa';
        
        $amount = 1;
        //Revisa si el API del BN esta arriba.
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            //Agrega la tarjeta al API del BN.
            $card = $paymentUtils->userCardInclusion($request->number, $nameCard, $cardMonth, $cardYear, $request->cvc);
            if($card['apiStatus'] == 'Successful'){
                $last_4digits = substr($request->number, -4);
                $paymentMethod = PaymentMethod::updateOrCreate([
                    'user_id' => $user->id,
                    'name' => $request->first_name_card,
                    'last_name' => $request->last_name_card,
                    'last_4digits' => $last_4digits,
                    'due_date' => $cardMonth . '/' .$cardYear,
                    'token_bn' => $card['cardTokenId'],
                    'default_card' => 1
                ]);
                
                $payment = Payment::updateOrCreate(
                    [
                        'sale_id' => $sale->id,
                        'payment_method_id' => $paymentMethod->id,
                        'payment_date' => $date,
                        'payment_status' => 1,
                        'amount' => $amount
                    ],
                    [
                        'proof' => $card['cardTokenId']
                    ]
                );

                $data = new stdClass();
                $data->description = 'Pago Suscripción Etax';
                $data->amount = $amount;
                $data->user_name = $user->user_name;
                $data->cardTokenId = $card['cardTokenId'];
                $paymentCard = $this->paymentCharge($data);
                if ($paymentCard['apiStatus'] == "Successful") {
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
                        return redirect('/wizard')->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito.');
                    }
                } else {
                    $mensaje = 'El pago ha sido denegado';
                    return redirect()->back()->withError($mensaje);
                }
            }else{
                $mensaje = 'No se pudo verificar la informacion de la tarjeta. ';
                return redirect()->back()->withError($mensaje);
            }
        }else{
            $mensaje = 'Pagos en Linea esta fuera de servicio. Dirijase a Configuraciones->Gestion de Pagos- para agregar una tarjeta';
            return redirect('wizard')->withError($mensaje);
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

            if($invoiceData->items[0]->descuento > 0){
                $discount_reason = 'Cupon con descuento de ' . $invoiceData->item->descuento;
                $discount = $invoiceData->items[0]->descuento;
            }else{
                $discount_reason = '';
                $discount = 0;
            }

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

            $invoiceDataSent = $invoice->setInvoiceData($data);
            if ( !empty($invoiceDataSent) ) {
                $invoice = $apiHacienda->createInvoice($invoiceDataSent, $tokenApi);
            }

            $company->last_invoice_ref_number = $invoice->reference_number;
            $company->last_document = $invoice->document_number;
            $company->save();
            clearInvoiceCache($invoice);

            return true;
        } else {
            return false;
        }
    }

    public function paymentTokenUpdateView($id){
        $subscription = getCurrentSubscription();
        $Payment = PaymentMethod::find($id);
        return view('payment/updatePaymentMethods')->with('payment', $Payment)
                                                        ->with('Id', $id);
    }
    public function paymentTokenUpdate(Request $request){
        $paymentUtils = new PaymentUtils();
        $cards = array(
            $request->number
        );
        foreach($cards as $c){
            $check = $this->checkCC($c, true);
            if($check!==false){
                $typeCard = $check;
            }else{
                echo "$c - Not a match";
            }
        }
        switch ($typeCard){
            case "Visa":
                $cardType = '001';
                break;
            case "Mastercard":
                $cardType = '002';
                break;
            case "American Express":
                $cardType = '003';
                break;
        }
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($request->Id);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $cardBn = new Client();
            $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserUpdateCard?applicationName=string&userName=string&userPassword=string&cardTokenId=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => 'Etax-' . $user->id . 'Klap',
                    'cardTokenId' => $paymentMethod->token_bn,
                    "cardDescription" => $paymentMethod->card_name,
                    "primaryAccountNumber" => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => $request->cvc
                ],
                'verify' => false,
            ]);
            $card = json_decode($cardCreationResult->getBody()->getContents(), true);
            $last_4digits = substr($request->number, -4);
            $due_date = $request->cardMonth . ' ' . $request->cardYear;
            $paymentMethod->last_4digits = $last_4digits;
            $paymentMethod->due_date = $due_date;
            $paymentMethod->nameCard = $typeCard;
            $paymentMethod->updated_by = $user->id;
            $paymentMethod->save();
            return redirect('payments');
        }else{
            $mensaje = 'Transacción no disponible en este momento';
            return redirect()->back()->withError($mensaje);
        }
    }

    public function paymentTokenDelete($Id){
        $paymentUtils = new PaymentUtils();
        $user = auth()->user();
        $paymentMethod = PaymentMethod::find($Id);
        $this->authorize('update', $paymentMethod);
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $cardBn = new Client();
            $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserDeleteCard", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => 'Etax-' . $user->id . 'Klap',
                    'cardTokenId' => $paymentMethod->token_bn
                ],
                'verify' => false,
            ]);
            $card = json_decode($cardCreationResult->getBody()->getContents(), true);
            if($card['apiStatus'] == 'Successful') {
                $paymentMethod->updated_by = $user->id;
                $paymentMethod->save();
                $paymentMethod->delete();
                return redirect('payments')->withMessage('Método de pago eliminado');
            }else{
                $mensaje = 'No se pudo eliminar el método de pago: ' . $card['apiStatus'];
                return redirect()->back()->withError($mensaje);
            }
        }else{
            $mensaje = 'Transacción no disponible en este momento';
            return redirect()->back()->withError($mensaje);
        }
    }

    public function paymentCharge($request){
        $appCharge = new Client();
        $appChargeBn = $appCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => 'ETAX',
                'applicationPassword' => 'ETFTTJUN1019%',
                'chargeDescription' => $request->description,
                'userName' => $request->user_name,
                "transactionCurrency" => "USD",
                "transactionAmount" => $request->amount
            ],
            'verify' => false,
        ]);
        $chargeAplied = json_decode($appChargeBn->getBody()->getContents(), true);
        Log::info("AppIncludeCharge".  implode(", ",$appChargeBn->getBody()->getContents()) );
        $chargeTokenId = $chargeAplied['chargeTokenId'];
        /****************************************************/
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppApplyCharge?applicationName=string&applicationPassword=string&userName=string&chargeTokeId=string&cardTokenId=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => 'ETAX',
                'applicationPassword' => 'ETFTTJUN1019%',
                'userName' => $request->user_name,
                'chargeTokenId' => $chargeTokenId,
                "cardTokenId" => $request->cardTokenId
            ],
            'verify' => false,
        ]);
        $charge = json_decode($chargeBn->getBody()->getContents(), true);
        Log::info("AppApplyCharge". implode(", ",$chargeBn->getBody()->getContents()) );
        return $charge;
    }
    /**
    *
    *
    *
    *
    */
    public function comprarProductos(Request $request){
        $paymentUtils = new PaymentUtils();
        //recibe parametros: etax_product_id, amount, description
        $date = Carbon::parse(now('America/Costa_Rica'));
        $current_company = currentCompany();
        $user_id = auth()->user()->id;
        $bnStatus = $paymentUtils->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $sale = Sale::create([
                "user_id" => $user_id,
                "company_id" => $current_company,
                "etax_product_id" => $request->etax_product_id,
                "status" => 1,
                "recurrency" => 0
            ]);
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'payment_date' => $date,
                'payment_status' => 1,
                'amount' => $request->amount
            ]);
            $paymentMethod = PaymentMethod::where('user_id', $sale->user->id)->where('default', true)->first();
            $data = new stdClass();
            $data->description = $request->description;
            $data->amount = $request->amount;
            $data->user_name = $sale->user->username;
            $data->cardTokenId = $paymentMethod->token_bn;
            $paymentTransaction = $this->paymentCharge($data);
            if($paymentTransaction['apiStatus'] == "Successful"){
                $payment->payment_status = 2;
                $payment->save();
                $sale->status = 1;
                $sale->save();
                //$Invoice = InvoiceController::sendHacienda();
            }else{
                return redirect()->back()->withError('No se pudo procesar el pago');
            }
        }else{
            return redirect()->back()->withError('Transacción no disponible en este momento');
        }
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
