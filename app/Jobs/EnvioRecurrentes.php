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
                
        if($recurrente->company_id != '208'){ return false; }
        
        try{
            Log::info("Recurrente ID: $recurrente->id, copia de Invoice: $recurrente->invoice_id, fecha: $recurrente->next_send");
            $oldInvoice = $recurrente->invoice;
            if($oldInvoice->hacienda_status != '99' || $oldInvoice->is_void){
                $invoice = new Invoice();
                $generatedDate = Carbon::parse($recurrente->next_send);
                $invoice = $oldInvoice->replicate();
                $invoice->document_key = 'Key programada';
                $invoice->document_number = 'Programada';
                $invoice->hacienda_status = '99';
                $invoice->hide_from_taxes = false;
                $invoice->is_void = false;
                $invoice->generated_date = $recurrente->next_send;
                $invoice->due_date = $recurrente->proximoVencimiento();
                $invoice->month = $generatedDate->month;
                $invoice->year = $generatedDate->year;
                $invoice->save();
                
                foreach ($oldInvoice->items as $oldItem) {
                    $item = $oldItem->replicate();
                    $item->invoice_id = $invoice->id;
                    $item->month = $invoice->month;
                    $item->year = $invoice->year;
                    $item->save();
                }
                
                $recurrente->invoice_id = $invoice->id;
                $recurrente->next_send = $recurrente->proximo_envio($recurrente->next_send);
                
                Log::info("Proxima fecha: $recurrente->next_send.");
                $recurrente->save();
            }else{
                Log::warning("Hubiera repetido esta");
            }
            
        }catch(\Exception $e){
            Log::error('Falló al crear recurrente: ' . $e);
        }
        
    }
    

}
