<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
use App\Invoice;
use App\InvoiceItem;
use App\RecurringInvoice;
use App\Company;

class EnvioProgramadas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::info("Inicio job de envio de programadas");
        $this->crearRecurrentes();
        $this->enviar();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }


    private function crearRecurrentes(){
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $today = $start_date->year."-".$start_date->month."-".$start_date->day." 23:59:59";
        //dd($today);
        $recurrentes = RecurringInvoice::where('next_send', '<=',$today)->with('invoice')->with('invoice.items')->get();
        foreach ($recurrentes as $recurrente) {
            try{
                $invoice_old = $recurrente->invoice;
                $invoice = new Invoice();
                $invoice->company_id = $invoice_old->company_id;
                $invoice->client_id = $invoice_old->client_id;
                $invoice->document_type = $invoice_old->document_type;
                $invoice->document_key = 'key programada';
                $invoice->reference_number = $invoice_old->reference_number;
                $invoice->document_number = 'Programada';
                $invoice->subtotal = $invoice_old->subtotal;
                $invoice->iva_amount = $invoice_old->iva_amount;
                $invoice->total = $invoice_old->total;
                $invoice->currency = $invoice_old->currency;
                $invoice->currency_rate = $invoice_old->currency_rate;
                $invoice->sale_condition = $invoice_old->sale_condition;
                $invoice->credit_time = $invoice_old->credit_time;
                $invoice->payment_type = $invoice_old->payment_type;
                $invoice->retention_percent = $invoice_old->retention_percent;
                $invoice->buy_order = $invoice_old->buy_order;
                $invoice->send_emails = $invoice_old->send_emails;
                $invoice->description = $invoice_old->description;
                $invoice->hacienda_status = 99;
                $invoice->payment_status = $invoice_old->payment_status;
                $invoice->payment_receipt = $invoice_old->payment_receipt;
                $invoice->generated_date = $recurrente->next_send;
                $invoice->due_date = $recurrente->proximoVencimiento();
                $invoice->is_void = $invoice_old->is_void;
                $invoice->is_totales = $invoice_old->is_totales;
                $invoice->is_authorized = $invoice_old->is_authorized;
                $invoice->is_code_validated = $invoice_old->is_code_validated;
                $invoice->status = $invoice_old->status;
                $invoice->generation_method = $invoice_old->generation_method; 
                $invoice->other_reference = $invoice_old->other_reference; 
                $invoice->other_document = $invoice_old->other_document; 
                $invoice->client_first_name = $invoice_old->client_first_name; 
                $invoice->client_last_name = $invoice_old->client_last_name; 
                $invoice->client_last_name2 = $invoice_old->client_last_name2; 
                $invoice->client_email = $invoice_old->client_email; 
                $invoice->client_address = $invoice_old->client_address; 
                $invoice->client_country = $invoice_old->client_country; 
                $invoice->client_state = $invoice_old->client_state; 
                $invoice->client_city = $invoice_old->client_city; 
                $invoice->client_district = $invoice_old->client_district; 
                $invoice->client_phone = $invoice_old->client_phone; 
                $invoice->client_zip = $invoice_old->client_zip; 
                $invoice->client_id_number = $invoice_old->client_id_number; 
                $invoice->reference_document_key = $invoice_old->reference_document_key; 
                $invoice->reference_generated_date = $invoice_old->reference_generated_date ; 
                $invoice->commercial_activity  = $invoice_old->commercial_activity ; 
                $invoice->xml_schema  = $invoice_old->xml_schema ; 
                $invoice->foreign_address  = $invoice_old->foreign_address ; 
                $invoice->reference_doc_type  = $invoice_old->reference_doc_type ; 
                $invoice->provider_id = $invoice_old->provider_id;
                $invoice->code_note = $invoice_old->code_note;
                $invoice->reason = $invoice_old->reason;
                $invoice->client_id_type = $invoice_old->client_id_type;
                $invoice->total_serv_exonerados = $invoice_old->total_serv_exonerados;
                $invoice->total_merc_exonerados = $invoice_old->total_merc_exonerados;
                $invoice->total_exonerados = $invoice_old->total_exonerados;
                $invoice->reference_id = $invoice_old->reference_id;
                $invoice->recurring_id = $invoice_old->recurring_id;
                $invoice->month = $start_date->month;
                $invoice->year = $start_date->year;
                $invoice->save();
                foreach ($invoice_old->items as $item_old) {
                    $item = new InvoiceItem();
                    $item->invoice_id = $invoice->id;
                    $item->month = $start_date->month;
                    $item->year = $start_date->year;
                    $item->company_id = $item_old->company_id;
                    $item->product_id = $item_old->product_id;
                    $item->item_number = $item_old->item_number;
                    $item->code = $item_old->code;
                    $item->name = $item_old->name;
                    $item->product_type = $item_old->product_type;
                    $item->measure_unit = $item_old->measure_unit;
                    $item->item_count = $item_old->item_count;
                    $item->unit_price = $item_old->unit_price;
                    $item->subtotal = $item_old->subtotal;
                    $item->iva_amount = $item_old->iva_amount;
                    $item->total = $item_old->total;
                    $item->discount_type = $item_old->discount_type;
                    $item->discount = $item_old->discount;
                    $item->discount_reason = $item_old->discount_reason;
                    $item->iva_type = $item_old->iva_type;
                    $item->iva_percentage = $item_old->iva_percentage;
                    $item->is_exempt = $item_old->is_exempt;
                    $item->is_identificacion_especifica = $item_old->is_identificacion_especifica;
                    $item->exoneration_document_type = $item_old->exoneration_document_type;
                    $item->exoneration_document_number = $item_old->exoneration_document_number;
                    $item->exoneration_company_name = $item_old->exoneration_company_name;
                    $item->exoneration_porcent = $item_old->exoneration_porcent;
                    $item->exoneration_amount = $item_old->exoneration_amount;
                    $item->impuesto_neto = $item_old->impuesto_neto;
                    $item->exoneration_total_amount = $item_old->exoneration_total_amount;
                    $item->exoneration_date = $item_old->exoneration_date;
                    $item->tariff_heading = $item_old->tariff_heading;
                    $item->exoneration_total_gravado = $item_old->exoneration_total_gravado;
                    $item->save();
                }
                $recurrente->invoice_id = $invoice->id;
                $recurrente->next_send = $recurrente->proximo_envio($start_date,$start_date);
                $recurrente->save();
            }catch(\Exception $e){
                Log::error('Falló al crear recurrente: ' . $e);
            }
        }
    }
    private function enviar(){
        try{
            $start_date = Carbon::parse(now('America/Costa_Rica'));
            $today = $start_date->year."-".$start_date->month."-".$start_date->day." 23:59:59";
            log::info('Facturas enviadas el '.$today);
            $invoices = Invoice::where("hacienda_status",'99')
                                ->where('generated_date', '<=',$today)
                                ->get();
            foreach ($invoices as $invoice) {
                try{
                    $company = Company::find($invoice->company_id);
                    if( strtolower($invoice->document_number) == 'programada') {
                        if ($invoice->document_type == '01') {
                            $invoice->reference_number = $company->last_invoice_ref_number + 1;
                        }
                        if ($invoice->document_type == '08') {
                            $invoice->reference_number = $company->last_invoice_pur_ref_number + 1;
                        }
                        if ($invoice->document_type == '09') {
                            $invoice->reference_number = $company->last_invoice_exp_ref_number + 1;
                        }
                        if ($invoice->document_type == '04') {
                            $invoice->reference_number = $company->last_ticket_ref_number + 1;
                        }
                        if ($invoice->document_type== '02') {
                            $invoice->reference_number = $company->last_debit_note_ref_number + 1;
                        }
                        if ($invoice->document_type == '03') {
                            $invoice->reference_number= $company->last_note_ref_number + 1;
                        }
                        $invoice->document_key = $this->getDocumentKey($invoice->document_type,$company);
                        $invoice->document_number = $this->getDocReference($invoice->document_type,$company);

                        if ($invoice->document_type == '01') {
                             $company->last_invoice_ref_number = $invoice->reference_number;
                        }
                        if ($invoice->document_type == '08') {
                            $company->last_invoice_pur_ref_number = $invoice->reference_number;
                        }
                        if ($invoice->document_type== '02') {
                            $company->last_debit_note_ref_number = $invoice->reference_number;
                        }
                        if ($invoice->document_type == '03') {
                           $company->last_note_ref_number = $invoice->reference_number;
                        }
                        if ($invoice->document_type == '09') {
                            $company->last_invoice_exp_ref_number = $invoice->reference_number;
                        }
                        if ($invoice->document_type == '04') {
                            $company->last_ticket_ref_number = $invoice->reference_number;
                        }
                    }
                    $invoice->hacienda_status = '01';
                    
                    $company->save();
                    $invoice->save();
                    Log::info("Factura  programada enviada de la compañia ".$company->id." con la llave: " . $invoice->document_key);
                }catch( \Throwable $ex ){
                    Log::error("error en envio de programada ".$invoice->id." error: " . $ex);
                }
            }
        }catch( \Throwable $ex ){
            Log::error("error en envio de programada: " . $ex);
        }
    }


    private function getDocReference($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
        if ($docType == '01') {
            $lastSale = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '02') {
            $lastSale = $company->last_debit_note_ref_number + 1;
        }
        if ($docType == '03') {
            $lastSale = $company->last_note_ref_number + 1;
        }
        if ($docType == '04') {
            $lastSale = $company->last_ticket_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
        $invoice = new Invoice();
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '02') {
            $ref = $company->last_debit_note_ref_number + 1;
        }
        if ($docType == '03') {
            $ref = $company->last_note_ref_number + 1;
        }
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType, $company).
            '1'.$invoice->getHashFromRef($ref);

        return $key;
    }

}
