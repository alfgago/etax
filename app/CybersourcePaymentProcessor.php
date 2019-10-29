<?php

namespace App;

use App\Utils\BridgeHaciendaApi;
use Carbon\Carbon;
//use CybsSoapClient;
use GuzzleHttp\Client;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Utils\Cybersource\CybsSoapClient;

class CybersourcePaymentProcessor extends PaymentProcessor
{
    use SoftDeletes;
    
    /**
     * Payment token creation
     * Params cardNumber, cardDescripcion, expiry, cvc, user_id, user_name
     *
     */
    /*public function createCardTokenWithCharge($request){
        $paymentProcessor = new PaymentProcessor();
        $cardData = $paymentProcessor->getCardNameType($request->number);
        $merchantId = 'tc_cr_011007172';
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($request->referenceCode);

        $paySubscriptionCreateService = new stdClass();
        $paySubscriptionCreateService->run = 'true';
        $requestClient->paySubscriptionCreateService = $paySubscriptionCreateService;

        $ccCaptureService = new stdClass();
        $ccCaptureService->run = 'true';
        $requestClient->ccCaptureService = $ccCaptureService;

        $ccAuthService = new stdClass();
        $ccAuthService->run = 'true';
        $requestClient->ccAuthService = $ccAuthService;

        $requestClient->ID = $merchantId;

        $merchantDefinedData = new stdClass();
        $merchantDefinedData->field1 = 'WEB';
        $requestClient->merchantDefinedData = $merchantDefinedData;

        $billTo = new stdClass();
        $billTo->firstName = $request->first_name_card;
        $billTo->lastName = $request->last_name_card;
        $billTo->address1 = $request->address1;
        $billTo->street1 = $request->street1;
        $billTo->city = $request->cardCity;
        $billTo->state = $request->cardState;
        $billTo->postalCode = $request->zip;
        $billTo->country = $request->country;
        $billTo->email = $request->email;
        $billTo->ipAddress = $request->IpAddress;
        $requestClient->billTo = $billTo;

        $card = new stdClass();
        $card->accountNumber = $request->number;
        $card->expirationMonth = (int)substr($request->expiry, 0, 2);
        $card->expirationYear = (int)'20' . substr($request->expiry, -2);
        $card->cardType = $cardData->type;
        $requestClient->card = $card;

        $purchaseTotals = new stdClass();
        $purchaseTotals->currency = 'USD';
        $purchaseTotals->grandTotalAmount = $request->amount;
        $requestClient->purchaseTotals = $purchaseTotals;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->frequency = 'on-demand';
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $requestClient->deviceFingerprintID = $request->deviceFingerPrintID;
        $requestClient->merchantId = $merchantId;

        $reply = $client->runTransaction($requestClient);

        $this->pay($request);

        return $reply;
    }*/
    /**
     * create Token Without Fee
     *
     *
     */
    public function createCardToken($request) {
        try {
            $user = auth()->user();

            $paymentProcessor = new PaymentProcessor();
            $cardData = $paymentProcessor->getCardNameType($request->number);

            $referenceCode = 5;
            $merchantId = 'tc_cr_011007172';
            $client = new CybsSoapClient();
            $requestClient = $client->createRequest($referenceCode);

            $paySubscriptionCreateService = new stdClass();
            $paySubscriptionCreateService->run = 'true';
            $requestClient->paySubscriptionCreateService = $paySubscriptionCreateService;

            $ccAuthService = new stdClass();
            $ccAuthService->run = 'true';
            $requestClient->ccAuthService = $ccAuthService;

            $requestClient->ID = $merchantId;

            $merchantDefinedData = new stdClass();
            $merchantDefinedData->field1 = 'WEB';
            $requestClient->merchantDefinedData = $merchantDefinedData;

            $billTo = new stdClass();
            $billTo->firstName = $request->first_name_card;
            $billTo->lastName = $request->last_name_card;
            $billTo->address1 = $request->address1;
            $billTo->street1 = $request->street1;
            $billTo->city = $request->cardCity;
            $billTo->state = $request->cardState;
            $billTo->postalCode = $request->zip;
            $billTo->country = $request->country;
            $billTo->email = $request->email;
            $billTo->ipAddress = $request->IpAddress;
            $requestClient->billTo = $billTo;

            $card = new stdClass();
            $card->accountNumber = $request->number;
            $card->expirationMonth = (int)substr($request->expiry, 0, 2);
            $card->expirationYear = (int)'20' . substr($request->expiry, -2);
            $card->cardType = $cardData->type;
            $requestClient->card = $card;

            $purchaseTotals = new stdClass();
            $purchaseTotals->currency = 'USD';
            $purchaseTotals->grandTotalAmount = 0;
            $requestClient->purchaseTotals = $purchaseTotals;

            $recurringSubscriptionInfo = new stdClass();
            $recurringSubscriptionInfo->frequency = 'on-demand';
            $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

            $requestClient->deviceFingerprintID = $request->deviceFingerPrintID;
            $requestClient->merchantId = $merchantId;

            $reply = $client->runTransaction($requestClient);

            $last_4digits = substr($request->number, -4);
            if($reply->decision === 'ACCEPT') {
                //Se logró agregar la tarjeta y crear el pago, entonces hay un nuevo payment method y un payment.
                $paymentMethod = PaymentMethod::updateOrCreate([
                    'user_id' => $user->id,
                    'name' => $request->first_name_card,
                    'last_name' => $request->last_name_card,
                    'last_4digits' => $last_4digits,
                    'masked_card' => $this->getMaskedCard($request->number),
                    'due_date' => $request->expiry,
                    'token_bn' => $reply->paySubscriptionCreateReply->subscriptionID,
                    'default_card' => 1,
                    'payment_gateway' => 'cybersource'

                ]);
            } else {
                $paymentMethod = PaymentMethod::where('user_id', $user->id)
                    ->where('last_4digits', $last_4digits)
                    ->first();
                if(!isset($paymentMethod)) {
                    return redirect()->back()->withError("El método de pago no pudo ser validado.")->withInput();
                }
            }

            return $paymentMethod;

        } catch (\Exception $e) {
            Log::error("Error al crear tarjeta Cybersourcer:-->>". $e);
        }
    }
    /**
     * Payment token delete
     * Params cardNumber, cardDescripcion, expiry, cvc, user_id, user_name
     *
     */
    public function deleteCardToken($request){
//        $client = new CybsSoapClient();
//        $referenceCode = null;
//        $merchantId = null;
//        $typeCard = null;
//        $frequency = null;
//        $requestClient = $client->createRequest($referenceCode);
//
//        $paySubscriptionCreateService = new stdClass();
//        $paySubscriptionCreateService->run = 'true';
//        $requestClient->paySubscriptionCreateService = $paySubscriptionCreateService;
//        $requestClient->deviceFingerPrintID = $request->deviceFingerPrintID ?? '';
//        $requestClient->merchantID = $merchantId;
//
//        $billTo = new stdClass();
//        $billTo->firstName = $request->firstName;
//        $billTo->lastName = $request->lastName;
//        $billTo->street1 = $request->street1;
//        $billTo->city = $request->cardCity;
//        $billTo->state = $request->cardState;
//        $billTo->postalCode = $request->zip;
//        $billTo->country = $request->country;
//        $billTo->email = $request->email;
//        $billTo->ipAddress = $request->IP;
//        $requestClient->billTo = $billTo;
//
//        $card = new stdClass();
//        $card->accountNumber = $request->cardNumber;
//        $card->expirationMonth = (int)substr($request->expiry, 0, 2);
//        $card->expirationYear = (int)'20' . substr($request->expiry, -2);
//        $card->cardType= $typeCard;
//        $requestClient->card = $card;
//
//        $purchaseTotals = new stdClass();
//        $purchaseTotals->currency = 'USD';
//        $requestClient->purchaseTotals = $purchaseTotals;
//
//        $recurringSubscriptionInfo = new stdClass();
//        $recurringSubscriptionInfo->frequency = $frequency;
//        $recurringSubscriptionInfo->amount = $request->amount;
//        $recurringSubscriptionInfo->automaticRenew = 'true';
//        $recurringSubscriptionInfo->numberOfPayments = $request->recurrency;
//        $recurringSubscriptionInfo->startDate = Carbon::parse(now('America/Costa_Rica'));
//        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;
//
//        $reply = $client->runTransaction($requestClient);
        return  true;
    }
    /**
     * Payment token update
     * Params: user_name, user_id, token_bn, cardDescription, cardNumber, cardMonth, cardYear, cvc
     *
     */
    public function updateCardToken($request){
        $paymentProcessor = new PaymentProcessor();
        $cardData = $paymentProcessor->getCardNameType($request->number);
        $referenceCode = $request->user_id;
        $merchantId = 'tc_cr_011007172';
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($referenceCode);

        $paySubscriptionUpdateService = new stdClass();
        $paySubscriptionUpdateService->run = 'true';
        $requestClient->paySubscriptionUpdateService = $paySubscriptionUpdateService;

        $requestClient->ID = $merchantId;

        $merchantDefinedData = new stdClass();
        $merchantDefinedData->field1 = 'WEB';
        $requestClient->merchantDefinedData = $merchantDefinedData;

        $card = new stdClass();
        $card->accountNumber = $request->number;
        $card->expirationMonth = (int)substr($request->expiry, 0, 2);
        $card->expirationYear = (int)'20' . substr($request->expiry, -2);
        $card->cardType = $cardData->type;
        $requestClient->card = $card;

        $recurringSubscriptionInfo = new stdClass();
        $recurringSubscriptionInfo->subscriptionID = $request->token;
        $requestClient->recurringSubscriptionInfo = $recurringSubscriptionInfo;

        $requestClient->deviceFingerprintID = $request->deviceFingerPrintID;
        $requestClient->merchantId = $merchantId;

        $reply = $client->runTransaction($requestClient);

        return $reply;
    }
    
