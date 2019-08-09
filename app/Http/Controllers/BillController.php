<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\UnidadMedicion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\BillUtils;
use \Carbon\Carbon;
use App\Bill;
use App\BillItem;
use App\Company;
use App\Provider;
use App\Actividades;
use App\CalculatedTax;
use App\CodigoIvaSoportado;
use App\ProductCategory;
use App\Http\Controllers\CacheController;
use App\Exports\BillExport;
use App\Imports\BillImport;
use Maatwebsite\Excel\Facades\Excel;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

/**
 * @group Controller - Facturas de compra
 *
 * Funciones de BillController
 */
class BillController extends Controller
{
  
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['receiveEmailBills']] );
    }
  
    /**
     * Index
     * Index de facturas recibidas. Usa indexData para cargar las facturas con AJAX
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Bill/index');
    }
    
    /**
     * Index Data
     * Funcion AJAX para cargar data de las facturas.
     * @bodyParam filtro Campo de filtro por tipo de documento. Por defecto es un 01. Si recibe 0 devuelve las eliminadas.
     * @return \Illuminate\Http\Response
     */
    public function indexData( Request $request ) {
        $current_company = currentCompany();

        $query = Bill::where('bills.company_id', $current_company)
                ->where('is_void', false)
                ->where('is_authorized', true)
                ->where('is_code_validated', true)
                ->where('is_totales', false)
                ->where('accept_status', 1)
                ->with('provider');
        
        $filtro = $request->get('filtro');
        if( $filtro == 0 ) {
            $query = $query->onlyTrashed();
        }else if( $filtro == 1 ) {
            $query = $query->where('document_type', '01');
        }else if( $filtro == 2 ) {
            $query = $query->where('document_type', '02');
        }else if( $filtro == 3 ) {
            $query = $query->where('document_type', '03');
        }else if( $filtro == 4 ) {
            $query = $query->where('document_type', '04');
        }             
                
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($bill) {
                $oficialHacienda = false;
                if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
                    $oficialHacienda =  true;
                }
                return view('Bill.ext.actions', [
                    'oficialHacienda' => $oficialHacienda,
                    'data' => $bill
                ])->render();
            }) 
            ->editColumn('moneda', function($bill) {
                return $bill->currency == 'CRC' ? $bill->currency : "$bill->currency ($bill->currency_rate)";
            })
            ->editColumn('hacienda_status', function( $bill) {
                if ($bill->hacienda_status == '03') {
                    return '<div class="green">  <span class="tooltiptext">Aceptada</span></div>
                        <a href="/facturas-recibidas/query-bill/'.$bill->id.'". title="Consultar factura en hacienda" class="text-dark mr-2 hidden"> 
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                          </a>';
                }
                if ($bill->hacienda_status == '04') {
                    return '<div class="red"> <span class="tooltiptext">Rechazada</span></div>
                        <a href="/facturas-recibidas/query-bill/'.$bill->id.'". title="Consultar factura en hacienda" class="text-dark mr-2 hidden"> 
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>';
                }
                return '<div class="yellow"><span class="tooltiptext">Procesando...</span></div>
                    <a href="/facturas-recibidas/query-bill/'.$bill->id.'". title="Consultar factura en hacienda" class="text-dark mr-2 hidden"> 
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                      </a>';
            })
            ->editColumn('provider', function(Bill $bill) {
                return $bill->providerName();
            })
            ->editColumn('document_type', function(Bill $bill) {
                return $bill->documentTypeName();
            })
            ->editColumn('generated_date', function(Bill $bill) {
                return $bill->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions', 'hacienda_status'])
            ->toJson();
    }

    /**
     * Crear factura existente
     * Muestra la pantalla para crear facturas existentes
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = currentCompanyModel();
        
        $units = UnidadMedicion::all()->toArray();
        $arrayActividades = $company->getActivities();
        
        return view("Bill/create", compact('units', 'arrayActividades') );
    }

    /**
     * Guardar factura existente
     * Store a newly created resource in storage.
     *
     * @bodyParam document_key required Clave de documento
     * @bodyParam document_number required Consecutivo de documento
     * @bodyParam sale_condition required Condición de venta de Hacienda
     * @bodyParam payment_type required Método de pago
     * @bodyParam retention_percent required Porcentaje de retención aplicado
     * @bodyParam credit_time required Plazo de crédito
     * @bodyParam buy_order required Órden de compra
     * @bodyParam other_reference required Referencias
     * @bodyParam send_emails required Correos electrónicos separados por coma
     * @bodyParam commercial_activity required Actividad comercial asignada
     * @bodyParam description required Descripción/Notas de la factura
     * @bodyParam currency required Moneda, ejemplo: USD o CRC
     * @bodyParam currency_rate required Tipo de cambio. Por defecto e 1
     * @bodyParam subtotal required Subtotal de la factura
     * @bodyParam total required Total de la factura
     * @bodyParam iva_amount required Monto correspondiente al IVA
     * @bodyParam generated_date required Fecha de generacion
     * @bodyParam hora required Hora de generación
     * @bodyParam due_date required Fecha de vencimiento
     * @bodyParam items required Array con item_number, code, name, product_type, measure_unit, item_count, unit_price, subtotal, total, discount_type, discount, iva_type, iva_percentage, iva_amount, tariff_heading, is_exempt
     * @bodyParam client_id required ID del cliente. Usar -1 si desea crear uno nuevo
     * @bodyParam tipo_persona required No obligatorio. Tipo de persona de proveedor nuevo
     * @bodyParam id_number required No obligatorio. Cédula de proveedor nuevo
     * @bodyParam code required No obligatorio. Código de proveedor nuevo
     * @bodyParam email required No obligatorio. Correo proveedor nuevo
     * @bodyParam billing_emails required No obligatorio. Lista de correos separados por coma de proveedor nuevo
     * @bodyParam first_name required No obligatorio. Primer nombre proveedor nuevo
     * @bodyParam last_name required No obligatorio. Apellido de proveedor nuevo
     * @bodyParam last_name2 required No obligatorio. Segundo apellido de proveedor nuevo
     * @bodyParam country required No obligatorio. País de proveedor nuevo
     * @bodyParam state required No obligatorio. Provincia de proveedor nuevo
     * @bodyParam city required No obligatorio. Cantón de provee dor nuevo
     * @bodyParam district required No obligatorio. Distrito de proveedor nuevo
     * @bodyParam neighborhood required No obligatorio. Barrio de proveedor nuevo
     * @bodyParam zip required No obligatorio. Código postal de proveedor nuevo
     * @bodyParam address required No obligatorio. Dirección de proveedor nuevo
     * @bodyParam phone required No obligatorio. Teléfono de proveedor nuevo
     * @bodyParam es_exento required No obligatorio. Indicar si es exento o no.
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $company = currentCompanyModel();
        $request->validate([
            'subtotal' => 'required',
            'items' => 'required',
        ]);
        if(CalculatedTax::validarMes($request->generated_date)){
            $bill = new Bill();
            $bill->company_id = $company->id;
            //Datos generales y para Hacienda
            $bill->document_type = "01";
            $bill->hacienda_status = "03";
            $bill->status = "02";
            $bill->payment_status = "01";
            $bill->payment_receipt = "";
            $bill->generation_method = "M";
            $bill->reference_number = $company->last_bill_ref_number + 1;
            $bill->accept_status = 1;
            
            $bill->setBillData($request);
            
            $company->last_bill_ref_number = $bill->reference_number;
            $company->save();
            
            clearBillCache($bill);
            return redirect('/facturas-recibidas');
            
        }else{
            return redirect('/facturas-recibidas')->withError('Mes seleccionado ya fue cerrado');
        }
    }

    /**
     * Mostrar factura existente
     * Display the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = currentCompanyModel();
        
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
        
        $units = UnidadMedicion::all()->toArray();
        $arrayActividades = $company->getActivities();
      
        return view('Bill/show', compact('bill', 'units', 'arrayActividades') );
    }

    /**
     * Editar factura
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = currentCompanyModel();
        
        $bill = Bill::findOrFail($id);
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
            $units = UnidadMedicion::all()->toArray();
            $this->authorize('update', $bill);
          
            //Valida que la factura recibida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
            if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
              return redirect('/facturas-recibidas');
            } 
            $arrayActividades = $company->getActivities();

        return view('Bill/edit', compact('bill', 'units', 'arrayActividades') );
        }else{
            return redirect('/facturas-recibidas')->withError('Mes seleccionado ya fue cerrado');
        }
    }

    /**
     * Actualizar factura
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $bill = Bill::findOrFail($id);
        if(CalculatedTax::validarMes($request->generated_date)){
            $this->authorize('update', $bill);
          
            //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
            if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
              return redirect('/facturas-emitidas');
            }
          
            $bill->setBillData($request);
            
            clearBillCache($bill);
        
        }else{
            return redirect('/facturas-recibidas')->withError('Mes seleccionado ya fue cerrado');
        }
        return redirect('/facturas-recibidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function anular($id)
    {
        return redirect('/facturas-recibidas');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
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
        
        if( $collection[0]->count() < 5001 ){
            try {
                foreach ($collection[0]->chunk(200) as $facturas) {
                    \DB::transaction(function () use ($facturas, &$company, &$i) {
                        
                        $inserts = array();
                        foreach ($facturas as $row){
                            if($row['consecutivocomprobante']) {
                                $i++;

                                $metodoGeneracion = "XLSX";

                                //Datos de proveedor
                                $nombreProveedor = $row['nombreproveedor'];
                                $codigoProveedor = $row['codigoproveedor'] ? $row['codigoproveedor'] : '';
                                $tipoPersona = $row['tipoidentificacion'];
                                $identificacionProveedor = $row['identificacionproveedor'];
                                $correoProveedor = $row['correoproveedor'];
                                $telefonoProveedor = null;

                                //Datos de factura
                                $consecutivoComprobante = $row['consecutivocomprobante'];
                                $claveFactura = array_key_exists('clavefactura', $row) ? $row['clavefactura'] : '';
                                $condicionVenta = str_pad($row['condicionventa'], 2, '0', STR_PAD_LEFT);
                                $metodoPago = str_pad($row['metodopago'], 2, '0', STR_PAD_LEFT);
                                $numeroLinea = array_key_exists('numerolinea', $row) ? $row['numerolinea'] : 1;
                                $fechaEmision = $row['fechaemision'];
                                $fechaVencimiento = array_key_exists('fechavencimiento', $row) ? $row['fechavencimiento'] : $fechaEmision;
                                $moneda = $row['moneda'];
                                $tipoCambio = $row['tipocambio'];
                                $totalDocumento = $row['totaldocumento'];
                                $tipoDocumento = $row['tipodocumento'];
                                $descripcion = array_key_exists('descripcion', $row) ? $row['descripcion'] : '';

                                //Datos de linea
                                $codigoProducto = $row['codigoproducto'];
                                $detalleProducto = $row['detalleproducto'];
                                $unidadMedicion = $row['unidadmedicion'];
                                $cantidad = array_key_exists('cantidad', $row) ? $row['cantidad'] : 1;
                                $precioUnitario = $row['preciounitario'];
                                $subtotalLinea = (float)$row['subtotallinea'];
                                $totalLinea = $row['totallinea'];
                                $montoDescuento = array_key_exists('montodescuento', $row) ? $row['montodescuento'] : 0;
                                $codigoEtax = $row['codigoivaetax'];
                                $montoIva = (float)$row['montoiva'];

                                $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
                                $codigoActividad = $row['actividadcomercial'] ?? $mainAct;
                                $xmlSchema = $row['xmlschema'] ?? 42;

                                //Datos de exoneracion
                                $totalNeto = 0;
                                $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                                $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                                $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                                $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                                $montoExoneracion = $row['montoexoneracion'] ?? 0;
                                $impuestoNeto = $row['impuestoneto'] ?? 0;
                                $totalMontoLinea = $row['totalmontolinea'] ?? 0;

                                $codigoEtax = str_pad($codigoEtax, 3, '0', STR_PAD_LEFT);

                                $arrayImportBill = array(
                                    'metodoGeneracion' => $metodoGeneracion,
                                    'idReceptor' => 0,
                                    'nombreProveedor' => $nombreProveedor,
                                    'codigoProveedor' => $codigoProveedor,
                                    'tipoPersona' => $tipoPersona,
                                    'identificacionProveedor' => $identificacionProveedor,
                                    'correoProveedor' => $correoProveedor,
                                    'telefonoProveedor' => $telefonoProveedor,
                                    'claveFactura' => $claveFactura,
                                    'consecutivoComprobante' => $consecutivoComprobante,
                                    'condicionVenta' => $condicionVenta,
                                    'metodoPago' => $metodoPago,
                                    'numeroLinea' => $numeroLinea,
                                    'fechaEmision' => $fechaEmision,
                                    'fechaVencimiento' => $fechaVencimiento,
                                    'moneda' => $moneda,
                                    'tipoCambio' => $tipoCambio,
                                    'totalDocumento' => $totalDocumento,
                                    'totalNeto' => $totalNeto,
                                    'tipoDocumento' => $tipoDocumento,
                                    'codigoProducto' => $codigoProducto,
                                    'detalleProducto' => $detalleProducto,
                                    'unidadMedicion' => $unidadMedicion,
                                    'cantidad' => $cantidad,
                                    'precioUnitario' => $precioUnitario,
                                    'subtotalLinea' => $subtotalLinea,
                                    'totalLinea' => $totalLinea,
                                    'montoDescuento' => $montoDescuento,
                                    'codigoEtax' => $codigoEtax,
                                    'montoIva' => $montoIva,
                                    'descripcion' => $descripcion,
                                    'isAuthorized' => true,
                                    'codeValidated' => true,
                                    'tipoDocumentoExoneracion' => $tipoDocumentoExoneracion,
                                    'documentoExoneracion' => $documentoExoneracion,
                                    'companiaExoneracion' => $companiaExoneracion,
                                    'porcentajeExoneracion' => $porcentajeExoneracion,
                                    'montoExoneracion' => $montoExoneracion,
                                    'impuestoNeto' => $impuestoNeto,
                                    'totalMontoLinea' => $totalMontoLinea,
                                    'xmlSchema' => $xmlSchema,
                                    'codigoActividad' => $codigoActividad
                                );

                                $insert = Bill::importBillRow($arrayImportBill);

                                if ($insert) {
                                    array_push($inserts, $insert);
                                }
                            }
                        }
                        
                        BillItem::insert($inserts);
                    });
                    
                }
            }catch( \ErrorException $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError('Por favor verifique que su documento de excel contenga todas las columnas indicadas. Error en la fila. '.$i);
            }catch( \InvalidArgumentException $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Ha ocurrido un error al subir su archivo. Por favor verifique que los campos de fecha estén correctos. Formato: "dd/mm/yyyy : 01/01/2018"');
            }catch( \Exception $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Linea: '.$i);
            }catch( \Throwable $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Linea: '.$i);
            }
        
            $company->save();
            
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
        }else{
            return redirect('/facturas-recibidas')->withError('Usted tiene un límite de 5000 facturas por archivo.');
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

                    $FechaEmision = explode("T", $arr['FechaEmision']);
                    $FechaEmision = explode("-", $FechaEmision[0]);
                    $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];

                    if(CalculatedTax::validarMes($FechaEmision)){
                        $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
                        $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                        $consecutivoComprobante = $arr['NumeroConsecutivo'];
                        $clave = $arr['Clave'];
                        
                        //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                        if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                            //Registra el XML. Si todo sale bien, lo guarda en S3
                            $bill = Bill::saveBillXML( $arr, 'XML' );
                            if( $bill ) {
                                Bill::storeXML( $bill, $file );
                            }
                        }else{
                            return back()->withError( "El documento $consecutivoComprobante no le pertenece a su compañía actual" );
                        }
                    }else{
                        return back()->withError('Mes seleccionado ya fue cerrado');
                    }
                }
            }
            $company->save();
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
        }catch( \Exception $ex ){
            Log::error('Error importando XML ' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.');
        }catch( \Throwable $ex ){
            Log::error('Error importando XML ' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.');
        }

        return redirect('/facturas-recibidas/aceptaciones')->withMessage('Facturas importados exitosamente en '.$time.'s');

    }
    
    
    /**
     * Despliega las facturas que requieren validación de códigos
     *
     * @return \Illuminate\Http\Response
     */
    public function indexValidaciones()
    {
        $current_company = currentCompany();
        $bills = Bill::where('company_id', $current_company)
                        ->where('is_void', false)
                        ->where('is_totales', false)
                        ->where('is_code_validated', false)
                        ->where('is_authorized', true)
                        ->orderBy('generated_date', 'DESC')
                        ->orderBy('reference_number', 'DESC')->paginate(10);
        return view('Bill/index-validaciones', [
          'bills' => $bills
        ]);
    }



    public function validar($id){
        $current_company = currentCompany();
        $bill = Bill::find($id);
            $company = Company::select('commercial_activities')->where('id', $current_company)->first();
            $activities_company = explode(", ", $company->commercial_activities);
            $commercial_activities = Actividades::whereIn('codigo', $activities_company)->get();
            $codigos_etax = CodigoIvaSoportado::get();
            $categoria_productos = ProductCategory::whereNotNull('bill_iva_code')->get();

            return view('Bill/validar', compact('bill', 'commercial_activities', 'codigos_etax', 'categoria_productos'));
        
    }

    public function GuardarValidar(Request $request)
    {
        $bill = Bill::findOrFail($request->bill);
        
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
            $bill->activity_company_verification = $request->actividad_comercial;
            $bill->is_code_validated = true;
            foreach( $request->items as $item ) {
                BillItem::where('id', $item['id'])
                ->update([
                  'iva_type' =>  $item['iva_type'],
                  'product_type' =>  $item['product_type'],
                  'porc_identificacion_plena' =>  $item['porc_identificacion_plena']
                ]);
            }
            
            $bill->save();
            
            clearBillCache($bill);

            return back()->withMessage( 'La factura '. $bill->document_number . ' ha sido validada');
        }else{
            return back()->withError('Mes seleccionado ya fue cerrado');
        }

    }
    
    public function hideBill ( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
        
        if ( $request->hide_from_taxes ) {
            $bill->hide_from_taxes = true;
            $bill->save();
            clearBillCache($bill);
            return redirect('/facturas-recibidas')->withMessage( 'La factura '. $bill->document_number . ' se ha ocultado para cálculo de IVA.');
        }else{
            $bill->hide_from_taxes = false;
            $bill->save();
            clearBillCache($bill);
            return redirect('/facturas-recibidas')->withMessage( 'La factura '. $bill->document_number . ' se ha incluido nuevamente para cálculo de IVA.');
        }
    }
    
    public function confirmarValidacion( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
            $this->authorize('update', $bill);
            
            $tipoIva = $request->tipo_iva;
            foreach( $bill->items as $item ) {
                $item->iva_type = $request->tipo_iva;
                $item->save();
            }
            
            $bill->is_code_validated = true;
            $bill->save();
            
            if( $bill->year == 2018 ) {
                clearLastTaxesCache($bill->company->id, 2018);
            }
            clearBillCache($bill);
            
            return redirect('/facturas-recibidas/aceptaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido validada');
        }else{
            return redirect('/facturas-recibidas/aceptaciones')->withError('Mes seleccionado ya fue cerrado');
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAuthorize()
    {
        return view('Bill/index-autorizaciones');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDataAuthorize() {
        $current_company = currentCompany();

        $query = Bill::where('bills.company_id', $current_company)
        ->where('is_void', false)
        ->where('is_authorized', false)
        ->where('is_totales', false)
        ->with('provider');
        
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($bill) {
                return view('Bill.ext.auth-actions', [
                    'id' => $bill->id,
                    'is_code_validated' => $bill->is_code_validated
                ])->render();
            }) 
            ->editColumn('provider', function(Bill $bill) {
                return $bill->provider->getFullName();
            })
            ->editColumn('generated_date', function(Bill $bill) {
                return $bill->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
    
    public function authorizeBill ( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
        
            $this->authorize('update', $bill);
            
            if ( $request->autorizar ) {
                $bill->is_authorized = true;
                $bill->save();
                return redirect('/facturas-recibidas/autorizaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido autorizada. Recuerde validar el código');
            }else {
                $bill->is_authorized = false;
                $bill->is_void = true;
                BillItem::where('bill_id', $bill->id)->delete();
                $bill->delete();
                return redirect('/facturas-recibidas/autorizaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido rechazada');
            }

        }else{
            return redirect('/facturas-recibidas/autorizaciones')->withError('Mes seleccionado ya fue cerrado');
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAccepts()
    {
        $query = Bill::where('bills.company_id', currentCompany())
        ->where('is_void', false)
        ->where('accept_status', '0')
        ->where('is_totales', false)
        ->where('is_authorized', true)
        ->with('provider')->get();
        
        foreach ($query as $bill) {
            $bill->calculateAcceptFields();
        }
        
        $current_company = currentCompanyModel();

        if( $current_company->use_invoicing ) {
            if ($current_company->atv_validation == false) {
                $apiHacienda = new BridgeHaciendaApi();
                $token = $apiHacienda->login(false);
                $validateAtv = $apiHacienda->validateAtv($token, $current_company);
    
                if($validateAtv) {
                    if ($validateAtv['status'] == 400) {
                        Log::info('Atv Not Validated Company: '. $current_company->id_number);
                        if (strpos($validateAtv['message'], 'ATV no son válidos') !== false) {
                            $validateAtv['message'] = "Los parámetros actuales de acceso a ATV no son válidos";
                        }
                        return redirect('/empresas/certificado')->withError( "Error al validar el certificado: " . $validateAtv['message']);
    
                    } else {
                        Log::info('Atv Validated Company: '. $current_company->id_number);
                        $current_company->atv_validation = true;
                        $current_company->save();
                    }
                }else {
                    return redirect('/empresas/certificado')->withError( 'Hubo un error al validar su certificado digital. Verifique que lo haya ingresado correctamente. Si cree que está correcto, ' );
                }
            }
        }else{
            return view('Bill/index-aceptaciones-hacienda')->withMessage('Usted no tiene un facturación con eTax activada, por lo que esta pantalla únicamente validará los códigos eTax para cálculo y no realizará aceptaciones con Hacienda.');
        }

        if ($current_company->last_rec_ref_number === null) {
            return redirect('/empresas/configuracion')->withError( "No ha ingresado ultimo consecutivo de recepcion");
        }
        
        return view('Bill/index-aceptaciones-hacienda');
    }
    
    /**
     * Returns the required ajax data.
     * @return \Illuminate\Http\Response
     */
    public function indexDataAccepts() {
        $current_company = currentCompany();
        $query = Bill::where('bills.company_id', $current_company)
        ->where('is_void', false)
        ->where('accept_status', '0')
        ->where('is_totales', false)
        ->where('is_authorized', true)
        ->with('provider');

        return datatables()->eloquent( $query )
            ->addColumn('actions', function($bill) {
                return view('Bill.ext.accept-actions', [
                    'bill' => $bill
                ])->render();
            }) 
            ->editColumn('total', function(Bill $bill) {
                $total = number_format($bill->total,2);
                return "$bill->currency $total";
            })
            ->editColumn('accept_total_factura', function(Bill $bill) {
                return $bill->total * $bill->currency_rate;
            })
            ->editColumn('accept_iva_total', function(Bill $bill) {
                return $bill->iva_amount;
            })
            ->editColumn('accept_iva_acreditable', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  $bill->accept_iva_acreditable;
            })
            ->editColumn('accept_iva_gasto', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  $bill->accept_iva_gasto;
            })
            ->editColumn('provider', function(Bill $bill) {
                return $bill->provider->getFullName();
            })
            ->editColumn('generated_date', function(Bill $bill) {
                return $bill->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
    
    public function indexAcceptsOther()
    {
        $bills = Bill::where('bills.company_id', currentCompany())
        ->where('is_void', false)
        ->where('accept_status', '0')
        ->where('is_totales', false)
        ->where('is_authorized', true)
        ->with('provider')->paginate(10);
        
        return view('Bill/index-aceptaciones-otros', [
          'bills' => $bills
        ]);
    }
    
    public function markAsNotAccepted ( $id )
    {
        $bill = Bill::findOrFail($id);
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
        
            $this->authorize('update', $bill);
            
            $bill->accept_status = 0;
            $bill->is_code_validated = false;
            $bill->save();
            return redirect('/facturas-recibidas/aceptaciones')->withMessage( 'La factura '. $bill->document_number . ' ha sido incluida para aceptación');
        }else{
            return redirect('/facturas-recibidas/aceptaciones')->withError('Mes seleccionado ya fue cerrado');
        }

    }
    
    
    /**
     *  Metodo para hacer las aceptaciones
     */
    public function sendAcceptMessage (Request $request, $id)
    {
        try {
            $company = currentCompanyModel();
            $bill = Bill::findOrFail($id);
            if( currentCompanyModel()->use_invoicing ) {
                $apiHacienda = new BridgeHaciendaApi();
                $tokenApi = $apiHacienda->login(false);
                if ($tokenApi !== false) {
                    if (!empty($bill)) {
                        $bill->accept_status = $request->respuesta;
                        $bill->save();
                        $company->last_rec_ref_number = $company->last_rec_ref_number + 1;
                        $company->save();
                        $company->last_document_rec = getDocReference('05',$company->last_rec_ref_number);
                        $company->save();
                        $apiHacienda->acceptInvoice($bill, $tokenApi);
                    }
                    $mensaje = 'Aceptación enviada.';
                    if($request->respuesta == 2){
                        $mensaje = 'Rechazo de factura enviado';
                    }
                    clearBillCache($bill);
                    return redirect('/facturas-recibidas/aceptaciones')->withMessage( $mensaje );
    
                } else {
                    return back()->withError( 'Ha ocurrido un error al enviar factura.' );
                }
            } else {
                if (!empty($bill)) {
                    $bill->accept_status = $request->respuesta;
                    $bill->save();
                }
                clearBillCache($bill);
                return redirect('/facturas-recibidas/aceptaciones')->withMessage('Factura aceptada para cálculo en eTax.');
            }
        } catch ( Exception $e) {
            Log::error ("Error al crear aceptacion de factura");
            return redirect('/facturas-recibidas/aceptaciones')->withError( 'La factura no pudo ser aceptada. Por favor contáctenos.');
        }
    }
    
    public function correctAccepted ( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
        $FechaEmision = explode(" ", $bill->generated_date);
        $FechaEmision = explode("-", $FechaEmision[0]);
        $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
        if(CalculatedTax::validarMes($FechaEmision)){
            $this->authorize('update', $bill);
            
            $bill->accept_iva_condition = $request->accept_iva_condition ? $request->accept_iva_condition : '02';
            $bill->accept_iva_acreditable = $request->accept_iva_acreditable;
            $bill->accept_iva_gasto = $request->accept_iva_gasto;
            $bill->accept_status = 1;
            $bill->hacienda_status = "01";
            $bill->accept_id_number = currentCompany();

            $bill->save();
            clearBillCache($bill);
            return redirect('/facturas-recibidas/aceptaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido aceptada');
        }else{
            return redirect('/facturas-recibidas/aceptaciones')->withError('Mes seleccionado ya fue cerrado');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
        clearBillCache($bill);
        BillItem::where('bill_id', $bill->id)->delete();
        $bill->delete();
        
        return redirect('/facturas-recibidas')->withMessage('La factura ha sido eliminada satisfactoriamente.');
    } 
    
    /**
     * Restore the specific item
     *
     * @param  \App\Bill  $bill
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $bill = Bill::onlyTrashed()->where('id', $id)->first();
        if( $bill->company_id != currentCompany() ){
            return 404;
        }
        $bill->restore();
        BillItem::onlyTrashed()->where('bill_id', $bill->id)->restore();
        clearBillCache($bill);
        
        return redirect('/facturas-recibidas')->withMessage('La factura ha sido restaurada satisfactoriamente.');
    }  
    
    public function fixImports() {
        $billUtils = new BillUtils();
        $bills = Bill::where('generation_method', 'Email')->orWhere('generation_method', 'XML')->get();

        foreach($bills as $bill) {
            try {
            if( !$bill->provider_zip ){
                $file = $billUtils->downloadXml( $bill, $bill->company_id );
                if($file) {
                    $xml = simplexml_load_string($file);
                    $json = json_encode( $xml ); // convert the XML string to JSON
                    $arr = json_decode( $json, TRUE );
                    $bill = Bill::saveBillXML( $arr, $bill->generation_method );
                }
            }
            }catch(\Throwable $e){
                Log::warning("No se pudo arreglar la $bill->id " . $e->getMessage());
            }
        }
        
        return true;
    }
    
    public function queryBill($id) {
        try {
            /*
            $bill = Bill::findOrFail($id);
            $this->authorize('update', $bill);

            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);

            if ($tokenApi !== false) {
                $company = currentCompanyModel();
                $result = $apiHacienda->queryHacienda($bill, $tokenApi, $company);
                if ($result == false) {
                    return redirect()->back()->withErrors('El servidor de Hacienda es inaccesible en este momento, o el comprobante no ha sido recibido. Por favor intente de nuevo más tarde o contacte a soporte.');
                }
                $filename = 'AHC-'.$bill->document_key . '.xml';
                if( ! $bill->document_key ) {
                    $filename = $bill->document_number . '-' . $bill->provider_id . '.xml';
                }
                $headers = [
                    'Content-Type' => 'application/xml',
                    'Content-Description' => 'File Transfer',
                    'Content-Disposition' => "attachment; filename={$filename}",
                    'filename'=> $filename
                ];
                return response($result, 200, $headers);
            }
            */
        } catch (\Exception $e) {
            Log::error("Error consultado factura -->" .$e);
            return redirect()->back()->withErrors('Error al consultar comprobante en hacienda');
        }
    }
    
}
