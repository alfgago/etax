<?php

namespace App;

use stdClass;
use Carbon\Carbon;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class PaymentProcessor extends Model
{
    use SoftDeletes;
    /**
    * API status query
    *
    *
    */
    public function statusAPI(){
        return true;
    }
    /**
    * Payment token creation
    *
    *
    */
    public function createCardToken($data){
        return true;
    }
    /**
     *create token without fee
     *
     *
     */
    /*public function createTokenWithoutFee($data){
        return true;
    }*/
    /**
    * checkCC
    *
    *
    */
    public function checkCC($cc, $extra_check = false){
        return true;
    }
    /**
    * Payment token update
    *
    *
    */
    public function updateCardToken($data){
        return true;
    }
    /**
    * Payment creation
    *
    *
    */
    public function createPayment($data){
        return true;
    }
    /**
    * Make payment
    *
    *
    */
    public function pay($data){
        return true;
    }
    /**
    * Payment query
    *
    *
    */
    public function payments(){
        return true;
    }
    /**
    * Payment methods of user
    *
    *
    */
    public function getPaymentMethods(){
        return true;
    }
    /**
    * Payment method creation
    *
    *
    */
    public function createPaymentMethod($data){
        return true;
    }
    /**
    * Payment method update
    *
    *
    */
    public function updatePaymentMethod($data){
        return true;
    }
    /**
    * Payment method delete
    *
    *
    */
    public function deletePaymentMethod($data){
        return true;
    }

    public function getChargeProof($chargeIncluded){

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
            "mastercard" => "(5[1-5][0-9]{14}$)",
            "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
        );
        $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Master Card", "Switch");
        $matches = array();
        $pattern = "#^(?:".implode("|", $cards).")$#";
        $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
        /*if($extra_check && $result > 0){
            $result = (validatecard($cc))?1:0;
        }*/
        return ($result>0)?$names[sizeof($matches)-2]:false;
    }
    
    /**
     * getCardNameType
     */
    public function getCardNameType($number){
        $cards = array(
            $number
        );
        foreach($cards as $c){
            $check = $this->check_cc($c, true);
            $nameCard = $check;
        }
        switch ($nameCard){
            case "Visa":
                $cardType = '001';
            break;
            case 'Master Card':
                $cardType = '002';
            break;
            case "American Express":
                $cardType = '003';
            break;
        }
        $card = new stdClass();
        $card->type = $cardType;
        $card->name = $nameCard;

        return $card;
    }
    /**
     *getMaskedCard
     *
     *
     */
    public function getMaskedCard($number){
        $firstDigits = substr($number, 0, 6);
        $first4 = substr($number, 0, 4);
        $last2 = substr($firstDigits, -2);
        $masked_card = $first4 . '-'. $last2 .'**-****-' . substr($number, -4);

        return $masked_card;
    }
    /**
     * Invoice
     *
     *
     */
    public function sendInvoice(){
        return true;
    }
    /**
     * getDocReference
     *
     *
     */
    public function getDocReference($docType, $companyId = null) {
        if( $companyId ){
            $company = Company::find($companyId);
        }else{
            $company = currentCompanyModel();
        }
        $lastSale = $company->last_invoice_ref_number + 1;
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }
    /**
     * getDocumentKey
     *
     *
     */
    public function getDocumentKey($docType, $companyId = null) {
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
    /**
     *
     *
     *
     */
    public static function selectPaymentGateway($payment_gateway) {

        if ($payment_gateway === null) {
            return false;
        }
        switch ($payment_gateway){
            case 'cybersource':
                $class = new CybersourcePaymentProcessor();
            break;
            case 'klap':
                $class = new KlapPaymentProcessor();
            break;
        }
        return $class;
    }
    /**
     * Create invoice eTax
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
                Log::error('Error al crear factura de compra eTax. ' . $e );
            }

            Log::info( 'Factura de suscripción exitosa.' );
            return true;
        } else {
            return false;
        }
    }
    /**
     * comprarProductos
     *
     *
     */
    public function comprarProductos($request){
        return true;
    }
    /**
     *setInvoiceInfo
     *
     *
     */
    public function setInvoiceInfo($request){
        $invoiceData = new stdClass();
        $invoiceData->client_code = $request->id_number;
        $invoiceData->client_id_number = $request->id_number;
        $invoiceData->client_id = $request->client_id;
        $invoiceData->tipo_persona = $request->tipo_persona;
        $invoiceData->first_name = $request->first_name;
        $invoiceData->last_name = $request->last_name ?? null;
        $invoiceData->last_name2 = $request->last_name2 ?? null;
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
        $invoiceData->amount = $request->amount;
        $invoiceData->subtotal = $request->subtotal;
        $invoiceData->iva_amount = $request->iva_amount;
        $invoiceData->discount_reason = $request->razonDescuento;

        $item = new stdClass();
        $item->code = $request->item_code;
        $item->name = $request->item_name;
        $item->montoDescontado = $request->montoDescontado;
        $item->descuento = $request->montoDescontado;
        $item->discount_reason = $request->razonDescuento;
        $item->cantidad = 1;
        $item->iva_amount = $request->iva_amount;
        $item->unit_price = $request->subtotal;
        $item->subtotal = $request->subtotal;
        $item->total = $request->amount;

        $invoiceData->items = [$item];
        return $invoiceData;
    }
    /**
     *
     *
     *
     */
    function getUserIpAddr(){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)) { $ip = $client; }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) { $ip = $forward; }
        else { $ip = $remote; }
        return $ip;
    }
}
