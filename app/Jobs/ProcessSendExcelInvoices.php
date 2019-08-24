<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Invoice;
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

class ProcessSendExcelInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoiceList = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoiceList)
    {
        $this->invoiceList = $invoiceList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
                
        Log::info("Agregando facturas a queue");
        foreach($this->invoiceList as $fac){
            ProcessSendExcelSingleInvoice::dispatch($fac)->onQueue('imports');
        }
        Log::info(count($this->invoiceList)." facturas importadas por excel");
    }

}
