<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Company;
use App\Provider;
use App\CalculatedTax;
use App\Http\Controllers\CacheController;
use App\Exports\BillExport;
use App\Imports\BillImport;
use Maatwebsite\Excel\Facades\Excel;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class BillController extends Controller
{
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['receiveEmailBills']] );
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Bill/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $current_company = currentCompany();

        $query = Bill::where('bills.company_id', $current_company)->where('is_void', false)->where('is_authorized', true)->where('is_totales', false)->with('provider');
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($bill) {
                $hideEdit = false;
                if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
                    $hideEdit =  true;
                }
                return view('datatables.actions', [
                    'routeName' => 'facturas-recibidas',
                    'showTitle' => 'Ver factura',
                    'deleteTitle' => 'Anular factura',
                    'editTitle' => 'Editar factura',
                    'deleteIcon' => 'fa fa-ban',
                    'hideEdit' => $hideEdit,
                    'id' => $bill->id
                ])->render();
            }) 
            ->editColumn('provider', function(Bill $bill) {
                return $bill->provider->fullname;
            })
            ->editColumn('generated_date', function(Bill $bill) {
                return $bill->generatedDate()->format('d/m/Y');
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
        return view("Bill/create");
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
      
        $bill = new Bill();
        $company = currentCompanyModel();
        $bill->company_id = $company->id;

        //Datos generales y para Hacienda
        $bill->document_type = "01";
        $bill->hacienda_status = "01";
        $bill->payment_status = "01";
        $bill->payment_receipt = "";
        $bill->generation_method = "M";
        $bill->reference_number = $company->last_bill_ref_number + 1;
        
        $bill->setBillData($request);
        
        $company->last_bill_ref_number = $bill->reference_number;
        $company->save();
        
        clearBillCache($bill);
      
        return redirect('/facturas-recibidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
      
        return view('Bill/show', compact('bill') );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
      
        //Valida que la factura recibida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
          return redirect('/facturas-recibidas');
        } 
      
        return view('Bill/edit', compact('bill') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
          return redirect('/facturas-emitidas');
        }
      
        $bill->setBillData($request);
        
        clearBillCache($bill);
        
        return redirect('/facturas-recibidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bill = Bill::find($id);
        $this->authorize('update', $bill);
        
        clearBillCache($bill);
        
        $bill->is_void = true;
        $bill->save();
        
        return redirect('/facturas-recibidas');
    }
    
    
    public function export() {
        return Excel::download(new BillExport(), 'documentos-recibidos.xlsx');
    }

    public function importExcel() {
        
        request()->validate([
          'archivo' => 'required',
        ]);
      
        $time_start = getMicrotime();
        
        try {
            $collection = Excel::toCollection( new BillImport(), request()->file('archivo') );
        }catch( \Exception $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }catch( \Throwable $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }
        
        $company = currentCompanyModel();
        
        if( $collection[0]->count() < 2501 ){
            try {
                foreach ($collection[0]->chunk(200) as $facturas) {
                    \DB::transaction(function () use ($facturas, &$company, &$i) {
                        
                        $inserts = array();
                        foreach ($facturas as $row){
                            $i++;
                            
                            $metodoGeneracion = "XLSX";
                            
                            //Datos de proveedor
                            $nombreProveedor = $row['nombreproveedor'];
                            $codigoProveedor = $row['codigoproveedor'] ? $row['codigoproveedor'] : '';
                            $tipoPersona = $row['tipoidentificacion'];
                            $identificacionProveedor = $row['identificacionproveedor'];
                            $correoProveedor = '';
                            $telefonoProveedor = '';
                                        
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
                            
                            $insert = Bill::importBillRow(
                                $metodoGeneracion, 0, $nombreProveedor, $codigoProveedor, $tipoPersona, $identificacionProveedor, $correoProveedor, $telefonoProveedor,
                                $claveFactura, $consecutivoComprobante, $condicionVenta, $metodoPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                                $idMoneda, $tipoCambio, $totalDocumento, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, true, true
                            );
                            
                            if( $insert ) {
                                array_push( $inserts, $insert );
                            }
                        }
                        
                        BillItem::insert($inserts);
                    });
                    
                }
            }catch( \ErrorException $ex ){
                return back()->withError('Por favor verifique que su documento de excel contenga todas las columnas indicadas. Error en la fila. '.$i.'. Mensaje:' . $ex->getMessage());
            }catch( \InvalidArgumentException $ex ){
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Por favor verifique que los campos de fecha estén correctos. Formato: "dd/mm/yyyy : 01/01/2018"');
            }catch( \Exception $ex ){
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. '.$i.'. Mensaje:' . $ex->getMessage());
            }catch( \Throwable $ex ){
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. '.$i.'. Mensaje:' . $ex->getMessage());
            }
        
            $company->save();
            
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
        }else{
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 2500 facturas por archivo.');
        }
        
    }
    
    public function importXML() {
        request()->validate([
          'xmls' => 'required'
        ]);
          
        try {  
            $time_start = getMicrotime();
            $company = currentCompanyModel();
            if( request()->hasfile('xmls') ) {
                foreach(request()->file('xmls') as $file) {
                    $xml = simplexml_load_string( file_get_contents($file) );
                    $json = json_encode( $xml ); // convert the XML string to JSON
                    $arr = json_decode( $json, TRUE );
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $arr['Receptor']['Identificacion']['Numero'] ) ) {
                        $this->saveBillXML( $arr, 'XML' );
                    }else{
                        return back()->withError( 'La factura subida no le pertenece a su compañía actual.' );
                    }
                }
            }
            $company->save();
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
        }catch( \Exception $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Mensaje:' . $ex->getMessage());
        }catch( \Throwable $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Mensaje:' . $ex->getMessage());
        }
        
        return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
        
    }
    
    public function receiveEmailBills(Request $request) {
        $file = $request->file('attachment1');
        
        try {  
            Log::info( "Se recibió una factura por correo electrónico." );
            $xml = simplexml_load_string( file_get_contents($file) );
            $json = json_encode( $xml ); // convert the XML string to JSON
            $arr = json_decode( $json, TRUE );
            
            $this->saveBillXML( $arr, 'Email' );
            
            $path = \Storage::putFile(
                "correos", $file
            );
        }catch( \Exception $ex ){
            Log::error( "Hubo un error al guardar la factura. Mensaje:" . $ex->getMessage());
        }catch( \Throwable $ex ){
            Log::error( "Hubo un error al guardar la factura. Mensaje:" . $ex->getMessage());
        }
        
        return response()->json([
            'path' => $path,
            'success' => 'Exito',
            'error' => 'Resource not found'
        ], 200);
    }
    
    
    public function saveBillXML( $arr, $metodoGeneracion ) {
        $inserts = array();
        
        $claveFactura = $arr['Clave'];
        $consecutivoComprobante = $arr['NumeroConsecutivo'];
        $fechaEmision = Carbon::createFromFormat('Y-m-d', substr($arr['FechaEmision'], 0, 10))->format('d/m/Y');
        $fechaVencimiento = $fechaEmision;
        $nombreProveedor = $arr['Emisor']['Nombre'];
        $codigoProveedor = '';
        $tipoPersona = $arr['Emisor']['Identificacion']['Tipo'];
        $identificacionProveedor = $arr['Emisor']['Identificacion']['Numero'];
        $correoProveedor = $arr['Emisor']['CorreoElectronico'];
        $telefonoProveedor = $arr['Emisor']['Telefono']['NumTelefono'];
        $tipoIdReceptor = $arr['Receptor']['Identificacion']['Tipo'];
        $numIdReceptor = $arr['Receptor']['Identificacion']['Numero'];
        $nombreReceptor = $arr['Receptor']['Nombre'];
        $condicionVenta = array_key_exists('CondicionVenta', $arr) ? $arr['CondicionVenta'] : '';
        $plazoCredito = array_key_exists('PlazoCredito', $arr) ? $arr['PlazoCredito'] : '';
        $medioPago = array_key_exists('MedioPago', $arr) ? $arr['MedioPago'] : '';
        $idMoneda = $arr['ResumenFactura']['CodigoMoneda'];
        $tipoCambio = $arr['ResumenFactura']['TipoCambio'];
        $totalDocumento = $arr['ResumenFactura']['TotalComprobante'];
        $tipoDocumento = '01';
        $descripcion = $arr['ResumenFactura']['CodigoMoneda'];
        
        $authorize = true;
        if( $metodoGeneracion == "Email" || $metodoGeneracion == "XML-A" ) {
            $authorize = false;
        }
        
        $lineas = $arr['DetalleServicio']['LineaDetalle'];
        //Revisa si es una sola linea. Si solo es una linea, lo hace un array para poder entrar en el foreach.
        if( array_key_exists( 'NumeroLinea', $lineas ) ) {
            $lineas = [$arr['DetalleServicio']['LineaDetalle']];
        }
        
        foreach( $lineas as $linea ) {
            $numeroLinea = $linea['NumeroLinea'];
            $codigoProducto = array_key_exists('Codigo', $linea) ? $linea['Codigo']['Codigo'] : '';
            $detalleProducto = $linea['Detalle'];
            $unidadMedicion = $linea['UnidadMedida'];
            $cantidad = $linea['Cantidad'];
            $precioUnitario = (float)$linea['PrecioUnitario'];
            $subtotalLinea = (float)$linea['SubTotal'];
            $totalLinea = (float)$linea['MontoTotalLinea'];
            $montoDescuento = array_key_exists('MontoDescuento', $linea) ? (float)$linea['MontoDescuento']['Codigo'] : 0;
            $codigoEtax = '103'; //De momento asume que todo en 4.2 es al 13%.
            $montoIva = 0; //En 4.2 toma el IVA como en 0. A pesar de estar con cod. 103.
            
            $insert = Bill::importBillRow(
                $metodoGeneracion, $numIdReceptor, $nombreProveedor, $codigoProveedor, $tipoPersona, $identificacionProveedor, $correoProveedor, $telefonoProveedor,
                $claveFactura, $consecutivoComprobante, $condicionVenta, $medioPago, $numeroLinea, $fechaEmision, $fechaVencimiento,
                $idMoneda, $tipoCambio, $totalDocumento, $tipoDocumento, $codigoProducto, $detalleProducto, $unidadMedicion,
                $cantidad, $precioUnitario, $subtotalLinea, $totalLinea, $montoDescuento, $codigoEtax, $montoIva, $descripcion, $authorize, false
            );
            
            if( $insert ) {
                array_push( $inserts, $insert );
            }
        }
        
        BillItem::insert($inserts);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAccepts()
    {
        return view('Bill/index-accepts');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataAccepts() {
        $current_company = currentCompany();

        $query = Bill::where('bills.company_id', $current_company)->where('is_void', false)->where('is_authorized', false)->where('is_totales', false)->with('provider');
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($bill) {
                return view('Bill.ext.accept-actions')->render();
            }) 
            ->editColumn('provider', function(Bill $bill) {
                return $bill->provider->fullname;
            })
            ->editColumn('generated_date', function(Bill $bill) {
                return $bill->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
    
}
