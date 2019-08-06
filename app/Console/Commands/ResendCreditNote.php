<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Jobs\ProcessCreditNote;
use App\Jobs\ProcessInvoice;
use App\Jobs\ProcessReception;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ResendCreditNote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creditnote:resend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend  Credit Note to Hacienda';

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
            $this->info('Sending Credit Note to Hacienda....');
            $invoices = Invoice::where('hacienda_status', '01')->where('generation_method', 'etax')
                ->where('resend_attempts', '<', 6)->where('in_queue', false)
                ->where('document_type', '03')->get();
            $this->info('Sending Credit Note ....'. count($invoices));
            $this->info('Get Token Api Hacienda ....');
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);

            foreach ($invoices as $invoice) {
                $company = $invoice->company;
                $this->info('Sending Credit Note ....'. $invoice->document_key);
                sleep(4);
                ProcessCreditNote::dispatch($invoice->id, $company->id, $tokenApi)
                    ->onConnection(config('etax.queue_connections'))->onQueue('invoices');
            }
        } catch ( \Exception $e) {
            Log::error('Error resend command credit note'.$e);
            $this->info('Error Resending Credit Note to Hacienda....'. $e);
        }
    }
}
