<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\Bill;
use App\BillItem;
use App\AvailableBills;
use App\CalculatedTax;
use App\Actividades;
use App\XmlHacienda;
use App\Http\Controllers\BillController;
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
use App\Jobs\LogActivityHandler as Activity;

class MassValidateBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $billItem = null;
    private $itemData = null;
    private $company = null;
    private $actividad = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($billItem, $itemData, $company, $actividad)
    {
        $this->billItem = $billItem;
        $this->itemData = $itemData;
        $this->company = $company;
        $this->actividad = $actividad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $resultBills = [];
        $billItem = $this->billItem;
        $itemData = $this->itemData;
        $company = $this->company;
        
        $bill = $billItem->bill;
        if( CalculatedTax::validarMes( $bill->generatedDate()->format('d/m/Y'), $company )){
            try{
            	$bill->activity_company_verification = $this->actividad;
                BillItem::where('id', $billItem->id)
                ->update([
                  'iva_type' =>  $itemData['iva_type'],
                  'product_type' =>  $itemData['product_type'],
                  'porc_identificacion_plena' =>  $itemData['porc_identificacion_plena'],
                  'is_code_validated' =>  true
                ]);
                $validated = true;
                foreach($bill->items as $item){
                    if(!$item->is_code_validated){
                        $validated = false;
                    }
                }
                if($validated){
                    $bill->is_code_validated = true;
                    if(!$company->use_invoicing){
                        $bill->accept_status = 1;
                    }
                    $billItem->calcularAcreditablePorLinea();
                    $bill->save();
                }
                
                $user = auth()->user();
                /*Activity::dispatch(
                    $user,
                    $bill,
                    [
                        'company_id' => $bill->company_id,
                        'id' => $bill->id,
                        'document_key' => $bill->document_key
                    ],
                    "La factura ". $bill->document_number . " ha sido validada."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');*/
                
                clearBillCache($bill);
            }catch(\Throwable $e){
                Log::error( "Error en validar " . $e->getMessage() );
            	//BillController::notificar(2, $company->id, $company->id, "Error validando factura", "Hubo un error validando la factura: $bill->document_number.", 'error', 'bills\validacion masiva', '/facturas-recibidas/lista-validar-masivo');
            } 
        }else{
                Log::warning( "Factura no validada por mes cerrado: " . $bill->generatedDate()->format('d/m/Y') );
        	//BillController::notificar(2, $company->id, $company->id, "Error validando factura", "No se pudo validar la factura: $bill->document_number ya que el mes ya fue cerrado.", 'error', 'bills\validacion masiva', '/facturas-recibidas/lista-validar-masivo');
        }
    }

}
