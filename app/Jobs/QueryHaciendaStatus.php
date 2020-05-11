<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $dateLimit = Carbon::now()->addMonths(-2);
            if( $invoice->created_at > $dateLimit){
                $apiHacienda = new BridgeHaciendaApi();
                $company = $invoice->company;
                $tokenApi = Cache::remember('token-api-queries', '60000', function () use ($apiHacienda, $company)  {
                    return $apiHacienda->login(false, $company->id);
                });
                Log::debug("Query de $invoice->document_number, empresa $invoice->company_id");
                if ($tokenApi !== false) {
                    $result = $apiHacienda->queryHacienda($invoice, $tokenApi, $company);
                }
            }
            $invoice->query_attempts = $invoice->query_attempts+1;
            $invoice->save();
        } catch (\Exception $e) {
            Log::error("Error en job query hacienda: " .$e);
        }
        
    }
}
