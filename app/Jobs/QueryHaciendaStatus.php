<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Support\Facades\Log;

class QueryHaciendaStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invoice = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $invoice = $this->invoice;
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
    
            if ($tokenApi !== false) {
                $company = $invoice->company;
                $result = $apiHacienda->queryHacienda($invoice, $tokenApi, $company);
            }
        } catch (\Exception $e) {
            Log::error("Error en job query hacienda: " .$e);
        }
        
    }
}
