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

    private $invoiceId = '';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        $this->enviarProgramadas();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
    
    private function enviarProgramadas(){
        try{
            $invoice = Invoice::where('id', $this->invoiceId)
                            ->with('company')
                            ->with('items')->firstOrFail();
            $company = $invoice->company;
            
            try{
                $currRate = $invoice->getLiveCurrencyRate();
                if($currRate > 1){
                    $invoice->currency_rate = $currRate;
                }
            }catch(\Exception $e){
                Log::error("error en envio de programada: " . $e->getMessage() );
            }
            
            //if($company->id != '208'){ return false; }
            
            if( strtolower($invoice->document_number) == 'programada') {
                $invoice->document_key = getDocumentKey($invoice->document_type, $company);
                $invoice->document_number = getDocReference($invoice->document_type, $company);

                if ($invoice->document_type == '01') {
                    $invoice->reference_number = $company->last_invoice_ref_number + 1;
                    $company->last_invoice_ref_number = $invoice->reference_number;
                    $company->last_document = $invoice->document_number;
                }
                if ($invoice->document_type == '02') {
                    $invoice->reference_number = $company->last_debit_note_ref_number + 1;
                    $company->last_debit_note_ref_number = $invoice->reference_number;
                }
                if ($invoice->document_type == '03') {
                    $invoice->reference_number= $company->last_note_ref_number + 1;
                    $company->last_note_ref_number = $invoice->reference_number;
                }
                if ($invoice->document_type == '04') {
                    $invoice->reference_number = $company->last_ticket_ref_number + 1;
                    $company->last_ticket_ref_number = $invoice->reference_number;
                }
                if ($invoice->document_type == '08') {
                    $invoice->reference_number = $company->last_invoice_pur_ref_number + 1;
                    $company->last_invoice_pur_ref_number = $invoice->reference_number;
                }
                if ($invoice->document_type == '09') {
                    $invoice->reference_number = $company->last_invoice_exp_ref_number + 1;
                    $company->last_invoice_exp_ref_number = $invoice->reference_number;
                }
            }
            $invoice->hacienda_status = '01';
            
            $company->save();
            $invoice->save();
            Log::info("Factura  programada enviada de la empresa ".$company->id." con la llave: " . $invoice->document_key);
        }catch( \Throwable $ex ){
            Log::error("error en envio de programada: " . $ex);
        }
    }

}
