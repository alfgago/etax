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
        $current_company = auth()->user()->companies->first()->id;
        $invoices = Invoice::where('company_id', $current_company)->orderBy('generated_date', 'DESC')->paginate(10);
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
        return view("Invoice/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $invoice = new Invoice();
        $company = auth()->user()->companies->first();
        $invoice->company_id = $company->id;

        //Datos generales y para Hacienda
        $invoice->document_type = "01";
        $invoice->document_key = $request->document_key;
        $invoice->document_number = $request->document_number;
        $invoice->reference_number = $company->last_invoice_ref_number + 1;
        $invoice->sale_condition = $request->sale_condition;
        $invoice->payment_type = $request->payment_type;
        $invoice->credit_time = $request->credit_time;
        $invoice->buy_order = $request->buy_order;
        $invoice->other_reference = $request->other_reference;
        $invoice->hacienda_status = "01";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "";
        $invoice->generation_method = "M";
      
        //Datos de cliente
        
        if( $request->client_id == '-1' ){
            $tipo_persona = $request->tipo_persona;
            $identificacion_cliente = $request->id_number;
            $codigo_cliente = $request->code;
            
            $cliente = Client::firstOrCreate(
                [
                    'id_number' => $identificacion_cliente,
                    'company_id' => $company->id,
                ],
                [
                    'code' => $codigo_cliente ,
                    'company_id' => $company->id,
                    'tipo_persona' => $tipo_persona,
                    'id_number' => $identificacion_cliente
                ]
            );
            $cliente->first_name = $request->first_name;
            $cliente->last_name = $request->last_name;
            $cliente->last_name2 = $request->last_name2;
            $cliente->emisor_receptor = $request->emisor_receptor;
            $cliente->country = $request->country;
            $cliente->state = $request->state;
            $cliente->city = $request->city;
            $cliente->district = $request->district;
            $cliente->neighborhood = $request->neighborhood;
            $cliente->zip = $request->zip;
            $cliente->address = $request->address;
            $cliente->phone = $request->phone;
            $cliente->es_exento = $request->es_exento;
            $cliente->email = $request->email;
            $cliente->save();
                
            $invoice->client_id = $cliente->id;
        }else{
            $invoice->client_id = $request->client_id;
        }
        
        //Datos de factura
        $invoice->description = $request->description;
        $invoice->subtotal = $request->subtotal;
        $invoice->currency = $request->currency;
        $invoice->currency_rate = $request->currency_rate;
        $invoice->total = $request->total;
        $invoice->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $invoice->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $invoice->due_date = $fechaV;
      
        $invoice->save();
      
        foreach($request->items as $item){
          $item_number = $item['item_number'];
          $code = $item['code'];
          $name = $item['name'];
          $product_type = $item['product_type'];
          $measure_unit = $item['measure_unit'];
          $item_count = $item['item_count'];
          $unit_price = $item['unit_price'];
          $subtotal = $item['subtotal'];
          $total = $item['total'];
          $discount_percentage = '0';
          $discount_reason = '';
          $iva_type = $item['iva_type'];
          $iva_percentage = $item['iva_percentage'];
          $iva_amount = $item['iva_amount'];
          $is_exempt = false;
          $isIdentificacion = $item['is_identificacion_especifica'];
          
          $invoice->addItem( $fecha->year, $fecha->month, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt );
        }
        
        $company->last_invoice_ref_number = $invoice->reference_number;
        $company->last_document = $invoice->document_number;
        $company->save();
        
        $this->clearInvoiceCache($invoice);
      
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
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' && $invoice->generation_method != 'XML' ){
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
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' && $invoice->generation_method != 'XML' ){
          return redirect('/facturas-emitidas');
        }
      
        $company = $invoice->company; 
      
        //Datos generales y para Hacienda
        $invoice->document_key = $request->document_key;
        $invoice->document_number = $request->document_number;
        $invoice->sale_condition = $request->sale_condition;
        $invoice->payment_type = $request->payment_type;
        $invoice->credit_time = $request->credit_time;
        $invoice->buy_order = $request->buy_order;
        $invoice->other_reference = $request->other_reference;
      
        //Datos de cliente
        
        if( $request->client_id == '-1' ){
            $tipo_persona = $request->tipo_persona;
            $identificacion_cliente = $request->id_number;
            $codigo_cliente = $request->code;
            
            $cliente = Client::firstOrCreate(
                [
                    'id_number' => $identificacion_cliente,
                    'company_id' => $company->id,
                ],
                [
                    'code' => $codigo_cliente ,
                    'company_id' => $company->id,
                    'tipo_persona' => $tipo_persona,
                    'id_number' => $identificacion_cliente
                ]
            );
            $cliente->first_name = $request->first_name;
            $cliente->last_name = $request->last_name;
            $cliente->last_name2 = $request->last_name2;
            $cliente->emisor_receptor = $request->emisor_receptor;
            $cliente->country = $request->country;
            $cliente->state = $request->state;
            $cliente->city = $request->city;
            $cliente->district = $request->district;
            $cliente->neighborhood = $request->neighborhood;
            $cliente->zip = $request->zip;
            $cliente->address = $request->address;
            $cliente->phone = $request->phone;
            $cliente->es_exento = $request->es_exento;
            $cliente->email = $request->email;
            $cliente->save();
                
            $invoice->client_id = $cliente->id;
        }else{
            $invoice->client_id = $request->client_id;
        }
        
        //Datos de factura
        $invoice->description = $request->description;
        $invoice->subtotal = $request->subtotal;
        $invoice->currency = $request->currency;
        $invoice->currency_rate = $request->currency_rate;
        $invoice->total = $request->total;
        $invoice->iva_amount = $request->iva_amount;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->generated_date . ' ' . $request->hora);
        $invoice->generated_date = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->due_date );
        $invoice->due_date = $fechaV;
      
        $invoice->save();
      
        //Recorre las items de factura y las guarda
        $lids = array();
        foreach($request->items as $item) {
          
          $item_id = $item['id'] ? $item['id'] : 0;
          $item_number = $item['item_number'];
          $code = $item['code'];
          $name = $item['name'];
          $product_type = $item['product_type'];
          $measure_unit = $item['measure_unit'];
          $item_count = $item['item_count'];
          $unit_price = $item['unit_price'];
          $subtotal = $item['subtotal'];
          $total = $item['total'];
          $discount_percentage = '0';
          $discount_reason = '';
          $iva_type = $item['iva_type'];
          $iva_percentage = $item['iva_percentage'];
          $iva_amount = $item['iva_amount'];
          $is_exempt = false;
          $isIdentificacion = $item['is_identificacion_especifica'];
          
          $item_modificado = $invoice->addEditItem( $item_id, $fecha->year, $fecha->month, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $isIdentificacion, $is_exempt );

          array_push( $lids, $item_modificado->id );
        }
      
        foreach ( $invoice->items as $item ) {
          if( !in_array( $item->id, $lids ) ) {
            $item->delete();
          }
        }
        
        $this->clearInvoiceCache($invoice);
          
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

        $this->clearInvoiceCache($invoice);
        
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' && $invoice->generation_method != 'XML' ){
          return redirect('/facturas-emitidas');
        }
        
        foreach ( $invoice->items as $item ) {
          $item->delete();
        }
        $invoice->delete();
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
        
        $collection = Excel::toCollection( new InvoiceImport(), request()->file('archivo') );
        $company = auth()->user()->companies->first();
        
        $i = 0;
        
        if( $collection[0]->count() < 5001 ){
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
                                    $invoice->document_type = $row['idtipodocumento'];
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
                                    'iva_type' => $row['codigoimpuesto'],
                                    'iva_amount' => $row['montoiva'] ? $row['montoiva'] : 0,
                                ];
                            }
                            /**END LINEA DE FACTURA**/
                            $this->clearInvoiceCache($invoice);
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
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 5000 facturas por archivo.');
        }
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }  
    
    private function clearInvoiceCache($invoice){
        $month = $invoice->generatedDate()->month;
        $year = $invoice->dueDate()->year;
        CacheController::clearTaxesCache($invoice->company_id, $month, $year);
        CacheController::clearTaxesCache($invoice->company_id, 0, $year);
    }
    
}
