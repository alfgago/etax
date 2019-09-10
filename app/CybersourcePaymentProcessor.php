<?php

namespace App;

use App\Utils\BridgeHaciendaApi;
use Carbon\Carbon;
use CybsSoapClient;
use GuzzleHttp\Client;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/../vendor/autoload.php';

class CybersourcePaymentProcessor extends PaymentProcessor
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
     *check_cc
     *
     *
     */
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
    /**
     * Payment token creation
     * Params cardNumber, cardDescripcion, expiry, cvc, user_id, user_name
     *
     */
    public function createCardToken($request){
        $cards = array(
            $request->number
        );
        foreach ($cards as $c) {
            $check = $this->checkCC($c, true);
            $typeCard = $check;
        }
        switch ($request->recurrency){
            case 1:
                $frequency = 'monthly';
            break;
            case 6:
                $frequency = 'biannual';
            break;
            case 12:
                $frequency = 'annual';
            break;
        }
        $referenceCode = 5;
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($referenceCode);

        $paySubscriptionCreateService = new stdClass();
        $paySubscriptionCreateService->run = 'true';
        $requestClient->paySubscriptionCreateService = $paySubscriptionCreateService;
        $requestClient->deviceFingerPrintID = $request->deviceFingerPrintID;

        $billTo = new stdClass();
        $billTo->firstName = $request->firstName;
        $billTo->lastName = $request->lastName;
        $billTo->street1 = $request->street1;
        $billTo->city = $request->cardCity;
        $billTo->state = $request->cardState;
        $billTo->postalCode = $request->zip;
        $billTo->country = $request->country;
        $billTo->email = $request->email;
        $billTo->ipAddress = $request->IP;
        $requestClient->billTo = $billTo;

        $card = new stdClass();
        $card->accountNumber = $request->cardNumber;
        $card->expirationMonth = (int)substr($request->expiry, 0, 2);
        $card->expirationYear = (int)'20' . substr($request->expiry, -2);
        $card->cardType= $typeCard;
        $requestClient->card = $card;

        $purchaseTotals = new stdClass();
        $purchaseTotals->currency = 'USD';
        $requestClient->purchaseTotals = $purchaseTotals;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->frequency = $frequency;
        $recurringSubscriptionInfo->amount = $request->amount;
        $recurringSubscriptionInfo->automaticRenew = 'true';
        $recurringSubscriptionInfo->numberOfPayments = $request->recurrency;
        $recurringSubscriptionInfo->startDate = Carbon::parse(now('America/Costa_Rica'));
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $reply = $client->runTransaction($requestClient);
        return $reply;
    }
    /**
     * Payment token delete
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
    public function updateCardToken($request){
        $client = new CybsSoapClient();
        $typeCard = $this->check_cc();
        $requestClient = $client->createRequest($request->referenceCode);

        $paySubscriptionUpdateService = new stdClass();
        $paySubscriptionUpdateService->run = 'true';
        $requestClient->paySubscriptionUpdateService = $paySubscriptionUpdateService;

        $card = new stdClass();
        $card->accountNumber = $request->cardNumber;
        $card->expirationMonth = (int)substr($request->expiry, 0, 2);
        $card->expirationYear = (int)'20' . substr($request->expiry, -2);
        $card->cardType= $typeCard;
        $requestClient->card = $card;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->subscriptionID = $request->subscriptionID;
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        return $client->runTransaction($requestClient);
    }
    /**
     * Payment creation
     * Params saleId, paymentMethodId, amount, description, user_name
     * Requesting an On-Demand Transaction, Payment_Tokenization_SO_API.pdf, page 37
     */
    public function createPayment($request){
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($request->referenceCode);

        $ccAuthService = new stdClass();
        $ccAuthService->run = 'true';
        $requestClient->ccAuthService = $ccAuthService;

        $ccCaptureService = new stdClass();
        $ccCaptureService->run = 'true';
        $requestClient->ccCaptureService = $ccCaptureService;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->subscriptionID = $request->token_bn;
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $purchaseTotals = new stdClass();
        $purchaseTotals->currency = 'USD';
        $purchaseTotals->grandTotalAmount = $request->amount;
        $requestClient->purchaseTotals = $purchaseTotals;

        return $client->runTransaction($requestClient);
    }
    /**
     * Make payment
     * Params referenceCode, deviceFingerPrintID, subscriptionID, Amount
     *
     */
    public function pay($request){
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($request->referenceCode);

        $ccAuthService = new stdClass();
        $ccAuthService->run = 'true';
        $requestClient->ccAuthService = $ccAuthService;

        $ccCaptureService = new stdClass();
        $ccCaptureService->run = 'true';
        $requestClient->CreatePaymentService = $ccCaptureService;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->subscriptionID = $request->token_bn;
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $purchaseTotals = new stdClass();
        $purchaseTotals->currency = 'USD';
        $purchaseTotals->grandTotalAmount = $request->amount;
        $requestClient->purchaseTotals = $purchaseTotals;

        return $client->runTransaction($requestClient);
    }
    /**
     *Buy Products
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
    /**
     *crearFacturaClienteEtax
     *
     *
     */
    public function crearFacturaClienteEtax($invoiceData){
        $apiHacienda = new BridgeHaciendaApi();
        $tokenApi = $apiHacienda->login(false);
        if ($tokenApi !== false) {
            $invoice = new Invoice();
            $company = Company::find(1);
            $invoice->company_id = 1;
            $document_key = $this->getDocumentKey('01', 1);
            $document_number = $this->getDocReference('01', 1);

            //Datos generales y para Hacienda
            $invoice->document_type = "01";
            $invoice->hacienda_status = "01";
            $invoice->payment_status = "01";
            $invoice->payment_receipt = "";
            $invoice->generation_method = "etaxAuto";
            $invoice->reference_number = $company->last_invoice_ref_number + 1;
            $invoice->xml_schema = 43;

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

            $data->commercial_activity = 722003;

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
            $data->send_email = "info@etaxcr.com";
            $data->billing_emails = null;
            $data->buy_order = '';
            $data->due_date =
            $data->other_reference = '';
            $data->currency_rate = get_rates();
            $data->description = 'Factura de Etax.';
            $data->subtotal = $invoiceData->subtotal;
            $data->currency = 'USD';
            $data->total = $invoiceData->amount;
            $data->iva_amount = $invoiceData->iva_amount;
            $data->generated_date = Carbon::parse(now('America/Costa_Rica'))->format('d/m/Y');
            $data->hora = Carbon::parse(now('America/Costa_Rica'))->format('g:i A');
            $data->due_date = Carbon::parse(now('America/Costa_Rica'))->addDays(7)->format('d/m/Y');

            $item = array();

            $item['item_number'] = 1;
            $item['id'] = 0;
            $item['code'] = $invoiceData->items[0]->code;
            $item['name'] = $invoiceData->items[0]->name;
            $item['product_type'] = '17';
            $item['measure_unit'] = 'Sp';
            $item['item_count'] = $invoiceData->items[0]->cantidad;
            $item['unit_price'] = $invoiceData->items[0]->unit_price;
            $item['subtotal'] = $invoiceData->items[0]->subtotal;

            $item['discount_percentage'] = $invoiceData->items[0]->descuento;
            $item['discount_reason'] = $invoiceData->items[0]->discount_reason;
            $item['discount'] = $invoiceData->items[0]->descuento;

            $item['iva_type'] = 'S103';
            $item['iva_percentage'] = 13;
            $item['iva_amount'] = $invoiceData->items[0]->iva_amount;

            $item['total'] = $invoiceData->items[0]->total;
            $item['is_identificacion_especifica'] = 0;
            $item['is_exempt'] = 0;

            $data->items = [ $item ];

            try{
                $invoiceDataSent = $invoice->setInvoiceData($data);

                Log::info('Suscriptor: '. $data->client_id_number . ", Nombre: " . $data->first_name . " " . $data->last_name . " " . $data->last_name2 . ", Plan:" . $invoiceData->items[0]->name );
                if ( !empty($invoiceDataSent) ) {
                    $invoice = $apiHacienda->createInvoice($invoiceDataSent, $tokenApi);
                }
                $company->last_invoice_ref_number = $invoice->reference_number;
                $company->last_document = $invoice->document_number;
                $company->save();
                clearInvoiceCache($invoice);

            }catch(\Throwable $e){
                Log::error('Error al crear factura de compra eTax. ' . $e->getMessage() );
            }

            Log::info( 'Factura de suscripción exitosa.' );
            return true;
        } else {
            return false;
        }
    }
}
