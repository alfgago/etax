<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;
use App\Invoice;
use App\InvoiceItem;
use App\RecurringInvoice;
use App\Company;

class EnvioRecurrentes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $recurrenteId = '';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recurrenteId)
    {
        $this->recurrenteId = $recurrenteId;
        $this->enviarRecurrentes();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }


    private function enviarRecurrentes(){
        $recurrente = RecurringInvoice::where('id', $this->recurrenteId)
                            ->with('company')
                            ->with('invoice')
                            ->with('invoice.items')->firstOrFail();
        $today = Carbon::now('America/Costa_Rica')->endOfDay()->subHours(1);
        
        //if($recurrente->company_id != '208'){ return false; }
        
        try{
            Log::info('Registrando factura recurrente');
            $oldInvoice = $recurrente->invoice;
            $invoice = new Invoice();
            
            $invoice = $oldInvoice->replicate();
            $invoice->document_key = 'Key programada';
            $invoice->document_number = 'Programada';
            $invoice->hacienda_status = '99';
            $invoice->hide_from_taxes = false;
            $invoice->is_void = false;
            $invoice->generated_date = $recurrente->next_send;
            $invoice->due_date = $recurrente->proximoVencimiento();
            $invoice->month = $today->month;
            $invoice->year = $today->year;
            $invoice->save();
            
            foreach ($oldInvoice->items as $oldItem) {
                $item = $oldItem->replicate();
                $item->invoice_id = $invoice->id;
                $item->month = $invoice->month;
                $item->year = $invoice->year;
                $item->save();
            }
            
            $recurrente->invoice_id = $invoice->id;
            $recurrente->next_send = $recurrente->proximo_envio($invoice->generated_date);
            
            Log::info("Proxima fecha: $recurrente->next_send.");
            $recurrente->save();
            
        }catch(\Exception $e){
            Log::error('Falló al crear recurrente: ' . $e);
        }
        
    }
    

}