    /**
     * Payment creation
     * Params saleId, paymentMethodId, amount, description, user_name
     * Requesting an On-Demand Transaction, Payment_Tokenization_SO_API.pdf, page 37
     */
    public function createPayment($request){
        return false;
        /*$merchantId = 'tc_cr_011007172';
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($request->referenceCode);
        $requestClient->ID = $merchantId;

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

        $requestClient->merchantId = $merchantId;
        $requestClient->deviceFingerprintID = $request->deviceFingerPrintID;

        return $client->runTransaction($requestClient);*/
    }
    
    /**
     * Make payment
     * Params referenceCode, deviceFingerPrintID, subscriptionID, Amount
     *
     */
    public function pay($request, $isBuy = false) {
        $referenceCode = $request->product_id;
        $merchantId = 'tc_cr_011007172';
        $client = new CybsSoapClient();
        $requestClient = $client->createRequest($referenceCode);

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
        $requestClient->merchantId = $merchantId;

        $appliedCharge = $client->runTransaction($requestClient);
        
        Log::info("Resultado CyberSource: " . json_encode($appliedCharge) );
        
        if($appliedCharge->decision === 'ACCEPT') {
            if ($isBuy) {
               return $appliedCharge;
            }

            return $appliedCharge->requestID;
        }
        return false;
    }
    /**
     *Buy Products
     *
     *
     */
    public function comprarProductos($request) {
        $paymentMethod = PaymentMethod::where('id', $request->payment_method)->first();
        $user = auth()->user();
        $request->request->add(['referenceCode' => $request->referenceCode]);
        $request->request->add(['token_bn' => $paymentMethod->token_bn]);

        $company = currentCompanyModel();
        $date = Carbon::parse(now('America/Costa_Rica'));
        $sale = Sales::updateOrCreate([
            "user_id" => $user->id,
            "company_id" => $company->id,
            "etax_product_id" => $request->product_id,
            "status" => 2,
            "recurrency" => '0',
            "is_subscription" => false
        ]);
        $payment = Payment::updateOrCreate(
            [
                'sale_id' => $sale->id,
                'payment_status' => 1,
            ],
            [
                'payment_method_id' => $paymentMethod->id,
                'payment_date' => $date,
                'amount' => $request->amount,
                'payment_gateway' => 'cybersource'
            ]
        );

        $payment->save();

        $chargeCreated = $this->pay($request, true);
        if($chargeCreated->decision === "ACCEPT") {
            $sale->status = 1;
            $payment->proof = $chargeCreated->requestID;
            $payment->payment_status = 2;
            $sale->save();
            $payment->save();
            return true;
        } else {
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
        $newCardToken = $this->createTokenWithoutFee($cardData);
        if($newCardToken){
            $last_4digits = substr($data->cardNumber, -4);
            $firstDigits = substr($data->number, 0, 6);
            $first4 = substr($firstDigits, 0, 4);
            $last2 = substr($firstDigits, 0, -2);
            $masked_card = $first4 . '-'. $last2 .'**-****-' . $last_4digits;
            $paymentMethod = PaymentMethod::updateOrCreate([
                'user_id' => $user->id,
                'name' => $data->first_name_card,
                'last_name' => $data->last_name_card,
                'last_4digits' => $last_4digits,
                'masked_card' => $masked_card,
                'due_date' => $data->expiry,
                'token_bn' => $newCardToken->paySubscriptionCreateReply->subscriptionID
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
    public function deletePaymentMethod($paymentMethodId) {
        try {
            $paymentMethod = PaymentMethod::where('id', $paymentMethodId)->first();
            $delatedCard = $this->deleteCardToken($paymentMethod->token_bn);
            $paymentMethod->delete();
            return $delatedCard == true;

        } catch ( \Exception $e) {
            Log::error("Error al eliminar tarjeta Cybersource: $paymentMethodId");
        }
    }
    
    public function getChargeProof($chargeIncluded){
        if($chargeIncluded->decision == 'ACCEPT'){
            $appliedCharge_Id = $chargeIncluded->requestID;
        }else{
            return false;
        }
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
