<?php

namespace App;

use stdClass;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KlapPaymentProcessor extends PaymentProcessor
{
    use SoftDeletes;
    /**
     * API status query
     *
     *
     */
    public function statusAPI(){
        $BnEcomAPIStatus = new Client();
        $APIStatus = $BnEcomAPIStatus->request('POST', "https://emcom.oneklap.com:2263/api/LogOnApp?applicationName=string&applicationPassword=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password')
            ],
            'verify' => false,
        ]);
        $bnStatus = json_decode($APIStatus->getBody()->getContents(), true);
        return $bnStatus;
    }
    /**
     *checkCC
     *
     *
    */
    public function checkCC($cc, $extra_check = false){
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

        return ($result>0) ? $names[ sizeof($matches)-2 ] : false;
    }
    /**
     * Payment token creation
     * Params cardNumber, cardDescripcion, expiry, cvc, user_id, user_name
     *
     */
    public function createCardToken($data){
        $user = auth()->user();
        $cardBn = new Client();
        $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $user->user_name,
                'userPassword' => 'Etax-' . $user->id . 'Klap',
                'cardDescription' => $data->cardDescripcion,
                'primaryAccountNumber' => $data->cardNumber,
                "expirationMonth" => (int)$data->cardMonth,
                "expirationYear" => (int)$data->cardYear,
                "verificationValue" => (int)$data->cvc
            ],
            'verify' => false,
        ]);
        $card = json_decode($cardCreationResult->getBody()->getContents(), true);
        return $card;
    }
    /**
     * Payment token creation
     * Params cardNumber, cardDescripcion, expiry, cvc, user_id, user_name
     *
     */
    public function deleteCardToken($cardTokenId){
        $user = auth()->user();
        $cardBn = new Client();
        $cardDeleted = $cardBn->post('https://emcom.oneklap.com:2263/api/UserDeleteCard', [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'form_params' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $user->user_name,
                'userPassword' => 'Etax-' . $user->id . 'Klap',
                'cardTokenId' => $cardTokenId
            ]
        ]);
        $card = json_decode($cardDeleted->getBody()->getContents(), true);
        if($card['apiStatus'] === 'sucess'){
            return true;
        }else{
            return false;
        }
    }
    /**
     * Payment token update
     * Params: user_name, user_id, token_bn, cardDescription, cardNumber, cardMonth, cardYear, cvc
     *
     */
    public function updateCardToken($data){
        $cardBn = new Client();
        $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserUpdateCard?applicationName=string&userName=string&userPassword=string&cardTokenId=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $data->user_name,
                'userPassword' => 'Etax-' . $data->user_id . 'Klap',
                'cardTokenId' => $data->token,
                'cardDescription' => $data->cardDescription,
                'primaryAccountNumber' => $data->cardNumber,
                "expirationMonth" => (int) $data->cardMonth,
                "expirationYear" =>  (int) $data->cardYear,
                "verificationValue" => (int) $data->cvc
            ],
            'verify' => false,
        ]);
        $card = json_decode($cardCreationResult->getBody()->getContents(), true);
        return $card;
    }
    /**
     * Payment creation
     * Params saleId, paymentMethodId, amount, description, user_name
     *
     */
    public function createPayment($data){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $payment = Payment::updateOrCreate(
            [
                'sale_id' => $data->saleId,
                'payment_status' => 1,
            ],
            [
                'payment_date' => $date,
                'payment_method_id' => $data->paymentMethodId,
                'amount' => $data->amount,
                'proof' => 'pending -' . $data->description
            ]
        );
        $appCharge = new Client();
        $appChargeBn = $appCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password'),
                'chargeDescription' => $data->description,
                'userName' => $data->user_name,
                "transactionCurrency" => "USD",
                "transactionAmount" => $data->amount
            ],
            'verify' => false,
        ]);
        $chargeIncluded = json_decode($appChargeBn->getBody()->getContents(), true);
        if($chargeIncluded['apiStatus'] === "Successful"){
            $payment->charge_token = $chargeIncluded['chargeTokenId'];
            $payment->proof = $chargeIncluded['retrievalRefNo'];
            $payment->save();

            return $payment;
        }else{
            return false;
        }
    }
    /**
    * Make payment
    * Params user_id, chargeTokenId, payment_id
    *
    */
    public function pay($data){
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppApplyCharge?applicationName=string&applicationPassword=string&userName=string&chargeTokeId=string&cardTokenId=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password'),
                'userName' => $data->user_name,
                'chargeTokenId' => $data->chargeTokenId,
                "cardTokenId" => $data->cardTokenId
            ],
            'verify' => false,
        ]);
        $charge = json_decode($chargeBn->getBody()->getContents(), true);

        return $charge['apiStatus'] === "Successful";
    }
    /**
     *comprarProductos
     *
     *
     */
    public function comprarProductos($request, $producto, $amount){
        $bnStatus = $this->statusBNAPI();
        if($bnStatus['apiStatus'] == 'Successful'){
            $date = Carbon::parse(now('America/Costa_Rica'));
            $user = auth()->user();
            $data = new stdClass();
            $data->description = 'Compra de ' . $producto->name . ' eTax';
            $data->user_name = $user->user_name;
            $data->amount = $amount;
            $chargeCreated = $this->paymentIncludeCharge($data);

            if($chargeCreated['apiStatus'] == "Successful"){
                $paymentMethod = PaymentMethod::where('id', $request->payment_method)->first();
                $company = currentCompanyModel();
                $date = Carbon::parse(now('America/Costa_Rica'));
                $sale = Sales::updateOrCreate([
                    "user_id" => $user->id,
                    "company_id" => $company->id,
                    "etax_product_id" => $producto->id,
                    "status" => 2,
                    "recurrency" => false
                ]);
                $payment = Payment::updateOrCreate(
                    [
                        'sale_id' => $sale->id,
                        'payment_status' => 1,
                    ],
                    [
                        'payment_method_id' => $paymentMethod->id,
                        'payment_date' => $date,
                        'amount' => $producto->price
                    ]
                );
                //Si no hay un charge token, significa que no ha sido aplicado. Entonces va y lo aplica
                if( ! isset($payment->charge_token) ) {
                    $chargeIncluded = $this->paymentIncludeCharge($data);
                    $chargeTokenId = $chargeIncluded['chargeTokenId'];
                    $payment->charge_token = $chargeTokenId;
                    $payment->save();
                }
                $chargeTokenId = $chargeCreated['chargeTokenId'];
                $charge = new stdClass();
                $charge->cardTokenId = $paymentMethod->token_bn;
                $charge->user_name = $user->user_name;
                $charge->chargeTokenId = $chargeTokenId;

                $appliedCharge = $this->paymentApplyCharge($charge);
                if($appliedCharge['apiStatus'] == "Successful"){
                    $payment->proof = $appliedCharge['retrievalRefNo'];
                    $payment->payment_status = 2;
                    $payment->save();
                    $sale->status = 1;
                    $sale->save();

                    return true;
                }else{
                    return false;
                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * Payment method creation
     * Params cardNumber, cardDescripcion, expiry, cvc, first_name_card, last_name_card, first
     *
     */
    public function getPaymentMethods(){
        $user = auth()->user();
        $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        return $paymentMethods;
    }
    /**
     * Payment method creation
     * Params cardNumber, cardDescripcion, expiry, cvc, first_name_card, last_name_card, first
     *
     */
    public function createPaymentMethod($request){
        $user = auth()->user();
        $request->number = preg_replace('/\s+/', '',  $request->number);
        $payment_gateway = new CybersourcePaymentProcessor();
        $cards = array(
            $request->number
        );
        foreach ($cards as $c) {
            $check = $payment_gateway->checkCC($c, true);
            $typeCard = $check;
        }
        if(isset($typeCard)){
            switch ($typeCard) {
                case "Visa":
                    $cardType = '001';
                    $nameCard = "Visa";
                    break;
                case false:
                    $cardType = '002';
                    $nameCard = "Mastercard";
                    break;
                case "American Express":
                    $cardType = '003';
                    $nameCard = "American Express";
                    break;
            }
            $cardYear = substr($request->expiry, -2);
            $cardMonth = substr($request->expiry, 0, 2);
            $cardBn = new Client();
            $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserIncludeCard?applicationName=string&userName=string&userPassword=string&cardDescription=string&primaryAccountNumber=string&expirationMonth=int&expirationYear=int&verificationValue=int", [
                'headers' => [
                    'Content-Type' => "application/json",
                ],
                'json' => [
                    'applicationName' => config('etax.klap_app_name'),
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
                $paymentMethod = PaymentMethod::create([
                    'user_id' => $user->id,
                    'name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'last_4digits' => $last_4digits,
                    'masked_card' => $card['maskedCard'],
                    'due_date' => $request->cardMonth . ' ' . $request->cardYear,
                    'token_bn' => $card['cardTokenId']
                ]);
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * Payment method update
     * Params paymentMethodId, token_bn, cardDescription, cardNumber, cardMonth, cardYear, cvc,
     *
     */
    public function updatePaymentMethod($data){
        $user = auth()->user();
        $cardYear = substr($data->expiry, -2);
        $cardMonth = substr($data->expiry, 0, 2);
        $paymentMethod = PaymentMethod::where('id', $data->paymentMethodId)->first();
        $cardData = new stdClass();
        $cardData->user_name = $user->user_name;
        $cardData->user_id = $user->id;
        $cardData->token_bn = $paymentMethod->token_bn;
        $cardData->cardDescription = 'eTax card payment';
        $cardData->cardNumber = $data->cardNumber;
        $cardData->cardMonth = $data->cardMonth;
        $cardData->cardYear = $data->cardYear;
        $cardData->cvc = $data->cvc;
        $updatedCard = $this->updateCardToken($cardData);
        if($updatedCard['apiStatus'] === "Successful"){
            $paymentMethod->due_date = $cardMonth. '/' . $cardYear;
            $paymentMethod->save();
            return true;
        }else{
            return false;
        }
    }
    /**
     * Payment method delete
     *
     *
     */
    public function deletePaymentMethod($paymentMethodId){
        $paymentMethod = PaymentMethod::where('id', $paymentMethodId)->first;
        $delatedCard = $this->deleteCardToken($paymentMethod->token_bn);

        return $delatedCard == true;
    }
}
