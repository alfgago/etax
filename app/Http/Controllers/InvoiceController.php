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
use Yajra\Datatables\Datatables;
use App\DataTables\InvoicesDataTable;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Log;
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
        $this->middleware('auth', ['except' => ['receiveEmailInvoices']] );
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Invoice/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $current_company = currentCompany();
        
        $query = Invoice::where('invoices.company_id', $current_company)
                ->where('is_void', false)
                ->where('is_authorized', true)
                ->where('is_code_validated', true)
                ->where('is_totales', false)
                ->with('client');
                
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($invoice) {
                $hideEdit = false;
                if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
                    $hideEdit =  true;
                }
                return view('datatables.actions', [
                    'routeName' => 'facturas-emitidas',
                    'deleteTitle' => 'Anular factura',
                    'editTitle' => 'Editar factura',
                    'deleteIcon' => 'fa fa-ban',
                    'hideEdit' => $hideEdit,
                    'id' => $invoice->id
                ])->render();
            }) 
            ->editColumn('client', function(Invoice $invoice) {
                return $invoice->client->fullname;
            })
            ->editColumn('generated_date', function(Invoice $invoice) {
                return $invoice->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions'])
            ->toJson();
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
        return redirect()->back()->withMessage("Funcionalidad de facturación electrónica habilitada muy pronto.");
        
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
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
      
        return view('Invoice/show', compact('invoice') );
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
    
    public function importExcel() {
        
        request()->validate([
          'archivo' => 'required',
        ]);
      
        $time_start = getMicrotime();
        
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
                            
                            $metodoGeneracion = "XLSX";
                            
                            //Datos de proveedor
                            $nombreCliente = $row['nombrecliente'];
                            $codigoCliente = array_key_exists('codigocliente', $row) ? $row['codigocliente'] : '';
                            $tipoPersona = $row['tipoidentificacion'];
                            $identificacionCliente = $row['identificacionreceptor'];
                            $correoCliente = '';
                            $telefonoCliente = '';
                                        
                            //Datos de factura
                            $consecutivoComprobante = $row['consecutivocomprobante'];
                            $claveFactura = array_key_exists('clavefactura', $row) ? $row['clavefactura'] : '';
                            $condicionVenta = str_pad($row['condicionventa'], 2, '0', STR_PAD_LEFT);
                            $metodoPago = str_pad($row['metodopago'], 2, '0', STR_PAD_LEFT);
                            $numeroLinea = array_key_exists('numerolinea', $row) ? $row['numerolinea'] : 1;
                            $fechaEmision = $row['fechaemision'];
                            
                            $fechaVencimiento = array_key_exists('fechavencimiento', $row) ? $row['fechavencimiento'] : $fechaEmision;
                            $idMoneda = $row['idmoneda'];
                            $tipoCambio = $row['tipocambio'];
                            $totalDocumento = $row['totaldocumento'];
                            $tipoDocumento = $row['tipodocumento'];
                            $descripcion = array_key_exists('descripcion', $row)  ? $row['descripcion'] : '';
                            
                            //Datos de linea
                            $codigoProducto = $row['codigoproducto'];
                            $detalleProducto = $row['detalleproducto'];
                            $unidadMedicion = $row['unidadmedicion'];
                            $cantidad = array_key_exists('cantidad', $row) ? $row['cantidad'] : 1;
                            $precioUnitario = $row['preciounitario'];
                            $subtotalLinea = (float)$row['subtotallinea'];
                            $totalLinea = $row['totallinea'];
                            $montoDescuento = array_key_exists('montodescuento', $row) ? $row['montodescuento'] : 0;
                            $codigoEtax = $row['codigoetax'];
                            $montoIva = (float)$row['montoiva'];
                            $totalNeto = 0;
                            
                            $insert = Invoice::importInvoiceRow(
                                $metodoGeneracion, 0, $nombreCliente, $codigoCliente, $tipoPersona, $identificacionCliente, $correoCliente, $telefonoCliente,
                                $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                                $idMoneda, $tipoCambio, $totalDocumento, $totalNeto, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, true, true
                            );
                            
                            if( $insert ) {
                                array_push( $inserts, $insert );
                            }
                            
                        }
                        
                        InvoiceItem::insert($inserts);
                    });
                    
                }
            }catch( \ErrorException $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError('Por favor verifique que su documento de excel contenga todas las columnas indicadas. Error en la fila: '.$i);
            }catch( \InvalidArgumentException $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Por favor verifique que los campos de fecha estén correctos. Formato: "dd/mm/yyyy : 01/01/2018"');
            }catch( \Exception $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Error en la fila. '.$i);
            }catch( \Throwable $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. '.$i);
            }
            
            $company->save();
            
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente en '.$time.'s.');
        }else{
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 2500 facturas por archivo.');
        }
    }
    
    public function importXML() {
        request()->validate([
          'xmls' => 'required'
        ]);
        
        $count = count(request()->file('xmls'));
        if( $count > 10 ) {
            return back()->withError( 'Por favor mantenga el límite de 10 archivos por intento.');
        }
          
        try {  
            $time_start = getMicrotime();
            $company = currentCompanyModel();
            if( request()->hasfile('xmls') ) {
                foreach(request()->file('xmls') as $file) {
                    $xml = simplexml_load_string( file_get_contents($file) );
                    $json = json_encode( $xml ); // convert the XML string to JSON
                    $arr = json_decode( $json, TRUE );
                    
                    $identificacionReceptor = $arr['Receptor']['Identificacion']['Numero'];
                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3
                        if( Invoice::saveInvoiceXML( $arr, 'XML' ) ) {
                            Invoice::storeXML( $file, $consecutivoComprobante, $identificacionEmisor, $identificacionReceptor );
                        }
                    }else{
                        return back()->withError( "La factura $consecutivoComprobante subida no le pertenece a su compañía actual." );
                    }
                }
            }
            $company->save();
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
        }catch( \Exception $ex ){
            Log::error('Error importando con archivo inválido' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Asegúrese de estar enviando un XML válido.');
        }catch( \Throwable $ex ){
            Log::error('Error importando con archivo inválido' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Asegúrese de estar enviando un XML válido.');
        }
        
        return redirect('/facturas-emitidas/validaciones')->withMessage('Facturas importados exitosamente en '.$time.'s');
        
    }
    
    /**
     * Despliega las facturas que requieren validación de códigos
     *
     * @return \Illuminate\Http\Response
     */
    public function indexValidaciones()
    {
        $current_company = currentCompany();
        $invoices = Invoice::where('company_id', $current_company)
                    ->where('is_void', false)
                    ->where('is_totales', false)
                    ->where('is_code_validated', false)
                    ->where('is_authorized', true)
                    ->orderBy('generated_date', 'DESC')
                    ->orderBy('reference_number', 'DESC')->paginate(10);
        return view('Invoice/index-validaciones', [
          'invoices' => $invoices
        ]);
    }
    
    public function confirmarValidacion( Request $request, $id )
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        $tipoIva = $request->tipo_iva;
        foreach( $invoice->items as $item ) {
            $item->iva_type = $request->tipo_iva;
            $item->save();
        }
        
        $invoice->is_code_validated = true;
        $invoice->save();
        
        if( $invoice->year == 2018 ) {
            clearLastTaxesCache($invoice->company->id, 2018);
        }
        clearInvoiceCache($invoice);
        
        return redirect('/facturas-emitidas/validaciones')->withMessage( 'La factura '. $invoice->document_number . 'ha sido validada');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAuthorize()
    {
        return view('Invoice/index-autorizaciones');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataAuthorize() {
        $current_company = currentCompany();

        $query = Invoice::where('invoices.company_id', $current_company)
        ->where('is_void', false)
        ->where('is_authorized', false)
        ->where('is_totales', false)
        ->with('client');
        
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($invoice) {
                return view('Invoice.ext.auth-actions', [
                    'id' => $invoice->id
                ])->render();
            }) 
            ->editColumn('client', function(Invoice $invoice) {
                return $invoice->client->fullname;
            })
            ->editColumn('generated_date', function(Invoice $invoice) {
                return $invoice->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
    
    public function authorizeInvoice ( Request $request, $id )
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        if ( $request->autorizar ) {
            $invoice->is_authorized = true;
            $invoice->save();
            return redirect('/facturas-emitidas/autorizaciones')->withMessage( 'La factura '. $invoice->document_number . 'ha sido autorizada');
        }else {
            $invoice->is_authorized = false;
            $invoice->is_void = true;
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
            return redirect('/facturas-emitidas/autorizaciones')->withMessage( 'La factura '. $invoice->document_number . 'ha sido rechazada');
        }
        
        
    }
    
    
    
    
}
