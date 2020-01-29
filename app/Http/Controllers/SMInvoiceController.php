<?php

namespace App\Http\Controllers;

use App\SMInvoice;
use App\Invoice;
use App\InvoiceItem;
use App\Imports\InvoiceImportSM;
use App\Jobs\ProcessSendSMInvoices;
use App\Jobs\ProcessRegisterSMInvoices;
use App\Jobs\CheckSMExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class SMInvoiceController extends Controller
{
    /**SMInvoice
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }
        
        $batches = SMInvoice::select('batch')->distinct()->get();
        return view('SMInvoice/index', compact('batches'));
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }
        $batch = request()->get('batch');
        $query = SMInvoice::where('batch', $batch);
        
        return datatables()->eloquent( $query )
            ->orderColumn('created_date', '-created_date $1')
            ->addColumn('clave_col', function(SMInvoice $smInvoice) {
                if( isset($smInvoice->batch_repeated) ) {
                    return "Duplicada en $smInvoice->batch_repeated";
                }
                return isset($smInvoice->document_key) ? substr($smInvoice->document_key, 21, 20) : "No registrada";
            })
            ->toJson();
    }


    public function importExcelSM() {
        request()->validate([
          'archivo' => 'required',
        ]);
        
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }

        $fileType = request()->fileType ?? '01';
        $fileName = request()->file('archivo')->getClientOriginalName();
        $collection = Excel::toCollection( new InvoiceImportSM(), request()->file('archivo') );
        $companyId = currentCompany();
        
        try {
            Log::debug('Creando job de registro de facturas. Excel tiene: ' . $collection[0]->count() . ' lineas.');

            foreach ($collection[0]->chunk(50) as $facturas) {
                ProcessRegisterSMInvoices::dispatch($facturas, $companyId, $fileType, $fileName)->onQueue('bulk');
            }
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo: " . $ex);
            return redirect('/sm')->withError('Se detectó un error en el archivo. Por favor contacte a soporte.');
        }


        return redirect('/sm')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
    }

    public function confirmarEnvioExcelSM() {
        request()->validate([
          'batch' => 'required',
        ]);
        
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }

        $companyId = currentCompany();
        $batch =  request()->batch;
        $invoiceList = SMInvoice::where('batch', $batch)->whereNull('document_key')->whereNull('batch_repeated')->get();

        try {
            Log::debug('Creando job de envio de facturas SM.');
            foreach ($invoiceList->chunk(50) as $facturas) {
                ProcessSendSMInvoices::dispatch($facturas, $companyId)->onQueue('bulk');
            }
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo: " . $ex);
            return redirect('/sm')->withError('Se detectó un error en el archivo. Por favor contacte a soporte.');
        }


        return redirect('/sm')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
    }
    
    public function revisarNotasCredito() {
        request()->validate([
          'batch' => 'required',
        ]);
        
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }

        $companyId = currentCompany();
        $batch =  request()->batch;
        $invoiceList = SMInvoice::where('batch', $batch)->where('document_type', '03')->with('invoice')->get();

        try {
            Log::debug('Creando job de revisar Notas de Credito para SM.');
            foreach ($invoiceList->chunk(20) as $facturas) {
                CheckSMExcel::dispatch($facturas, $companyId)->onQueue('bulk');
            }
        }catch( \Throwable $ex ){
            Log::error("Error en NC SM: " . $ex);
            return redirect('/sm')->withError('Se detectó un error en el archivo. Por favor contacte a soporte.');
        }


        return redirect('/sm')->withMessage('NC revisadas, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
    }


}
