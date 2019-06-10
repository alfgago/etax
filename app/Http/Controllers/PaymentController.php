<?php

namespace App\Http\Controllers;

use App\EtaxProducts;
use App\Payment;
use App\Subscription;
use App\PaymentMethod;
use Carbon\Carbon;
use CybsSoapClient;
use Illuminate\Http\Request;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

require __DIR__ . '/../../../vendor/autoload.php';

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Payment/index');
    }
    public function createView(){
        return view('payment/CreatePaymentMethod');
    }
    public function StatusBnAPI(){
        $BnEcomAPIStatus = new Client();
        $APIStatus = $BnEcomAPIStatus->request('POST', "http://www.fttserver.com:4217/api/LogOnApp?applicationName=string&applicationPassword=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX_TEST',
                'applicationPassword' => 'ETFTTJUN0619%'
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
            $CardCreationResult = $CardBn->request('POST', "http://www.fttserver.com:4217/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX_TEST',
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
                return 'termina en ...' . $payment_method->last_4digits;
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
        $subscription = getCurrentSubscription();
        return view('payment/create')->with('subscription', $subscription);
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
        $planSelected = $request->paymentAmount;
        $subscription = getCurrentSubscription();
        return view('payment/paymentCard')->with('planSelected', $planSelected)
                                               ->with('subscription', $subscription);
    }

    public function paymentCard(Request $request){
        $user = auth()->user();
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $date = Carbon::now()->format('Y/m/d');
        if (isset($request->coupon)) {
            $cuponConsultado = Coupon::where('code', $request->coupon)
                ->where('used', 0);
            if (isset($cuponConsultado)) {
                $descuento = ($cuponConsultado->discount_percentage) / 100;
            } else {
                $descuento = 0;
            }
        } else {
            $descuento = 0;
        }
        $planSelected = $request->planSelected;
        switch ($planSelected) {
            case 1:
                $costo = 11.99;
                $next_payment_date = $start_date->addMonths(1);
                $numberOfPayments = 1;
                break;
            case 2:
                $costo = 71.94;
                $next_payment_date = $start_date->addMonths(6);
                $numberOfPayments = 6;
                break;
            case 3:
                $costo = 143.88;
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
        $payment = Payment::create([
            'subscription_id' => $request->subscriptionId,
            'payment_date' => $start_date,
            'payment_status' => 1,
            'amount' => $amount
        ]);
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $CardBn = new Client();
            $CardCreationResult = $CardBn->request('POST', "http://www.fttserver.com:4217/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX_TEST',
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
                $Token = $token_bn;
                $token_cybersource = '';
                $paymentMethod = PaymentMethod::create([
                    'user_id' => $user->id,
                    'name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'last_4digits' => $last_4digits,
                    'nameCard' => $NameCard,
                    'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                    'token_cybersource' => $token_cybersource,
                    'token_bn' => $token_bn
                ]);
                $payment->proof = $Token;
                $payment->payment_status = 1;
                $payment->save();

                $BnPayment = new Client();
                $BnPaymentCard = $BnPayment->request('POST', "http://www.fttserver.com:4217/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
                    'headers' => [
                        'Content-Type' => "application/json",
                    ],
                    'json' => ['applicationName' => 'ETAX_TEST',
                        'applicationPassword' => 'ETFTTJUN0619%',
                        "chargeDescription" => 'Compra de Plan ' . $planSelected,
                        "userName" => $user->user_name,
                        "transactionCurrency" => "USD",
                        "transactionAmount" => $amount
                    ],
                    'verify' => false,
                ]);
                $PaymentCard = json_decode($BnPaymentCard->getBody()->getContents(), true);
                if ($PaymentCard['apiStatus'] == "Successful") {
                    $subscription = Subscription::find($request->subscriptionId);
                    $subscription->status = 1;
                    $subscription->next_payment_date = $next_payment_date;
                    $subscription->save();
                    return redirect('wizard');
                } else {
                    $mensaje = 'El pago ha sido denegado';
                    return redirect()->back()->withError($mensaje);
                }
            }else{
                $mensaje = 'No se pudo verificar la informacion de esta tarjeta. Dirijase a Configuraciones->Gestion de Pagos- para agregar una tarjeta';
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
            $CardCreationResult = $CardBn->request('POST', "http://www.fttserver.com:4217/api/UserUpdateCard?applicationName=string&userName=string&userPassword=string&cardTokenId=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX_TEST',
                    'userName' => $user->user_name,
                    'userPassword' => $user->password,
                    'cardTokenId' => $PaymentMethod->token_bn,
                    "cardDescription" => $PaymentMethod->card_name,
                    "primaryAccountNumber" => $request->number,
                    "expirationMonth" => $request->cardMonth,
                    "expirationYear" => '20' . $request->cardYear,
                    "verificationValue" => "444"
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
            $CardCreationResult = $CardBn->request('POST', "http://www.fttserver.com:4217/api/UserDeleteCard", [
                'headers' => [
                    'Content-Type'  => "application/json",
                ],
                'json' => ['applicationName' => 'ETAX_TEST',
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
    *
    *
    *
    *
    */
    public function paymentCharge(Request $request){
        $user = auth()->user();
        $BnCharge = new Client();
        $ChargeBn = $BnCharge->request('POST', "http://www.fttserver.com:4217/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX_TEST',
                'applicationPassword' => 'ETFTTJUN0619%',
                'chargeDescription' => $request->description,
                'userName' => $user->user_name,
                "transactionCurrency" => "USD",
                "transactionAmount" => $request->amount
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
        $date = Carbon::parse(now('America/Costa_Rica'));
        $current_company = currentCompany();
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful'){
            $sale = Sale::create([
                "company_id" => $current_company,
                "etax_product_id" => $request->id,
                "status" => 3,
                "recurrency" => 0
            ]);
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'payment_date' => $date,
                'payment_status' => 1,
                'amount' => $request->amount
            ]);
            $Payment = $this->paymentCharge($request);
            if($Payment['apiStatus'] == "Successful"){
                $payment->payment_status = 2;
                $sale->status = 1;
                $payment->save();
                $sale->save();
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
    public function pagarSuscripcion(){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $user = auth()->user();
        $BnStatus = $this->StatusBnAPI();
        if($BnStatus['apiStatus'] == 'Successful') {
            $subscriptionsUnpaid = EtaxProducts::where('isSubscription', 1);
            for($i=0;$i<count($subscriptionsUnpaid);$i++){
                $sale = Sale::updateOrCreate($subscriptionsUnpaid[$i]->id, [
                    "etax_product_id" => $subscriptionsUnpaid[$i]->id,
                    "status" => 3
                ]);
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'payment_date' => $date,
                    'payment_status' => 1,
                    'amount' => $subscriptionsUnpaid[$i]->price
                ]);
                $data = new stdClass();
                $data->description = '';
                $data->amount = $subscriptionsUnpaid[$i]->price;
                $Payment = $this->paymentCharge($data);
                if ($Payment['apiStatus'] == "Successful") {
                    $payment->payment_status = 2;
                    $sale->status = 1;
                    $payment->save();
                    $sale->save();
                    //crear factura electronica
                } else {
                    return redirect()->back()->withError('No se pudo procesar el pago');
                }
            }
        }else{
            $mensaje = 'Transacción no disponible en este momento';
            return redirect()->back()->withError($mensaje);
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
