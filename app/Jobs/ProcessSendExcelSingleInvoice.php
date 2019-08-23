<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
use App\InvoiceItem;
use App\AvailableInvoices;
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

class ProcessSendExcelSingleInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoiceArr = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoiceArr = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoiceArr = $this->invoiceArr;
        $fac = $invoiceArr['factura'];
        $lineas = $invoiceArr['lineas'];
        try {
            $invoice = Invoice::firstOrNew(
              [
                  'company_id' => $fac->company_id,
                  'document_number' => $fac->document_number,
                  'document_key' => $fac->document_key,
              ], $fac->toArray()
            );
            
            $invoice->hacienda_status = '01';
            $invoice->generation_method = "etax-bulk";
            
            if( !$invoice->id ){
                $available_invoices = AvailableInvoices::where('company_id', $invoice->company_id)
                                      ->where('year', $invoice->year)
                                      ->where('month', $invoice->month)
                                      ->first();
                if( isset($available_invoices) ) {
                  $available_invoices->current_month_sent = $available_invoices->current_month_sent + 1;
                  $available_invoices->save();
                }
                $invoice->save();
            }
            
            $invoice->subtotal = 0;
            $invoice->iva_amount = 0;
            
            foreach( $lineas as $linea ){
                $linea['invoice_id'] = $invoice->id;
                $invoice->subtotal = $invoice->subtotal + $linea['subtotal'];
                $invoice->iva_amount = $invoice->iva_amount + $linea['iva_amount'];
                InvoiceItem::updateOrCreate(
                [
                    'invoice_id' => $linea['invoice_id'],
                    'item_number' => $linea['item_number'],
                ], $linea);
            }
      
            $invoice->save();
            if( $invoice->year == 2018 ) {
             clearLastTaxesCache($invoice->company_id, 2018);
            }
            clearInvoiceCache($invoice);
                
        }catch( \Throwable $ex ){
            Log::error("Error importando lineas de factura " . $ex);
        }
    }

}
