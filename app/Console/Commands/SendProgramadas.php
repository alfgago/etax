<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Jobs\ProcessInvoice;
use App\Jobs\EnvioProgramadas;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

class SendProgramadas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:programadas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia facturas programadas';

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
            $today = Carbon::now('America/Costa_Rica')->endOfDay()->subHours(1);
            
            $invoices = Invoice::where("hacienda_status",'99')
                                ->where('is_void', false)
                                ->where('generated_date', '<=', $today)
                                ->get();
            
            foreach ($invoices as $invoice) {
                $this->info('Enviando programadas ....'. $invoice->id);
                //sleep(1);
                EnvioProgramadas::dispatch($invoice->id)->onQueue('sendbulk');
            }
        } catch ( \Exception $e) {
            Log::error('Error programadas command '.$e);
        }
    }
}
