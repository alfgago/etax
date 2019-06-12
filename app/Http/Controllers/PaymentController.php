<?php

namespace App\Http\Controllers;

use App\EtaxProducts;
use App\Payment;
use App\Sales;
use App\Subscription;
use App\PaymentMethod;
use App\SubscriptionPlan;
use Carbon\Carbon;
use CybsSoapClient;
use Illuminate\Http\Request;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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
        return view('Payment/index')->with('cantidad', $cantidad);
    }
    public function createView(){
        return view('payment/CreatePaymentMethod');
    }
    public function StatusBnAPI(){
        $BnEcomAPIStatus = new Client();
        $APIStatus = $BnEcomAPIStatus->request('POST', "https://emcom.oneklap.com:2263/api/LogOnApp?applicationName=string&applicationPassword=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'applicationPassword' => 'ETFTTJUN1019%'
            ],
            'verify' => false,
        ]);
        $BnStatus = json_decode($APIStatus->getBody()->getContents(), true);
        return $BnStatus;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $user = auth()->user();
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $cards = array(
                $request->number
            );
            foreach ($cards as $c) {
                $check = $this->check_cc($c, true);
                if ($check !== false) {
                    $TypeCard = $check;
                } else {
                    echo "$c - Not a match";
                }
            }
            switch ($TypeCard) {
                case "Visa":
                    $CardType = '001';
                    $NameCard = "Visa";
                    break;
                case "Mastercard":
                    $CardType = '002';
                    $NameCard = "Mastercard";
                    break;
                case "American Express":
                    $CardType = '003';
                    $NameCard = "";
                    break;
            }
            $CardBn = new Client();
            $CardCreationResult = $CardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => $user->password,
                    'cardDescription' => $NameCard,
                    'primaryAccountNumber' => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => $request->cvc
                ],
                'verify' => false,
            ]);
            $Card = json_decode($CardCreationResult->getBody()->getContents(), true);
            if($Card['apiStatus'] == 'Success') {
                $last_4digits = substr($request->number, -4);
                $token_bn = $Card['cardTokenId'];
                $paymentMethod = PaymentMethod::create([
                    'user_id' => $user->id,
                    'name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'last_4digits' => $last_4digits,
                    'nameCard' => $NameCard,
                    'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                    'token_cybersource' => '',
                    'token_bn' => $token_bn
                ]);
                return redirect('payments');
            }else{
                return redirect()->back()->withErrors('No se aprobó esta tarjeta');
            }
        }else{
            return redirect()->back()->withErrors('Pagos en línea está fuera de servicio en este momento. No se pudo gestionar la transacción');
        }
    }
    /*
    *
    *
    *
    *
    */
    public function indexData(){
        $user = auth()->user();
        $query = PaymentMethod::where('user_id', $user->id);
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($payment_method) {
                return view('payment.actions', [
                    'data' => $payment_method
                ])->render();
            })
            ->editColumn('last_4digits', function(PaymentMethod $payment_method) {
                if($payment_method->default_card == 1){
                    $text_default = ' Por defecto';
                }else{
                    $text_default = '';
                }
                return 'Termina en ...' . $payment_method->last_4digits . ' ' . $text_default;
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

    public function check_cc($cc, $extra_check = false){
        $cards = array(
            "visa" => "(4\d{12}(?:\d{3})?)",
            "amex" => "(3[47]\d{13})",
            "jcb" => "(35[2-8][89]\d\d\d{10})",
            "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
            "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
            "mastercard" => "(5[1-5]\d{14})",
            "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
        );
        $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
        $matches = array();
        $pattern = "#^(?:".implode("|", $cards).")$#";
        $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
        /*if($extra_check && $result > 0){
            $result = (validatecard($cc))?1:0;
        }*/
        return ($result>0)?$names[sizeof($matches)-2]:false;
    }

    public function paymentCheckout(Request $request){
        $planSelected = $request->selectedPlan;
        $sale = getCurrentSubscription();
        return view('payment/paymentCard')->with('planSelected', $planSelected)
                                               ->with('sale', $sale);
    }

    public function paymentCard(Request $request){
        $user = auth()->user();
        $current_company = currentCompany();
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
        $planSelected = $request->planSelected;
        $subscription_plan = SubscriptionPlan::findOrFail($request->planId);
        switch ($planSelected) {
            case 1:
                $costo = $subscription_plan->monthly_price;
                $next_payment_date = $start_date->addMonths(1);
                $numberOfPayments = 1;
                break;
            case 2:
                $costo = $subscription_plan->six_price * 6;
                $next_payment_date = $start_date->addMonths(6);
                $numberOfPayments = 6;
                break;
            case 3:
                $costo = $subscription_plan->annual_price * 12;
                $next_payment_date = $start_date->addMonths(12);
                $numberOfPayments = 12;
                break;
        }
        $montoDescontado = $costo * $descuento;
        $subtotal = ($costo - $montoDescontado);
        $iv = $subtotal * 0.13;
        $amount = $subtotal + $iv;
        $cards = array(
            $request->number
        );
        foreach ($cards as $c) {
            $check = $this->check_cc($c, true);
            if ($check !== false) {
                $TypeCard = $check;
            }
        }
        switch ($TypeCard) {
            case "Visa":
                $CardType = '001';
                $NameCard = "Visa";
                break;
            case "Mastercard":
                $CardType = '002';
                $NameCard = "Mastercard";
                break;
            case "American Express":
                $CardType = '003';
                $NameCard = "";
                break;
        }
        $sale = Sale::where('user_id', $user->id);
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'payment_date' => $date,
            'payment_status' => 1,
            'amount' => $amount
        ]);
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $CardBn = new Client();
            $CardCreationResult = $CardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => $user->password,
                    'cardDescription' => $NameCard,
                    'primaryAccountNumber' => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => $request->cvc
                ],
                'verify' => false,
            ]);
            $Card = json_decode($CardCreationResult->getBody()->getContents(), true);
            if($Card['apiStatus'] == 'Success') {
                $last_4digits = substr($request->number, -4);
                $token_bn = $Card['cardTokenId'];
                $paymentMethod = PaymentMethod::create([
                    'user_id' => $user->id,
                    'name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'last_4digits' => $last_4digits,
                    'nameCard' => $NameCard,
                    'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                    'token_bn' => $token_bn,
                    'default' => 1
                ]);
                $payment->proof = $token_bn;
                $payment->payment_status = 1;
                $payment->save();

                $data = new stdClass();
                $data->description = 'Pago Suscripcion Etax';
                $data->amount = $amount;
                $data->user_name = $sale->user->username;
                $data->cardTokenId = $token_bn;
                $PaymentCard = $this->paymentCharge($data);
                if ($PaymentCard['apiStatus'] == "Successful") {
                    $sub = Sale::updateOrCreate (

                        [
                            'user_id' => $user->id
                        ],
                        [
                            'status'  => 1,
                            'etax_product_id' => '',
                            'recurrency' => $numberOfPayments
                        ]

                    );
                    //enviar factura electronica
                    return redirect('wizard');
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


    public function paymentTokenUpdateView($id){
        $subscription = getCurrentSubscription();
        $Payment = PaymentMethod::find($id);
        return view('payment/updatePaymentMethods')->with('payment', $Payment)
                                                        ->with('Id', $id);
    }
    /*
    *
    *
    *
    */
    public function paymentTokenUpdate(Request $request){
        $cards = array(
            $request->number
        );
        foreach($cards as $c){
            $check = $this->check_cc($c, true);
            if($check!==false){
                $TypeCard = $check;
            }else{
                echo "$c - Not a match";
            }
        }
        switch ($TypeCard){
            case "Visa":
                $CardType = '001';
                break;
            case "Mastercard":
                $CardType = '002';
                break;
            case "American Express":
                $CardType = '003';
                break;
        }
        $user = auth()->user();
        $PaymentMethod = PaymentMethod::find($request->Id);
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $CardBn = new Client();
            $CardCreationResult = $CardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserUpdateCard?applicationName=string&userName=string&userPassword=string&cardTokenId=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => $user->password,
                    'cardTokenId' => $PaymentMethod->token_bn,
                    "cardDescription" => $PaymentMethod->card_name,
                    "primaryAccountNumber" => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => $request->cvc
                ],
                'verify' => false,
            ]);
            $Card = json_decode($CardCreationResult->getBody()->getContents(), true);
            $last_4digits = substr($request->number, -4);
            $due_date = $request->cardMonth . ' ' . $request->cardYear;
            $PaymentMethod->last_4digits = $last_4digits;
            $PaymentMethod->due_date = $due_date;
            $PaymentMethod->nameCard = $TypeCard;
            $PaymentMethod->updated_by = $user->id;
            $PaymentMethod->save();
            return redirect('payments');
        }else{
            $mensaje = 'Transacción no disponible en este momento';
            return redirect()->back()->withError($mensaje);
        }
    }

    public function paymentTokenDelete($Id){
        $user = auth()->user();
        $PaymentMethod = PaymentMethod::find($Id);
        $this->authorize('update', $PaymentMethod);
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $CardBn = new Client();
            $CardCreationResult = $CardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserDeleteCard", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX',
                    'userName' => $user->user_name,
                    'userPassword' => $user->password,
                    'cardTokenId' => $PaymentMethod->token_bn
                ],
                'verify' => false,
            ]);
            $Card = json_decode($CardCreationResult->getBody()->getContents(), true);
            if($Card['apiStatus'] == 'sucess') {
                $PaymentMethod->updated_by = $user->id;
                $PaymentMethod->save();
                $PaymentMethod->delete();
                return redirect('payments')->withMessage('Método de pago eliminado:');
            }else{
                $mensaje = 'No se pudo eliminar el método de pago: ' . $Card['apiStatus'];
                return redirect()->back()->withError($mensaje);
            }
        }else{
            $mensaje = 'Transacción no disponible en este momento';
            return redirect()->back()->withError($mensaje);
        }
    }
    /**
    *Parametros : description, user_name, amount, cardTokenId
    *
    *
    *
    */
    public function paymentCharge(Request $request){
        $AppCharge = new Client();
        $AppChargeBn = $AppCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'applicationPassword' => 'ETFTTJUN1019%',
                'chargeDescription' => $request->description,
                'userName' => $request->user_name,
                "transactionCurrency" => "USD",
                "transactionAmount" => $request->amount
            ],
            'verify' => false,
        ]);
        $ChargeAplied = json_decode($AppChargeBn->getBody()->getContents(), true);
        $chargeTokenId = $ChargeAplied['chargeTokenId'];
        /****************************************************/
        $BnCharge = new Client();
        $ChargeBn = $BnCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppApplyCharge?applicationName=string&applicationPassword=string&userName=string&chargeTokeId=string&cardTokenId=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'applicationPassword' => 'ETFTTJUN1019%',
                'userName' => $request->user_name,
                'chargeTokenId' => $chargeTokenId,
                "cardTokenId" => $request->cardTokenId
            ],
            'verify' => false,
        ]);
        $Charge = json_decode($ChargeBn->getBody()->getContents(), true);
        return $Charge;
    }
    /**
    *
    *
    *
    *
    */
    public function comprarProductos(Request $request){
        //recibe parametros: etax_product_id, amount, description
        $date = Carbon::parse(now('America/Costa_Rica'));
        $current_company = currentCompany();
        $user_id = auth()->user()->id;
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
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
    /**
    *
    *
    *
    *
    */
    public function dailySubscriptionsPayment(){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful') {
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
    /*
    *
    *
    *
    *
    */
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
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request){
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
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
