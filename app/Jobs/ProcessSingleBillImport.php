<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Bill;
use App\BillItem;
use App\AvailableBills;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\BillUtils;
use App\Jobs\ProcessReception;
use App\XmlHacienda;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessSingleBillImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billArr = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bill)
    {
        $this->billArr = $bill;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $billArr = $this->billArr;
        $fac = $billArr['factura'];
        $lineas = $billArr['lineas'];
        try {
            $bill = Bill::firstOrNew(	
              [	
                  'company_id' => $fac->company_id,	
                  'document_number' => $fac->document_number,	
                  'document_key' => $fac->document_key,	
                  'provider_id_number' => $fac->provider_id_number,	
              ], $fac->toArray()	
            );
            
            if( !$bill->id ){
                /*$available_bills = AvailableBills::where('company_id', $bill->company_id)
                                      ->where('year', $bill->year)
                                      ->where('month', $bill->month)
                                      ->first();
                if( isset($available_bills) ) {
                  $available_bills->current_month_sent = $available_bills->current_month_sent + 1;
                  $available_bills->save();
                }*/
                $bill->save();
            }
            
            $bill->subtotal = 0;
            $bill->iva_amount = 0;
            $bill->is_code_validated = true;
            
            foreach( $lineas as $linea ){
                $linea['bill_id'] = $bill->id;
                $bill->subtotal = $bill->subtotal + $linea['subtotal'];
                $bill->iva_amount = $bill->iva_amount + $linea['iva_amount'];
                $item = BillItem::updateOrCreate(
                [
                    'bill_id' => $linea['bill_id'],
                    'item_number' => $linea['item_number'],
                ], $linea);
                $item->fixCategoria();
            }
      
            $bill->save();
            if( $bill->year == 2018 ) {
             clearLastTaxesCache($bill->company_id, 2018);
            }
            clearBillCache($bill);
                
        }catch( \Throwable $ex ){
            Log::error("Error importando lineas de factura " . $ex);
        }
    }

}
