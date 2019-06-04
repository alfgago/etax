<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Subscription;
use Carbon\Carbon;
use CybsSoapClient;
use Illuminate\Http\Request;
use stdClass;

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

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){

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

    public function getUserIpAddr(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
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
        if($extra_check && $result > 0){
            $result = (validatecard($cc))?1:0;
        }
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
        if (isset($request->coupon)) {
            $cuponConsultado = Coupon::where('code', $request->coupon)
                ->where('used', 0);
            if (isset($cuponConsultado)) {
                $descuento = ($cuponConsultado->discount_percentage) / 100;
            } else {
                $descuento = 0;
            }
        }
        $planSelected = $request->planSelected;
        switch ($planSelected) {
            case 1:
                $costo = 11.99;
                $next_payment_date = $start_date->addMonths(1);
                $frequency = 'monthly';
                $numberOfPayments = 1;
                break;
            case 2:
                $costo = 71.94;
                $next_payment_date = $start_date->addMonths(6);
                $frequency = 'biannual';
                $numberOfPayments = 6;
                break;
            case 3:
                $costo = 143.88;
                $next_payment_date = $start_date->addMonths(12);
                $frequency = 'annual';
                $numberOfPayments = 12;
                break;
        }
        $montoDescontado = $costo * $descuento;
        $subtotal = ($costo - $montoDescontado);
        $iv = $subtotal * 0.13;
        $amount = $subtotal + $iv;
        $IP = $this->getUserIpAddr();
        $cards = array(
            $request->number
        );
        foreach($cards as $c){
            $check = check_cc($c, true);
            if($check!==false){
                $TypeCard = $check;
            }else{
                echo "$c - Not a match";
            }
        }
        switch ($TypeCard){
            case "Visa":
                $CardType = 001;
                break;
            case "Mastercard":
                $CardType = 002;
                break;
            case "American Express":
                $CardType = 003;
                break;
        }
        /**************************************************************/
        // Before using this example, you can use your own reference code for the transaction.
        $referenceCode = 'your_merchant_reference_code';

        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($referenceCode);

        // This section contains a sample transaction request for creating a subscription

        $paySubscriptionCreateService = new stdClass();
        $paySubscriptionCreateService->run = 'true';
        $requestClient->paySubscriptionCreateService = $paySubscriptionCreateService;

        $billTo = new stdClass();
        $billTo->firstName = $request->first-name;
        $billTo->lastName = $request->last-name;
        $billTo->street1 = $request->street1;
        $billTo->city = $request->city;
        $billTo->state = $request->state;
        $billTo->postalCode = $request->postalCode;
        $billTo->country = $request->country;
        $billTo->email = $request->email;
        $billTo->ipAddress = $IP;
        $requestClient->billTo = $billTo;

        $card = new stdClass();
        $card->accountNumber = $request->number;
        $card->expirationMonth = $request->cardMonth;
        $card->expirationYear = $request->cardYear;
        $card->cardType= $CardType;
        $requestClient->card = $card;

        $purchaseTotals = new stdClass();
        $purchaseTotals->currency = 'USD';
        $requestClient->purchaseTotals = $purchaseTotals;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->frequency = $frequency;
        $recurringSubscriptionInfo->amount = $amount;
        $recurringSubscriptionInfo->automaticRenew = 'true';
        $recurringSubscriptionInfo->numberOfPayments = $numberOfPayments;
        $recurringSubscriptionInfo->startDate = $start_date;

        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $reply = $client->runTransaction($requestClient);

        /**************************************************************/
        if($reply->decision == 'ACCEPT'){
            $token_cybersource = $reply->requestToken;
            $last_4digits = substr($request->cardNumber, -4);
            $paymentMethod = PaymentMethodController::create([
                'user_id' => $user->id,
                'name' => $request->firstName,
                'last_name' => $request->lastName,
                'last_4digits' =>  $last_4digits,
                'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                'token_cybersource' => $token_cybersource
            ]);
            $payment = Payment::create([
                'subscription_id' => $request->subscriptionId,
                'payment_date' => $start_date,
                'payment_status' => 2,
                'next_payment_date' => $next_payment_date,
                'amount' => $amount,
                'proof' => $reply->ccAuthReply_reconciliationID

            ]);
            $sub = Subscription::updateOrCreate (
                [
                    'user_id' => $user->id
                ],
                [
                    'status'  => 1,
                    'trial_end_date' => $start_date,
                    'start_date' => $start_date,
                    'next_payment_date' => $next_payment_date,
                ]
            );
            return view('Wizard.index');
        }else{
            if($reply->decision == 'ERROR'){
                $mensaje = 'Hubo un error en la transaccion';
            }else if ($reply->decision == 'REJECT'){
                $mensaje = 'El pago fue denegado';
            }
            return view('payment/create')->withMessage($mensaje);
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
