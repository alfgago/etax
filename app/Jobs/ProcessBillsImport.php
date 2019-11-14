<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Bill;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
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

class ProcessBillsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billList = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($billList)
    {
        $this->billList = $billList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
                
        Log::info("Agregando facturas a queue");
        foreach($this->billList as $fac){
            ProcessSingleBillImport::dispatch($fac)->onQueue('imports');
        }
        Log::info(count($this->billList)." facturas importadas por excel");
    }

}
