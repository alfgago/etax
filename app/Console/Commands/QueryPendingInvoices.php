<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Jobs\QueryHaciendaStatus;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueryPendingInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $invoices = Invoice::where('hacienda_status', '05')
                ->where('is_void', false)
                ->where('resend_attempts', '<', 6)->where('in_queue', false)
                ->whereIn('document_type', ['01', '03', '04', '08', '09'])
                ->get();
            $this->info('Querying pending invoices ....'. count($invoices));
            $this->info('Get Token Api Hacienda ....');
            foreach ($invoices as $invoice) {
                QueryHaciendaStatus::dispatch($invoice)->onQueue('invoices');
            }
            
        } catch ( \Exception $e) {
            Log::error('Error in query pending command '.$e);
        }
    }
}
