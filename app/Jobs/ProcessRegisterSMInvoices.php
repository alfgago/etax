<?php

namespace App\Jobs;

use App\ApiResponse;
use App\Company;
use App\SMInvoice;
use App\Invoice;
use App\Mail\CreditNoteNotificacion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
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
use Illuminate\Support\Facades\Cache;

class ProcessRegisterSMInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $excelCollection = null;
    private $companyId = null;
    private $fileType = null;
    private $batchName = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($excelCollection, $companyId, $fileType, $batchName)
    {
        $this->excelCollection = $excelCollection;
        $this->companyId = $companyId;
        $this->fileType = $fileType;
        $this->batchName = $batchName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company = Company::find($this->companyId);
        $excelCollection = $this->excelCollection;
        $batchName = $this->batchName;
        $fileType = $this->fileType;
        
        sleep (1);
        Log::notice("SM Seguros importando ". $excelCollection->count() ." lineas...");
        $mainAct = $company->getActivities() ? $company->getActivities()[0]->codigo : 0;

        $i = 0;
        $invoiceList = array();
        $descripciones = $excelCollection->pluck('descricpion');
        $existingSMInvoices = SMInvoice::select('descripcion', 'total', 'document_key', 'batch')->whereIn('descripcion', $descripciones)->get();
        $existingInvoices = Invoice::select('id', 'description', 'total', 'document_key')->where('company_id', $this->companyId)->whereIn('description', $descripciones)->get();
        $today = Carbon::parse( now('America/Costa_Rica') );
        
        foreach ($excelCollection as $row){
            try{
                $registerSMInvoice = false;    
                if( isset($row['doc_identificacion']) ){
                    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : ($row['descricpion'] ?? null);
                    if( ! $existingSMInvoices->pluck('descripcion')->contains($descripcion) ){
                        $documentKey = null;
                        $batchRepeated = null;
                        $invoiceId = null;
                        if( $existingInvoices->pluck('description')->contains($descripcion) ){
                            $existingInvoice = $existingInvoices->where('description', $descripcion)->first();
                            $documentKey = $existingInvoice->document_key;
                            $invoiceId = $existingInvoice->invoice_id;
                        }
                        $registerSMInvoice = true;
                    }else{
                        $smInvoice = $existingSMInvoices->where('descripcion', $descripcion)->first();
                        if($smInvoice->batch != $batchName){
                            $documentKey = $smInvoice->document_key;
                            $invoiceId = $smInvoice->invoice_id;
                            $batchRepeated = $smInvoice->batch;
                        }
                        $registerSMInvoice = true;
                    }
                    
                    if($registerSMInvoice){
                        $smInvoice = SMInvoice::create([
                            'batch' => $batchName,
                            'document_type' => $fileType,
                            'document_key' => $documentKey,
                            'invoice_id' => $invoiceId,
                            'num_factura' => trim($row['num_factura']),
                            'num_objeto' => trim($row['num_objeto']),
                            'fecha_emision' => trim($row['fecha_emision']),
                            'fecha_pago' => trim($row['fecha_pago']),
                            'condicion' => trim($row['condicion']),
                            'medio_pago' => trim($row['medio_pago']),
                            'moneda' => trim($row['moneda']),
                            'tipo_id' => trim($row['tipo_id']),
                            'doc_identificacion' => trim($row['doc_identificacion']),
                            'nombre_tomador' => trim($row['nombre_tomador']),
                            'telefono_habitacion' => trim($row['telefono_habitacion']),
                            'telefono_celular' => trim($row['telefono_celular']),
                            'correo' => trim($row['correo']),
                            'provincia' => trim($row['provincia']),
                            'canton' => trim($row['canton']),
                            'distrito' => trim($row['distrito']),
                            'codigo_postal' => trim($row['codigo_postal']),
                            'des_direccion' => trim($row['des_direccion']),
                            'cantidad' => trim($row['cantidad']),
                            'precio_unitario' => trim($row['precio_unitario']),
                            'impuesto' => trim($row['impuesto']),
                            'total' => trim($row['total']),
                            'descripcion' => trim($descripcion),
                            'actividad_comercial' => isset($row['actividad_comercial']) ? trim($row['actividad_comercial']) : $mainAct,
                            'codigo_etax' => isset($row['codigo_etax']) ? trim($row['codigo_etax']) : trim($row['cogigo_etax']),
                            'categoria' => trim($row['categoria']),
                            'refer_factura' => isset($row['refer_factura']) ? trim($row['refer_factura']) : null,
                            'month' => $today->month,
                            'year' => $today->year,
                            'batch_repeated' => $batchRepeated,
                        ]);
                    }
                }
            }catch( \Throwable $ex ){
                Log::error("Error en factura SM:" . $ex);
            }
        }
    }
    
   

}
