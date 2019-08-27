<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Jobs\ProcessInvoice;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResendInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:resend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend invoices to Hacienda';

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
            $this->info('Sending invoices to Hacienda....');
            $invoices = Invoice::where('hacienda_status', '01')->where('generation_method','like', '%etax%')
                ->where('resend_attempts', '<', 6)->where('in_queue', false)
                ->where('id', '!=', 1110)
                ->whereIn('document_type', ['01', '04', '08', '09'])->get();
            $this->info('Sending invoices ....'. count($invoices));
            $this->info('Get Token Api Hacienda ....');
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);

            foreach ($invoices as $invoice) {
                $company = $invoice->company;
                $invoice->resend_attempts = $invoice->resend_attempts + 1;
                $invoice->in_queue = true;
                $invoice->save();
                $this->info('Sending invoice ....'. $invoice->document_key);
                sleep(2);
                ProcessInvoice::dispatch($invoice->id, $company->id, $tokenApi)
                    ->onConnection(config('etax.queue_connections'))->onQueue('invoices');
            }
        } catch ( \Exception $e) {
            Log::error('Error resend command '.$e);
            $this->info('Error Resending invoices to Hacienda....'. $e);
        }
    }
}
