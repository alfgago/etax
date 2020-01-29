<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\SMInvoice;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
use App\Utils\BillUtils;
use App\Jobs\ProcessReception;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CheckSMExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $excelCollection = null;
    private $companyId = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($excelCollection, $companyId)
    {
        $this->excelCollection = $excelCollection;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = Company::find($this->companyId);
        $excelCollection = $this->excelCollection;
        
        sleep (1);
        
        foreach ($excelCollection as $smInvoice){
            try{
                if( $smInvoice->document_type == '03' ){
                    $otherReference = $smInvoice->refer_factura ?? null;
                    if ( isset($otherReference) ) {
                        $nota = $smInvoice->invoice;
                        $ref = Invoice::where('company_id', $company->id)
                          ->where('buy_order', $otherReference)
                          ->first();
                          
                        if( !isset($nota) && isset($smInvoice->document_key) ){
                            $nota = Invoice::where('company_id', $company->id)
                              ->where('document_key', $smInvoice->document_key)
                              ->first();
                            $smInvoice->id = $nota->id;
                            $smInvoice->save();
                        }
                        if( isset($ref) && isset($nota) ) {
                            if( !isset($nota->other_reference) ){
                                  $nota->code_note = '01';
                                  $nota->reason = 'Factura anulada';
                                  $nota->other_reference = $ref->reference_number;
                                  $nota->reference_generated_date = $ref->generated_date;
                                  $nota->reference_document_key = $ref->document_key;
                                  $nota->reference_doc_type = $ref->document_type;
                                  $nota->resend_attempts = 0;
                                  $nota->in_queue = false;
                                  $nota->save();
                                  
                                  $ref->other_reference = $nota->reference_number;
                                  $ref->reason = 'Factura anulada por NC ' . $nota->reference_number;
                                  $ref->save();
                                  Log::info("Ligo la factura: " . $otherReference);
                            }
                        }else{
                          Log::warning("No encuentra referencia: " . $otherReference);
                        }
                        
                        $checkRepetidas = Invoice::where('buy_order', $otherReference)->count();
                        if($checkRepetidas > 1){
                            Log::warning("Factura SM $otherReference se encuentra mas de una vez.");
                        }
                    }
                }
            }catch( \Throwable $ex ){
                Log::error("Error al ligar facturas SM:" . $ex);
            }
        }
    }
    
    
}
