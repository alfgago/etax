<?php

namespace App\Utils;


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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Utils\BridgeHaciendaApi;

class PaymentUtils
{
    public function statusBNAPI(){
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
        /*if($extra_check && $result > 0){
            $result = (validatecard($cc))?1:0;
        }*/
        return ($result>0) ? $names[ sizeof($matches)-2 ] : false;
    }
    
    public function userCardInclusion($number, $cardDescripcion, $cardMonth, $cardYear, $cvc){
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
                'cardDescription' => $cardDescripcion,
                'primaryAccountNumber' => $number,
                "expirationMonth" => (int)$cardMonth,
                "expirationYear" => (int)'20'.$cardYear,
                "verificationValue" => (int)$cvc
            ],
            'verify' => false,
        ]);
        $card = json_decode($cardCreationResult->getBody()->getContents(), true);
        return $card;
    }

    public function userCardsInfo(){
        $user = auth()->user();
        $cardBn = new Client();
        $cardCreationResult = $cardBn->request('POST', "https://emcom.oneklap.com:2263/api/UserRequestCards?applicationName=string&userName=string&userPassword=string", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => ['applicationName' => 'ETAX',
                'userName' => $user->user_name,
                'userPassword' => 'Etax-' . $user->id . 'Klap'
            ],
            'verify' => false,
        ]);
        $cards = json_decode($cardCreationResult->getBody()->getContents(), true);
        return $cards;
    }
    
    
    public function paymentIncludeCharge($request){
        $appCharge = new Client();
        $appChargeBn = $appCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppIncludeCharge?applicationName=string&applicationPassword=string&chargeDescription=string&userName=string&transactionCurrency=string&transactionAmount=double", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password'),
                'chargeDescription' => $request->description,
                'userName' => $request->user_name,
                "transactionCurrency" => "USD",
                "transactionAmount" => $request->amount
            ],
            'verify' => false,
        ]);
        $chargeIncluded = json_decode($appChargeBn->getBody()->getContents(), true);
        return $chargeIncluded;
    }
    
    
    public function paymentApplyCharge($request){
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppApplyCharge?applicationName=string&applicationPassword=string&userName=string&chargeTokeId=string&cardTokenId=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password'),
                'userName' => $request->user_name,
                'chargeTokenId' => $request->chargeTokenId,
                "cardTokenId" => $request->cardTokenId
            ],
            'verify' => false,
        ]);
        $charge = json_decode($chargeBn->getBody()->getContents(), true);
        return $charge;
    }

    public function deleteKlapCharge($request){
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/AppDeleteCharge?applicationName=string&applicationPassword=string&userName=string&chargeTokenId=string", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'applicationPassword' => config('etax.klap_app_password'),
                'userName' => $request->user_name,
                'chargeTokenId' => $request->chargeTokenId,
            ],
            'verify' => false,
        ]);
        $chargeDeleted = json_decode($chargeBn->getBody()->getContents(), true);
        return $chargeDeleted;
    }

    public function deleteKlapUserCard($request){
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/UserDeleteCard", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $request->user_name,
                'userPassword' => $request->userPassword,
                'cardTokenId' => $request->userEmail,
            ],
            'verify' => false,
        ]);
        $cardDeleted = json_decode($chargeBn->getBody()->getContents(), true);
        return $cardDeleted;
    }

    public function deleteKlapUser($request){
        $bnCharge = new Client();
        $chargeBn = $bnCharge->request('POST', "https://emcom.oneklap.com:2263/api/UserDeleteProfile", [
            'headers' => [
                'Content-Type' => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $request->user_name,
                'userPassword' => $request->userPassword,
                'userEmail' => $request->userEmail,
            ],
            'verify' => false,
        ]);
        $userDeleted = json_decode($chargeBn->getBody()->getContents(), true);
        return $userDeleted;
    }
}
