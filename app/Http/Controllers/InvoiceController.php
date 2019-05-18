<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Invoice;
use App\InvoiceItem;
use App\Company;
use App\Client;
use App\CalculatedTax;
use App\Http\Controllers\CacheController;
use App\Exports\InvoiceExport;
use App\Imports\InvoiceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_company = currentCompany();
        $invoices = Invoice::where('company_id', $current_company)->where('is_void', false)->where('is_totales', false)->with('client')->orderBy('generated_date', 'DESC')->orderBy('reference_number', 'DESC')->paginate(10);
        return view('Invoice/index', [
          'invoices' => $invoices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Invoice/create-factura-manual");
    }

    /**
     * Muestra el formulario para emitir facturas
     *
     * @return \Illuminate\Http\Response
     */
    public function emitFactura()
    {
        return view("Invoice/create-factura", ['document_type' => '01']);
    }
    
    /**
     * Muestra el formulario para emitir tiquetes electrónicos
     *
     * @return \Illuminate\Http\Response
     */
    public function emitTiquete()
    {
        return view("Invoice/create-factura", ['document_type' => '04']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'subtotal' => 'required',
            'items' => 'required',
        ]);
        
        $invoice = new Invoice();
        $company = currentCompanyModel();
        $invoice->company_id = $company->id;

        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "";
        $invoice->generation_method = "M";
        $invoice->reference_number = $company->last_invoice_ref_number + 1;
        
        $invoice->setInvoiceData($request);
        
        $company->last_invoice_ref_number = $invoice->reference_number;
        $company->last_document = $invoice->document_number;
        $company->save();
        
        clearInvoiceCache($invoice);
      
        return redirect('/facturas-emitidas');
    }
    
    /**
     * Envía la factura electrónica a Hacienda
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendHacienda(Request $request)
    {
        $invoice = new Invoice();
        $company = currentCompanyModel();
        $invoice->company_id = $company->id;

        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "";
        $invoice->generation_method = "ETAX";
        $invoice->reference_number = $company->last_invoice_ref_number + 1;
        
        $invoice->setInvoiceData($request);
        
        dd($invoice);
        
        $company->last_invoice_ref_number = $invoice->reference_number;
        $company->last_document = $invoice->document_number;
        $company->save();
        
        clearInvoiceCache($invoice);
      
        return redirect('/facturas-emitidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
          return redirect('/facturas-emitidas');
        }  
      
        return view('Invoice/edit', compact('invoice') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
          return redirect('/facturas-emitidas');
        }
      
        $invoice->setInvoiceData($request);
        
        clearInvoiceCache($invoice);
          
        return redirect('/facturas-emitidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $this->authorize('update', $invoice);

        clearInvoiceCache($invoice);
        
        $invoice->is_void = true;
        $invoice->save();
        
        return redirect('/facturas-emitidas');
    }
    
    public function export() {
        return Excel::download(new InvoiceExport(), 'documentos-emitidos.xlsx');
    }
    
    public function import() {
        
        request()->validate([
          'archivo' => 'required',
          'tipo_archivo' => 'required',
        ]);
      
        $time_start = $this->microtime_float();
        
        try {
            $collection = Excel::toCollection( new InvoiceImport(), request()->file('archivo') );
        }catch( \Exception $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }catch( \Throwable $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }
        
        $company = currentCompanyModel();
        $i = 0;
        
        if( $collection[0]->count() < 2501 ){
            try {
                foreach ($collection[0]->chunk(200) as $facturas) {
                    \DB::transaction(function () use ($facturas, &$company, &$i) {
                        
                        $inserts = array();
                        foreach ($facturas as $row){
                            $i++;
                            
                            //Datos de cliente
                            $nombre_cliente = $row['nombrecliente'];
                            $codigo_cliente = $row['codigocliente'] ? $row['codigocliente'] : '';
                            $tipo_persona = $row['tipoidentificacion'];
                            $identificacion_cliente = $row['identificacionreceptor'];
                            
                            $clientCacheKey = "import-clientes-$identificacion_cliente-".$company->id;
                            if ( !Cache::has($clientCacheKey) ) {
                                $clienteCache =  Client::firstOrCreate(
                                    [
                                        'id_number' => $identificacion_cliente,
                                        'company_id' => $company->id,
                                    ],
                                    [
                                        'code' => $codigo_cliente ,
                                        'company_id' => $company->id,
                                        'tipo_persona' => str_pad($tipo_persona, 2, '0', STR_PAD_LEFT),
                                        'id_number' => $identificacion_cliente,
                                        'first_name' => $nombre_cliente
                                    ]
                                );
                                Cache::put($clientCacheKey, $clienteCache, 30);
                            }
                            $cliente = Cache::get($clientCacheKey);
                            
                            $invoiceCacheKey = "import-factura-$identificacion_cliente-" . $company->id . "-" . $row['consecutivocomprobante'];
                            if ( !Cache::has($invoiceCacheKey) ) {
                            
                                $invoice = Invoice::firstOrNew(
                                    [
                                        'company_id' => $company->id,
                                        'client_id' => $cliente->id,
                                        'document_number' => $row['consecutivocomprobante']
                                    ]
                                );
                                
                                if( !$invoice->exists ) {
                                    
                                    $invoice->company_id = $company->id;
                                    $invoice->client_id = $cliente->id;    
                            
                                    //Datos generales y para Hacienda
                                    $tipoDocumento = $row['tipodocumento'];
                                    if( $tipoDocumento == '01' || $tipoDocumento == '02' || $tipoDocumento == '03' || $tipoDocumento == '04' 
                                        || $tipoDocumento == '05' || $tipoDocumento == '06' || $tipoDocumento == '07' || $tipoDocumento == '08' || $tipoDocumento == '99' ) {
                                        $bill->document_type = $tipoDocumento;    
                                    } else {
                                       $bill->document_type = '01'; 
                                    }
                                    
                                    $invoice->reference_number = $company->last_invoice_ref_number + 1;
                                    $invoice->document_number =  $row['consecutivocomprobante'];
                                    
                                    //Datos generales
                                    $invoice->sale_condition = str_pad($row['condicionventa'], 2, '0', STR_PAD_LEFT);
                                    $invoice->payment_type = str_pad($row['metodopago'], 2, '0', STR_PAD_LEFT);
                                    $invoice->credit_time = 0;
                                    
                                    /*$invoice->buy_order = $row['ordencompra'] ? $row['ordencompra'] : '';
                                    $invoice->other_reference = $row['referencia'] ? $row['referencia'] : '';
                                    $invoice->other_document = $row['documentoanulado'] ? $row['documentoanulado'] : '';
                                    $invoice->hacienda_status = $row['estadohacienda'] ? $row['estadohacienda'] : '01';
                                    $invoice->payment_status = $row['estadopago'] ? $row['estadopago'] : '01';
                                    $invoice->payment_receipt = $row['comprobantepago'] ? $row['comprobantepago'] : '';*/
                                    
                                    $invoice->generation_method = "XLSX";
                                    
                                    //Datos de factura
                                    $invoice->currency = $row['idmoneda'];
                                    if( $invoice->currency == 1 ) { $invoice->currency = "CRC"; }
                                    if( $invoice->currency == 2 ) { $invoice->currency = "USD"; }
                                        
                                    
                                    $invoice->currency_rate = $row['tipocambio'];
                                    //$invoice->description = $row['description'] ? $row['description'] : '';
                                  
                                    $company->last_invoice_ref_number = $invoice->reference_number;
                                    
                                    $invoice->subtotal = 0;
                                    $invoice->iva_amount = 0;
                                    $invoice->total = $row['totaldocumento'];
                                    
                                    $invoice->save();
                                }   
                                Cache::put($invoiceCacheKey, $invoice, 30);
                            }
                            $invoice = Cache::get($invoiceCacheKey);
                            
                            $invoice->generated_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision']);
                            $invoice->due_date = Carbon::createFromFormat('d/m/Y', $row['fechaemision'])->addDays(15);  /////IMPORTANTE CORREGIR ANTES DE PRODUCCION
                            
                            $year = $invoice->generated_date->year;
                            $month = $invoice->generated_date->month;
                            
                            $invoice->year = $year;
                            $invoice->month = $month;
                          
                            /**LINEA DE FACTURA**/
                            $item = InvoiceItem::firstOrNew(
                                [
                                    'invoice_id' => $invoice->id,
                                    'item_number' => $row['numerolinea'] ? $row['numerolinea'] : 1,
                                ]
                            );
                            
                            if( !$item->exists ) {
                                $invoice->subtotal = $invoice->subtotal + (float)$row['subtotallinea'];
                                $invoice->iva_amount = $invoice->iva_amount + (float)$row['montoiva'];
                                
                                $inserts[] = [
                                    'invoice_id' => $invoice->id,
                                    'company_id' => $company->id,
                                    'year' => $year,
                                    'month' => $month,
                                    'item_number' => $row['numerolinea'] ? $row['numerolinea'] : 0,
                                    'code' => $row['codigoproducto'],
                                    'name' => $row['detalleproducto'],
                                    'product_type' => 1,
                                    'measure_unit' => $row['unidadmedicion'],
                                    'item_count' => $row['cantidad'] ? $row['cantidad'] : 1,
                                    'unit_price' => $row['preciounitario'],
                                    'subtotal' => $row['subtotallinea'],
                                    'total' => $row['totallinea'],
                                    'discount_type' => '01',
                                    'discount' => $row['montodescuento'] ? $row['montodescuento'] : 0,
                                    'discount_reason' => '',
                                    'iva_type' => $row['codigoetax'],
                                    'iva_amount' => $row['montoiva'] ? $row['montoiva'] : 0,
                                ];
                            }
                            /**END LINEA DE FACTURA**/
                            clearInvoiceCache($invoice);
                            $invoice->save();
                        }
                        
                        InvoiceItem::insert($inserts);
                    });
                    
                }
            }catch( \ErrorException $ex ){
                return back()->withError('Por favor verifique que su documento de excel contenga todas las columnas indicadas. Error en la fila. '.$i.'. Mensaje:' . $ex->getMessage());
            }catch( \InvalidArgumentException $ex ){
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Por favor verifique que los campos de fecha estén correctos. Formato: "dd/mm/yyyy : 01/01/2018"');
            }catch( \Exception $ex ){
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Error en la fila. '.$i.'. Mensaje:' . $ex->getMessage());
            }
            
            $company->save();
            
            $time_end = $this->microtime_float();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente en '.$time.'s.');
        }else{
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 2500 facturas por archivo.');
        }
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }  
    

    
}
