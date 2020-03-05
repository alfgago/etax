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
use DB;

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
        $query = SMInvoice::where('batch', $batch)->with('invoice');
        
        return datatables()->eloquent( $query )
            ->orderColumn('created_date', '-created_date $1')
            ->addColumn('clave_col', function(SMInvoice $smInvoice) {
                if( isset($smInvoice->batch_repeated) ) {
                    return "Duplicada en $smInvoice->batch_repeated";
                }
                if(isset($smInvoice->document_key)){
                    $invoice = $smInvoice->invoice;
                    if( !isset($invoice) ){
                        $invoice = Invoice::where('document_key', $smInvoice->document_key)->first();
                        $smInvoice->invoice_id = $invoice->id;
                        $smInvoice->save();
                    }
                    $estado = "";
                    if ($invoice->hacienda_status == '01') {
                        $estado = '<div class="yellow"><span class="tooltiptext">Procesando...</span></div>';
                    }
                    if ($invoice->hacienda_status == '03' || $invoice->hacienda_status == '30') {
                        $estado = '<div class="green">  <span class="tooltiptext">Aceptada</span></div>';
                    }
                    if ($invoice->hacienda_status == '04') {
                        $estado = '<div class="red"> <span class="tooltiptext">Rechazada</span></div>';
                    }
                    if ($invoice->hacienda_status == '05') {
                        $estado = '<div class="orange"> <span class="tooltiptext">Esperando respuesta de hacienda</span></div>';
                    }
                    if ($invoice->hacienda_status == '99') {
                         $estado = '<div class="blue"><span class="tooltiptext">Programada</span></div>';
                    }
                    
                    return "<a style='margin-right: 1rem;' href='/facturas-emitidas/$invoice->id' target='_blank'>$invoice->reference_number</a> $estado";
                }else{
                    return "No registrada";
                }
            })
            ->addColumn('mes_col', function(SMInvoice $smInvoice) {
                $invoice = $smInvoice->invoice;
                if( !isset($invoice) ){
                    return "";
                }else {
                    return "$invoice->year/".str_pad($invoice->month, 2, '0', STR_PAD_LEFT);
                }
            })
            ->rawColumns(['clave_col'])
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
            
            $i = 0;
            $chunkSize = 50;
            foreach ($collection[0]->chunk($chunkSize) as $facturas) {
                ProcessRegisterSMInvoices::dispatch($facturas, $companyId, $fileType, $fileName, $chunkSize, $i)->onQueue('bulk');
                $i++;
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
            foreach ($invoiceList->chunk(25) as $facturas) {
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
    
    public function SMDashboardWidget( $year, $month ) {
        
        $companyId = currentCompany();
        if($companyId != '1110'){
            //return 404;
        }
        
        $facturasExcelCache = "cachekey-smfacturasexcel-$year-$month";
        if ( !Cache::has($facturasExcelCache) ) {
            $facturasExcel = DB::select( DB::raw("select sum(it.subtotal*currency_rate) as subtotal, sum(it.iva_amount*currency_rate) as iva, sum(it.total*currency_rate) as total,
            COUNT(IF(hacienda_status = '01', 1, NULL)) as pendientes,
            COUNT(IF(hacienda_status = '03', 1, NULL)) as aceptadas,
            COUNT(IF(hacienda_status = '04', 1, NULL)) as rechazadas
            FROM invoices inv, invoice_items it
            WHERE inv.company_id = 1110 AND inv.id = it.invoice_id
            AND it.year = $year AND it.month = $month AND document_type != '03'
            AND generation_method = 'etax-bulk' AND is_void = 0
            AND inv.deleted_at IS NULL AND hide_from_taxes = false;") )[0];
            Cache::put($facturasExcelCache, $facturasExcel, now()->addMinutes(2));
        }else{
            $facturasExcel = Cache::get($facturasExcelCache);
        }
        
        $facturasEtaxCache = "cachekey-smfacturasetax-$year-$month";
        if ( !Cache::has($facturasEtaxCache) ) {
            $facturasEtax = DB::select( DB::raw("select sum(it.subtotal*currency_rate) as subtotal, sum(it.iva_amount*currency_rate) as iva, sum(it.total*currency_rate) as total,
            COUNT(IF(hacienda_status = '01', 1, NULL)) as pendientes,
            COUNT(IF(hacienda_status = '03', 1, NULL)) as aceptadas,
            COUNT(IF(hacienda_status = '04', 1, NULL)) as rechazadas
            FROM invoices inv, invoice_items it
            WHERE inv.company_id = 1110 AND inv.id = it.invoice_id
            AND it.year = $year AND it.month = $month AND document_type != '03'
            AND generation_method != 'etax-bulk' AND is_void = 0
            AND inv.deleted_at IS NULL AND hide_from_taxes = false;") )[0];
            Cache::put($facturasEtaxCache, $facturasEtax, now()->addMinutes(2));
        }else{
            $facturasEtax = Cache::get($facturasEtaxCache);
        }
        
        $notasExcelCache = "cachekey-smnotasexcel-$year-$month";
        if ( !Cache::has($notasExcelCache) ) {    
            $notasExcel = DB::select( DB::raw("select sum(it.subtotal*currency_rate) as subtotal, sum(it.iva_amount*currency_rate) as iva, sum(it.total*currency_rate) as total,
            COUNT(IF(hacienda_status = '01', 1, NULL)) as pendientes,
            COUNT(IF(hacienda_status = '03', 1, NULL)) as aceptadas,
            COUNT(IF(hacienda_status = '04', 1, NULL)) as rechazadas
            FROM invoices inv, invoice_items it
            WHERE inv.company_id = 1110 AND inv.id = it.invoice_id
            AND it.year = $year AND it.month = $month AND document_type = '03'
            AND generation_method = 'etax-bulk' AND is_void = 0
            AND inv.deleted_at IS NULL AND hide_from_taxes = false;") )[0];
            Cache::put($notasExcelCache, $notasExcel, now()->addMinutes(2));
        }else{
            $notasExcel = Cache::get($notasExcelCache);
        }
        
        $notasEtaxCache = "cachekey-smnotasetax-$year-$month";
        if ( !Cache::has($notasEtaxCache) ) {    
            $notasEtax = DB::select( DB::raw("select sum(it.subtotal*currency_rate) as subtotal, sum(it.iva_amount*currency_rate) as iva, sum(it.total*currency_rate) as total,
            COUNT(IF(hacienda_status = '01', 1, NULL)) as pendientes,
            COUNT(IF(hacienda_status = '03', 1, NULL)) as aceptadas,
            COUNT(IF(hacienda_status = '04', 1, NULL)) as rechazadas
            FROM invoices inv, invoice_items it
            WHERE inv.company_id = 1110 AND inv.id = it.invoice_id
            AND it.year = $year AND it.month = $month AND document_type = '03'
            AND generation_method != 'etax-bulk' AND is_void = 0
            AND inv.deleted_at IS NULL AND hide_from_taxes = false;") )[0];
            Cache::put($notasEtaxCache, $notasEtax, now()->addMinutes(2));
        }else{
            $notasEtax = Cache::get($notasEtaxCache);
        }

        return view('SMInvoice/smwidget', compact('facturasExcel', 'facturasEtax', 'notasExcel', 'notasEtax'));
    }


}
