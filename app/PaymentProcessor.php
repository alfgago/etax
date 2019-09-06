<?php

namespace App;

use App\Utils\BridgeHaciendaApi;
use Carbon\Carbon;
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
    public function createCardToken($number, $cardDescripcion, $cardMonth, $cardYear, $cvc){
        return true;
    }
    /**
    * Payment token creation
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
    public function updateCardToken(){
        return true;
    }
    /**
    * Payment creation
    *
    *
    */
    public function createPayment(){
        return true;
    }
    /**
    * Make payment
    *
    *
    */
    public function pay(){
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
    public function createPaymentMethod(){
        return true;
    }
    /**
    * Payment method update
    *
    *
    */
    public function updatePaymentMethod(){
        return true;
    }
    /**
    * Payment method delete
    *
    *
    */
    public function deletePaymentMethod(){
        return true;
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
    /**
     * getDocumentKey
     *
     *
     */
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
                Log::error('Error al crear factura de compra eTax. ' . $e->getMessage() );
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
}
