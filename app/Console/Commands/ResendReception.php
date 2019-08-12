<?php

namespace App\Console\Commands;

use App\Bill;
use App\Invoice;
use App\Jobs\ProcessCreditNote;
use App\Jobs\ProcessReception;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResendReception extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reception:resend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend Reception to Hacienda';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info('Sending Reception to Hacienda....');
            $bills = Bill::where('hacienda_status', '01')->where('accept_status', '1')->get();
            $this->info('Sending Reception ....'. count($bills));
            $this->info('Get Token Api Hacienda ....');
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);

            foreach ($bills as $bill) {
                $provider = $bill->provider;
                $company = $bill->company;
                if( isset($company->atv_validation) && $company ){
                    $ref = $company->last_rec_ref_number;
                    Log::info("Sending Reception ID:$bill->id, Empresa: $company->business_name, Doc: $bill->document_key" );
                    sleep(4);
                    ProcessReception::dispatch($bill->id, $provider->id, $tokenApi, $ref)
                        ->onConnection(config('etax.queue_connections'))->onQueue('receptions');
                }else{
                    Log::error("Error en resend: compra $bill->id no tiene empresa");
                }
            }
        } catch ( \Exception $e) {
            Log::error('Error resend command receptions'.$e);
            $this->info('Error Resending Receptions to Hacienda....'. $e);
        }
    }
}
