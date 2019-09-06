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
                'cardTokenId' => $data->token_bn,
                'cardDescription' => $data->cardDescription,
                'primaryAccountNumber' => $data->cardNumber,
                "expirationMonth" => (int) $data->cardMonth,
                "expirationYear" =>  (int) $data->cardYear,
                "verificationValue" => (int) $data->cvc
            ],
            'verify' => false,
        ]);
        $card = json_decode($cardCreationResult->getBody()->getContents(), true);

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
    * Params user_id, chargeTokenId,
    *
    */
    public function pay($data){
        $paymentMethod = PaymentMethod::where('user_id', $data->user_id)->where('default_card', 1);
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
    public function createPaymentMethod($data){
        $user = auth()->user();
        $cardYear = substr($data->expiry, -2);
        $cardMonth = substr($data->expiry, 0, 2);
        $cardData = new stdClass();
        $cardData->cardNumber = $data->cardNumber;
        $cardData->cardDescripcion = $data->cardDescripcion;
        $cardData->cardYear = $cardYear;
        $cardData->cardMonth = $cardMonth;
        $cardData->cvc = $data->cvc;
        $cardData->user_id = $user->user_id;
        $cardData->user_name = $user->user_name;
        $newCardToken = $this->createCardToken($cardData);
        if($newCardToken){
            $last_4digits = substr($data->cardNumber, -4);
            $paymentMethod = PaymentMethod::updateOrCreate([
                'user_id' => $user->id,
                'name' => $data->first_name_card,
                'last_name' => $data->last_name_card,
                'last_4digits' => $last_4digits,
                'masked_card' => $newCardToken['maskedCard'],
                'due_date' => $cardMonth . '/' .$cardYear,
                'token_bn' => $newCardToken['cardTokenId']
            ]);
            if($data->first == 1){
                $paymentMethod->default_card = 1;
            }else{
                $paymentMethod->default_card = 0;
            }
            $paymentMethod->save();
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
