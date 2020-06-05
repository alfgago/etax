<?php

namespace App\Console\Commands;

use App\Bill;
use App\Company;
use App\IntegracionEmpresa;
use App\Invoice;
use App\Utils\BridgeGoSocketApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\GoSocketInvoicesSync;

class GoSocketSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gosocket:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command GoSocket Sync documents';

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
        $apiGoSocket = new BridgeGoSocketApi();
        $integracionesGS = IntegracionEmpresa::where('status', 1)->get();
        $this->info('Usuarios con token '. $integracionesGS->count());

        foreach ($integracionesGS as $integracion) {
            $queryDates = $apiGoSocket->getQueryDates($integracion);
            foreach($queryDates as $q){
                GoSocketInvoicesSync::dispatch($integracion, $integracion->company_id, $q)->onConnection(config('etax.queue_connections'))->onQueue('gosocket');
            }
            $integracion->first_sync_gs = false;
            $integracion->save();
        }

    }
}
