<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Jobs\ProcessInvoice;
use App\Jobs\EnvioProgramadas;
use App\Utils\BridgeHaciendaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRecurrentes extends Command
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
    protected $description = 'Envia facturas recurrenes';

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
            Log::info("disparo de job envio programada");
            EnvioProgramadas::dispatch()->onQueue('sendbulk');
        } catch ( \Exception $e) {
            Log::error('Error recurrentes command '.$e);
        }
    }
}
