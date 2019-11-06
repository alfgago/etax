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
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::error("inicio job de envio de programadas");
        $start_date = Carbon::parse(now('America/Costa_Rica'));
            $today = $start_date->year."-".$start_date->month."-".$start_date->day." 23:59:59";
            $invoices = Invoice::where("hacienda_status",'99')->where('generated_date', '<=',$today)->get();
            foreach ($invoices as $invoice) {
                try{
                    $company = Company::where('id',$invoice->company_id)->first();
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
                    $invoice->hacienda_status = '01';
                    $invoice->company->addSentInvoice( $invoice->year, $invoice->month );

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
                    
                    $company->save();
                    $invoice->save();
                    Log::info("Factura  programada enviada de la compañia".$company->id." con la llave" . $invoice->document_key);
                }catch( \Throwable $ex ){
                    Log::error("error en envio de programada ".$invoice->id." error :" . $ex);
                }
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
