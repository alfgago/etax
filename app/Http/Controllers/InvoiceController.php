<?php

namespace App\Http\Controllers;

use App\Actividades;
use App\AvailableInvoices;
use App\CodigosPaises;
use App\UnidadMedicion;
use App\Utils\BridgeHaciendaApi;
use App\Utils\InvoiceUtils;
use \Carbon\Carbon;
use App\Invoice;
use App\InvoiceItem;
use App\Exports\InvoiceExport;
use App\Imports\InvoiceImport;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use GuzzleHttp\Message\ResponseInterface;
use PDF;


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
        $company = currentCompanyModel();
        if(empty($company->last_note_ref_number)){
            return redirect('/empresas/configuracion')->withErrors('No ha ingresado ultimo consecutivo de nota credito');
        }
        return view('Invoice/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData( Request $request ) {
        $current_company = currentCompany();

        $query = Invoice::where('invoices.company_id', $current_company)
                ->where('is_void', false)
                ->where('is_authorized', true)
                ->where('is_code_validated', true)
                ->where('is_totales', false)
                ->with('client');
                
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
        }else if( $filtro == 8 ) {
            $query = $query->where('document_type', '08');
        }else if( $filtro == 9 ) {
            $query = $query->where('document_type', '09');
        }             
                
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($invoice) {
                $oficialHacienda = false;
                if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
                    $oficialHacienda =  true;
                }
                return view('Invoice.ext.actions', [
                    'oficialHacienda' => $oficialHacienda,
                    'data' => $invoice
                ])->render();
            }) 
            ->editColumn('client', function(Invoice $invoice) {
                return !empty($invoice->client_first_name) ? $invoice->client_first_name.' '.$invoice->client_last_name : $invoice->clientName();
            })
            ->editColumn('hacienda_status', function(Invoice $invoice) {
                if ($invoice->hacienda_status == '03') {
                    return '<div class="green">  
                                <span class="tooltiptext">Aceptada</span>
                            </div>';
                }
                if ($invoice->hacienda_status == '04') {
                    return '<div class="red">  
                                <span class="tooltiptext">Rechazada</span>
                            </div>';
                }
                return '<div class="yellow">
                            <span class="tooltiptext">Creada</span>
                        </div>';
            })
            ->editColumn('document_type', function(Invoice $invoice) {
                return $invoice->documentTypeName();
            })
            ->editColumn('generated_date', function(Invoice $invoice) {
                return $invoice->generatedDate()->format('d/m/Y');
            })
            ->rawColumns(['actions', 'hacienda_status'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company = currentCompanyModel();
        //Revisa límite de facturas emitidas en el mes actual1
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $month = $start_date->month;
        $year = $start_date->year;
        $available_invoices = $company->getAvailableInvoices( $year, $month );
        
        $available_plan_invoices = $available_invoices->monthly_quota - $available_invoices->current_month_sent;
        if($available_plan_invoices < 1 && $company->additional_invoices < 1){
            return redirect()->back()->withError('Usted ha sobrepasado el límite de facturas mensuales de su plan actual.');
        }
        
        $arrayActividades = $company->getActivities();
    
        //Termina de revisar limite de facturas.
        $units = UnidadMedicion::all()->toArray();
        if( count($arrayActividades) == 0 ){
            return redirect('/empresas/editar')->withErrors('No ha definido una actividad comercial para esta empresa');
        }
        return view("Invoice/create-factura-manual", ['units' => $units])->with('arrayActividades', $arrayActividades);
    }

    /**
     * Muestra el formulario para emitir facturas
     *
     * @return \Illuminate\Http\Response
     */
    public function emitFactura($tipoDocumento)
    {
        $company = currentCompanyModel();

        //Revisa límite de facturas emitidas en el mes actual
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $month = $start_date->month;
        $year = $start_date->year;
        $available_invoices = $company->getAvailableInvoices( $year, $month );
        
        $available_plan_invoices = $available_invoices->monthly_quota - $available_invoices->current_month_sent;
        if($available_plan_invoices < 1 && $company->additional_invoices < 1){
            return redirect()->back()->withError('Usted ha sobrepasado el límite de facturas mensuales de su plan actual.');
        }
        //Termina de revisar limite de facturas.

        if ($company->atv_validation == false) {
            $apiHacienda = new BridgeHaciendaApi();
            $token = $apiHacienda->login(false);
            $validateAtv = $apiHacienda->validateAtv($token, $company);
            
            if( $validateAtv ) {
                if ($validateAtv['status'] == 400) {
                    Log::info('Atv Not Validated Company: '. $company->id_number);
                    if (strpos($validateAtv['message'], 'ATV no son válidos') !== false) {
                        $validateAtv['message'] = "Los parámetros actuales de acceso a ATV no son válidos";
                    }
                    return redirect('/empresas/certificado')->withError( "Error al validar el certificado: " . $validateAtv['message']);

                } else {
                    Log::info('Atv Validated Company: '. $company->id_number);
                    $company->atv_validation = true;
                    $company->save();
                }
            }else {
                return redirect('/empresas/certificado')->withError( 'Hubo un error al validar su certificado digital. Verifique que lo haya ingresado correctamente. Si cree que está correcto, ' );
            }
        }

        /*if( ! isset($company->logo_url) ){
            return redirect('/empresas/editar')->withError('Para poder emitir facturas, debe subir un logo y certificado ATV');
        }*/
        
        $units = UnidadMedicion::all()->toArray();
        $countries  = CodigosPaises::all()->toArray();

        $arrayActividades = $company->getActivities();
        
        if(count($arrayActividades) == 0){
            return redirect('/empresas/editar')->withError('No ha definido una actividad comercial para esta empresa');
        }

        if($company->last_note_ref_number == null) {
            return redirect('/empresas/configuracion')->withErrors('No ha ingresado ultimo consecutivo de nota credito');
        }
        return view("Invoice/create-factura", ['document_type' => $tipoDocumento, 'rate' => $this->get_rates(),

            'document_number' => $this->getDocReference($tipoDocumento),
            'document_key' => $this->getDocumentKey($tipoDocumento), 'units' => $units, 'countries' => $countries])->with('arrayActividades', $arrayActividades);
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
        $invoice->hacienda_status = "03";
        $invoice->payment_status = "01";
        $invoice->payment_receipt = "";
        $invoice->generation_method = "M";
        $invoice->setInvoiceData($request);
        
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
        //revision de branch para segmentacion de funcionalidades por tipo de documento

        try {
            Log::info("Envio de factura a hacienda -> ".json_encode($request->all()));
            $request->validate([
                'subtotal' => 'required',
                'items' => 'required',
            ]);

            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            if ($tokenApi !== false) {
                $invoice = new Invoice();
                $company = currentCompanyModel();
                $invoice->company_id = $company->id;

                //Datos generales y para Hacienda
                $invoice->document_type = $request->document_type;
                $invoice->hacienda_status = '01';
                $invoice->payment_status = "01";
                $invoice->payment_receipt = "";
                $invoice->generation_method = "etax";
                $invoice->xml_schema = 43;
                if ($request->document_type == '01') {
                    $invoice->reference_number = $company->last_invoice_ref_number + 1;
                }
                if ($request->document_type == '08') {
                    $invoice->reference_number = $company->last_invoice_pur_ref_number + 1;
                }
                if ($request->document_type == '09') {
                    $invoice->reference_number = $company->last_invoice_exp_ref_number + 1;
                }

                $invoiceData = $invoice->setInvoiceData($request);
                if (!empty($invoiceData)) {
                    $invoice = $apiHacienda->createInvoice($invoiceData, $tokenApi);
                }
                if ($request->document_type == '01') {
                    $company->last_invoice_ref_number = $invoice->reference_number;
                    $company->last_document = $invoice->document_number;

                } elseif ($request->document_type == '08') {
                    $company->last_invoice_pur_ref_number = $invoice->reference_number;
                    $company->last_document_invoice_pur = $invoice->document_number;

                }  elseif ($request->document_type == '09') {
                    $company->last_invoice_exp_ref_number = $invoice->reference_number;
                    $company->last_document_invoice_exp = $invoice->document_number;
                }

                $company->save();
                if ($invoice->hacienda_status == '03') {
                   // Mail::to($invoice->client_email)->send(new \App\Mail\Invoice(['new_plan_details' => $newPlanDetails, 'old_plan_details' => $plan]));
                }
                clearInvoiceCache($invoice);

                return redirect('/facturas-emitidas');
            } else {
                return back()->withError( 'Ha ocurrido un error al enviar factura.' );
            }
        } catch( \Exception $ex ) {
            Log::error("ERROR Envio de factura a hacienda -> ".$ex->getMessage());
            return back()->withError( 'Ha ocurrido un error al enviar factura.' );
        }
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
        
        $company = currentCompanyModel();
        $arrayActividades = $company->getActivities();

        $units = UnidadMedicion::all()->toArray();
        return view('Invoice/show', compact('invoice','units', 'arrayActividades') );
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $company = currentCompanyModel();
        
        $invoice = Invoice::findOrFail($id);
        $units = UnidadMedicion::all()->toArray();
        $this->authorize('update', $invoice);
      
        $arrayActividades = $company->getActivities();
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
          return redirect('/facturas-emitidas');
        }  
      
        return view('Invoice/edit', compact('invoice', 'units', 'arrayActividades') );
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
                
                /**Revisa limite de facturas**/
                /*$available = 0;
                $unicos = [];
                foreach ($collection[0]->chunk(200) as $facturas) {
                    foreach ($facturas as $row) {
                        $consecutivoComprobante = $row['consecutivocomprobante'];
                        array_push( $unicos, $consecutivoComprobante );
                    }
                }
                $unicos =  array_unique($unicos);
                $totalFacturasExcel = sizeof($unicos);

                $available_invoices = AvailableInvoices::where('company_id',$company->id)->first();
                $available_invoices_by_plan = $available_invoices->monthly_quota - $available_invoices->current_month_sent;

                if($totalFacturasExcel > $available_invoices_by_plan){
                    $available = $available_invoices_by_plan + $company->additional_invoices;
                }
                if($totalFacturasExcel > $available){
                    return back()->withError('No puede pasarse de su cuota de facturas mensual');
                }*/
                /**END Revisa limite de facturas**/
                
                foreach ($collection[0]->chunk(200) as $facturas) {

                    \DB::transaction(function () use ($facturas, &$company, &$i /*, $available_invoices, $available_invoices_by_plan*/) {

                        $inserts = array();
                        foreach ($facturas as $row){
                            $i++;
                            //dd($row);
                            $arrayRow = array();
                            /*if($available_invoices_by_plan > 0){
                                $available_invoices_by_plan = $available_invoices_by_plan - 1;
                            }else{
                                $company->additional_invoices = $company->additional_invoices - 1;
                            }*/

                            $metodoGeneracion = "XLSX";

                            //Datos de proveedor
                            $nombreCliente = $row['nombrecliente'];
                            $codigoCliente = array_key_exists('codigocliente', $row) ? $row['codigocliente'] : '';
                            $tipoPersona = $row['tipoidentificacion'];
                            $identificacionCliente = $row['identificacionreceptor'];
                            $correoCliente = $row['correoreceptor'];
                            $telefonoCliente = null;

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
                            
                            //Exoneraciones
                            $totalNeto = 0;
                            $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                            $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                            $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                            $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                            $montoExoneracion = $row['montoexoneracion'] ?? 0;
                            $impuestoNeto = $row['impuestoneto'] ?? 0;
                            $totalMontoLinea = $row['totalmontolinea'] ?? 0;
                            
                            //
                            $arrayInsert = array(
                                'metodoGeneracion' => $metodoGeneracion,
                                'idEmisor' => 0,
                                'nombreCliente' => $nombreCliente,
                                'descripcion' => $descripcion,
                                'codigoCliente' => $codigoCliente,
                                'tipoPersona' => $tipoPersona,
                                'identificacionCliente' => $identificacionCliente,
                                'correoCliente' => $correoCliente,
                                'telefonoCliente' => $telefonoCliente,
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
                                'cantidad' => $cantidad,
                                'precioUnitario' => $precioUnitario,
                                'totalLinea' => $totalLinea,
                                'montoIva' => $montoIva,
                                'codigoEtax' => $codigoEtax,
                                'montoDescuento' => $montoDescuento,
                                'subtotalLinea' => $subtotalLinea,
                                'tipoDocumento' => $tipoDocumento,
                                'codigoProducto' => $codigoProducto,
                                'detalleProducto' => $detalleProducto,
                                'unidadMedicion' => $unidadMedicion,
                                'tipoDocumentoExoneracion' => $tipoDocumentoExoneracion,
                                'documentoExoneracion' => $documentoExoneracion,
                                'companiaExoneracion' => $companiaExoneracion,
                                'porcentajeExoneracion' => $porcentajeExoneracion,
                                'montoExoneracion' => $montoExoneracion,
                                'impuestoNeto' => $impuestoNeto,
                                'totalMontoLinea' => $totalMontoLinea,
                                'xmlSchema' => $xmlSchema,
                                'codigoActividad' => $codigoActividad,
                                'isAuthorized' => true,
                                'codeValidated' => true
                            );

                            $insert = Invoice::importInvoiceRow( $arrayInsert );

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
                return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. IC 537'.$i);
            }
            
            $company->save();
            //$available_invoices->save();
            
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
            
            return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente en '.$time.'s.');
        }else{
            return redirect('/facturas-emitidas')->withError('Usted tiene un límite de 2500 facturas por archivo.');
        }
    }

    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }

    public function anularInvoice($id)
    {
        try {
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login();
            if ($tokenApi !== false) {
                $invoice = Invoice::findOrFail($id);
                $note = new Invoice();
                $company = currentCompanyModel();

                //Datos generales y para Hacienda
                $note->company_id = $company->id;
                $note->document_type = "03";
                $note->hacienda_status = '01';
                $note->payment_status = "01";
                $note->payment_receipt = "";
                $note->generation_method = "etax";
                $note->reference_number = $company->last_note_ref_number + 1;
                $note->save();
                $noteData = $note->setNoteData($invoice);
                if (!empty($noteData)) {
                    $apiHacienda->createCreditNote($noteData, $tokenApi);
                }
                $company->last_note_ref_number = $noteData->reference_number;
                $company->last_document_note = $noteData->document_number;
                $company->save();

                clearInvoiceCache($invoice);

                return redirect('/facturas-emitidas')->withMessage('Nota de crédito creada.');

            } else {
                return back()->withError( 'Ha ocurrido un error al enviar factura.' );
            }

        } catch ( \Exception $e) {
            Log::error('Error al anular facturar -->'.$e);
            return redirect('/facturas-emitidas')->withErrors('Error al anular factura');
        }

    }

    private function get_rates()
    {
        
        try {
            $value = Cache::remember('usd_rate', '60000', function () {
                $today = new Carbon();
                $client = new \GuzzleHttp\Client();
                $response = $client->get(config('etax.exchange_url'),
                    ['query' => [
                        'Indicador' => '317',
                        'FechaInicio' => $today::now()->format('d/m/Y'),
                        'FechaFinal' => $today::now()->format('d/m/Y'),
                        'Nombre' => config('etax.namebccr'),
                        'SubNiveles' => 'N',
                        'CorreoElectronico' => config('etax.emailbccr'),
                        'Token' => config('etax.tokenbccr')
                        ]
                    ]
                );
                $body = $response->getBody()->getContents();
                $xml = new \SimpleXMLElement($body);
                $xml->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
                $tables = $xml->xpath('//INGC011_CAT_INDICADORECONOMIC[@d:id="INGC011_CAT_INDICADORECONOMIC1"]');
                return json_decode($tables[0]->NUM_VALOR);
            });

            return $value;

        } catch( \Exception $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: ');
        } catch (RequestException $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: '.
                $e->getResponse()->getReasonPhrase());
            return null;
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
                    
                    $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0;
                    $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                    $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    
                    //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                    if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                        //Registra el XML. Si todo sale bien, lo guarda en S3.
                        if( Invoice::saveInvoiceXML( $arr, 'XML' ) ) {
                            $invoice = Invoice::where('company_id', $company->id)->where('document_number', $consecutivoComprobante)->first();
                            Invoice::storeXML( $invoice, $file );
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
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Asegúrese de estar enviando un XML de factura válida.');
        }catch( \Throwable $ex ){
            Log::error('Error importando con archivo inválido' . $ex->getMessage());
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. Asegúrese de estar enviando un XML de factura válida.');
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
                return $invoice->clientName();
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
    
     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        $invoice->delete();
        clearInvoiceCache($invoice);
        
        return redirect('/facturas-emitidas')->withMessage('La factura ha sido eliminada satisfactoriamente.');
    } 
    
    /**
     * Restore the specific item
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $invoice = Invoice::onlyTrashed()->where('id', $id)->first();
        if( $invoice->company_id != currentCompany() ){
            return 404;
        }
        $invoice->restore();
        InvoiceItem::onlyTrashed()->where('invoice_id', $invoice->id)->restore();
        clearInvoiceCache($invoice);
        
        return redirect('/facturas-emitidas')->withMessage('La factura ha sido restaurada satisfactoriamente.');
    }  
    
    public function downloadPdf($id) {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        $invoiceUtils = new InvoiceUtils();
        $file = $invoiceUtils->downloadPdf( $invoice, currentCompanyModel() );
        $filename = $invoice->document_key . '.pdf';
        if( ! $invoice->document_key ) {
            $filename = $invoice->document_number . '-' . $invoice->client_id . '.pdf';
        }
        
        $headers = [
            'Content-Type' => 'application/pdf', 
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename'=> $filename
        ];
        return response($file, 200, $headers);
    }
    
    public function downloadXml($id) {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        $invoiceUtils = new InvoiceUtils();
        $file = $invoiceUtils->downloadXml( $invoice, currentCompanyModel() );
        $filename = $invoice->document_key . '.xml';
        if( ! $invoice->document_key ) {
            $filename = $invoice->document_number . '-' . $invoice->client_id . '.xml';
        }
        
        if( !isset($file) ){
            return redirect()->back()->withError('No se encontró el XML de la factura. Por favor contacte a soporte.');
        }
        
        $headers = [
            'Content-Type' => 'application/xml', 
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename'=> $filename
        ];
        return response($file, 200, $headers);
    }
    
    public function resendInvoiceEmail($id) {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        $company = currentCompanyModel();
        
        $invoiceUtils = new InvoiceUtils();
        $path = $invoiceUtils->getXmlPath( $invoice, $company );
        $invoiceUtils->sendInvoiceEmail( $invoice, $company, $path );
        
        return back()->withMessage( 'Se han reenviado los correos exitosamente.');
    }
    
    private function getDocReference($docType) {
        if ($docType == '01') {
            $lastSale = currentCompanyModel()->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = currentCompanyModel()->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = currentCompanyModel()->last_invoice_exp_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType) {
        $company = currentCompanyModel();
        $invoice = new Invoice();
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef($ref);

        return $key;
    }
}
