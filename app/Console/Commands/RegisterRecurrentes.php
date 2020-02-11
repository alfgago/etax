<?php

namespace App\Console\Commands;

use App\Invoice;
use App\RecurringInvoice;
use App\Jobs\ProcessInvoice;
use App\Jobs\EnvioRecurrentes;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

class RegisterRecurrentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:recurrentes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia facturas recurrentes';

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
            $recurringDate = Carbon::now('America/Costa_Rica')->addDays(3)->endOfDay();
            
            $recurrentes = RecurringInvoice::select('id', 'company_id')->where('next_send', '<=', $recurringDate)->get();
            $this->info("Sending recurring invoices $recurringDate .... Encontradas: ". count($recurrentes));

            foreach ($recurrentes as $rec) {
                $this->info("Enviando recurrentes $rec->company_id .... $rec->id");
                //sleep(1);
                EnvioRecurrentes::dispatch($rec->id)->onQueue('sendbulk');
            }
        } catch ( \Exception $e) {
            Log::error('Error recurrentes command '.$e);
        }
    }
}
