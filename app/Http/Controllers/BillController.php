<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\UnidadMedicion;
use App\Utils\BridgeHaciendaApi;
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
use Illuminate\Support\Facades\Storage;
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
            ->editColumn('provider', function(Bill $bill) {
                return $bill->providerName();
            })
            ->editColumn('document_type', function(Bill $bill) {
                return $bill->documentTypeName();
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
        $units = UnidadMedicion::all()->toArray();
        return view("Bill/create", ['units' => $units]);
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
        $units = UnidadMedicion::all()->toArray();
        $this->authorize('update', $bill);
      
        return view('Bill/show', compact('bill', 'units') );
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
        $units = UnidadMedicion::all()->toArray();
        $this->authorize('update', $bill);
      
        //Valida que la factura recibida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $bill->generation_method != 'M' && $bill->generation_method != 'XLSX' ){
          return redirect('/facturas-recibidas');
        } 
      
        return view('Bill/edit', compact('bill', 'units') );
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
    public function anular($id)
    {
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
                            $idMoneda = $row['moneda'];
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
                            $codigoEtax = $row['codigoivaetax'];
                            $montoIva = (float)$row['montoiva'];
                            
                            $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
                            $codigoActividad = $row['codigoactividad'] ?? $mainAct;
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
                                'idMoneda' => $idMoneda,
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
                            
                            $insert = Bill::importBillRow( $arrayImportBill );
                            
                            if( $insert ) {
                                array_push( $inserts, $insert );
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
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.'.$i);
            }catch( \Throwable $ex ){
                Log::error('Error importando Excel' . $ex->getMessage());
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.'.$i);
            }
        
            $company->save();
            
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-recibidas')->withMessage('Facturas importados exitosamente en '.$time.'s');
        }else{
            return redirect('/facturas-recibidas')->withError('Usted tiene un límite de 2500 facturas por archivo.');
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
                    
                    $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    $clave = $arr['Clave'];
                    
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionReceptor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3
                        if( Bill::saveBillXML( $arr, 'XML' ) ) {
                            $bill = Bill::where('company_id', $company->id)->where('document_key', $clave)->first();
                            Bill::storeXML( $bill, $file );
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
            Log::error('Error importando XML ' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.');
        }catch( \Throwable $ex ){
            Log::error('Error importando XML ' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.');
        }

        return redirect('/facturas-recibidas/validaciones')->withMessage('Facturas importados exitosamente en '.$time.'s');

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
    
    public function confirmarValidacion( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
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
        clearInvoiceCache($bill);
        
        return redirect('/facturas-recibidas/validaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido validada');
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
                    'id' => $bill->id
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

        if (empty($current_company->last_rec_ref_number)) {
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
                return "$bill->currency $bill->total";
            })
            ->editColumn('accept_total_factura', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  "$bill->accept_total_factura";
            })
            ->editColumn('accept_iva_total', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  "$bill->accept_iva_total";
            })
            ->editColumn('accept_iva_acreditable', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  "$bill->accept_iva_acreditable";
            })
            ->editColumn('accept_iva_gasto', function(Bill $bill) {
                return $bill->xml_schema == 42 ? 'N/A en 4.2' :  "$bill->accept_iva_gasto";
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
        $this->authorize('update', $bill);
        
        $bill->accept_status = 0;
        $bill->save();
        return redirect('/facturas-recibidas/aceptaciones')->withMessage( 'La factura '. $bill->document_number . ' ha sido incluida para aceptación');
    }
    
    
    /**
     *  Metodo para hacer las aceptaciones
     */
    public function sendAcceptMessage (Request $request, $id)
    {
        try {
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            if ($tokenApi !== false) {
                $bill = Bill::findOrFail($id);
                $company = currentCompanyModel();
                if (!empty($bill)) {
                    $bill->accept_status = $request->respuesta;
                    $bill->save();
                    $company->last_rec_ref_number = $company->last_rec_ref_number + 1;
                    $company->save();
                    $company->last_document_rec = getDocReference($company->last_rec_ref_number);
                    $company->save();

                    $apiHacienda->acceptInvoice($bill, $tokenApi);
                }
                clearInvoiceCache($bill);
                return redirect('/facturas-recibidas/aceptaciones')->withMessage('Acceptacion Enviada.');

            } else {
                return back()->withError( 'Ha ocurrido un error al enviar factura.' );
            }

        } catch ( Exception $e) {
            Log::error ("Error al crear aceptacion de factura");
            return redirect('/facturas-recibidas/aceptaciones')->withError( 'La factura no pudo ser aceptada. Por favor contáctenos.');
        }
    }
    
    public function correctAccepted ( Request $request, $id )
    {
        $bill = Bill::findOrFail($id);
        $this->authorize('update', $bill);
        
        $bill->accept_iva_acreditable = $request->accept_iva_acreditable;
        $bill->accept_iva_gasto = $request->accept_iva_gasto;
        $bill->accept_status = 1;
        $bill->hacienda_status = "01";
        $bill->accept_id_number = currentCompany();

        $bill->save();
        return redirect('/facturas-recibidas/aceptaciones')->withMessage( 'La factura '. $bill->document_number . 'ha sido aceptada');
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
    
}
