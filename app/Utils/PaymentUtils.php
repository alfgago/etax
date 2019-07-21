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
use App\Utils\BridgeHaciendaApi;
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
                    "recurrency" => '0'
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

                $apliedCharge = $this->paymentApplyCharge($charge);
                if($apliedCharge['apiStatus'] == "Successful"){
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

    /*public function facturarProductosEtax($invoiceData){
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

            $data->tipo_persona = $invoiceData->tipo_persona;
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
            $data->due_date = Carbon::now()->addDays(30);
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

            if($invoiceData->items[0]->descuento > 0){
                $discount_reason = 'Cupón de descuento';
                $discount = $invoiceData->items[0]->descuento;
            }else{
                $discount_reason = null;
                $discount = 0;
            }
            $item['discount_percentage'] = $invoiceData->items[0]->descuento;
            $item['discount_reason'] = $discount_reason;
            $item['discount'] = $discount;

            $item['iva_type'] = '103';
            $item['iva_percentage'] = 0;
            $item['iva_amount'] = 0;

            $item['total'] = $invoiceData->subtotal + 0;
            $item['is_identificacion_especifica'] = 0;
            $item['is_exempt'] = 0;

            $data->items = [ $item ];

            try{
                $invoiceDataSent = $invoice->setInvoiceData($data);
                Log::info('Suscriptor: '. $data->client_id_number . ", Nombre: " . $data->first_name . " " . $data->last_name . " " . $data->last_name2 . ", Plan:" . $invoiceData->items[0]->name );
                if ( !empty($invoiceDataSent) ) {
                    $invoice = $apiHacienda->createInvoice($invoiceDataSent, $tokenApi);
                }
            }catch(\Throwable $e){}

            $company->last_invoice_ref_number = $invoice->reference_number;
            $company->last_document = $invoice->document_number;
            $company->save();
            clearInvoiceCache($invoice);

            return true;
        } else {
            return false;
        }
    }*/

    private function getDocReference($docType, $companyId = null) {
        if( $companyId ){
            $company = Company::find($companyId);
        }else{
            $company = currentCompanyModel();
        }
        $lastSale = $company->last_invoice_ref_number + 1;
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType, $companyId = null) {
        if( $companyId ){
            $company = Company::find($companyId);
        }else{
            $company = currentCompanyModel();
        }
        $invoice = new Invoice();
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType, 1).
            '1'.$invoice->getHashFromRef($company->last_invoice_ref_number + 1);

        return $key;
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

    public function userRequestCharges(){
        $user = auth()->user();
        $requestCharges = new Client();
        $userRequestCharges = $requestCharges->request('POST', "http://www.fttserver.com:4217/api/UserRequestCharges?applicationName=string&userName=string&applicationPassword=string", [
            'headers' => [
                'Content-Type'  => "application/json",
            ],
            'json' => [
                'applicationName' => config('etax.klap_app_name'),
                'userName' => $user->user_name,
                'userPassword' => 'Etax-' . $user->id . 'Klap'
            ],
            'verify' => false,
        ]);
        $charges = json_decode($userRequestCharges->getBody()->getContents(), true);
        return $charges;
    }
    
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
            $data->description = 'Factura de Etax';
            $data->subtotal = $invoiceData->subtotal;
            $data->currency = 'USD';
            $data->total = $invoiceData->amount;
            $data->iva_amount = $invoiceData->iva_amount;
            $data->generated_date = Carbon::now()->format('d/m/Y');
            $data->hora = Carbon::now()->format('g:i A');
            $data->due_date = Carbon::now()->addDays(7)->format('d/m/Y');

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
