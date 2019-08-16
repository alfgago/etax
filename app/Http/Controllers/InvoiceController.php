<?php

namespace App\Http\Controllers;

use App\Actividades;
use App\AvailableInvoices;
use App\CodigosPaises;
use App\UnidadMedicion;
use App\ProductCategory;
use App\CodigoIvaRepercutido;
use App\CalculatedTax;
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
use App\Jobs\ProcessInvoicesImport;

/**
 * @group Controller - Facturas de venta
 *
 * Funciones de InvoiceController
 */
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
        $company = currentCompanyModel(false);

        if ( !$company->atv_validation && $company->use_invoicing ) {
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

                    $user = auth()->user();
                    Cache::forget("cache-currentcompany-$user->id");
                }
            }else {
                return redirect('/empresas/certificado')->withError( 'Hubo un error al validar su certificado digital. Verifique que lo haya ingresado correctamente. Si cree que está correcto, ' );
            }
        }
        if($company->last_note_ref_number === null) {
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
            ->editColumn('moneda', function(Invoice $invoice) {
                return $invoice->currency == 'CRC' ? $invoice->currency : "$invoice->currency ($invoice->currency_rate)";
            })
            ->editColumn('hacienda_status', function(Invoice $invoice) {
                if ($invoice->hacienda_status == '03') {
                    return '<div class="green">  <span class="tooltiptext">Aceptada</span></div>
                        <a href="/facturas-emitidas/query-invoice/'.$invoice->id.'". title="Consultar factura en hacienda" class="text-dark mr-2"> 
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                          </a>';
                }
                if ($invoice->hacienda_status == '04') {
                    return '<div class="red"> <span class="tooltiptext">Rechazada</span></div>
                        <a href="/facturas-emitidas/query-invoice/'.$invoice->id.'". title="Consultar factura en hacienda" class="text-dark mr-2"> 
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>';
                }

                return '<div class="yellow"><span class="tooltiptext">Procesando...</span></div>
                    <a href="/facturas-emitidas/query-invoice/'.$invoice->id.'". title="Consultar factura en hacienda" class="text-dark mr-2"> 
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                      </a>';
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
        $countries  = CodigosPaises::all()->toArray();
        $units = UnidadMedicion::all()->toArray();
        if( count($arrayActividades) == 0 ){
            return redirect('/empresas/editar')->withErrors('No ha definido una actividad comercial para esta empresa');
        }
        return view("Invoice/create-factura-manual", ['units' => $units, 'countries' => $countries])->with('arrayActividades', $arrayActividades);
    }

    /**
     * Muestra el formulario para emitir facturas
     *
     * @return \Illuminate\Http\Response
     */
    public function emitFactura($tipoDocumento)
    {
        $company = currentCompanyModel(false);
        
        $errors = $company->validateEmit();
        if( $errors ){
            return redirect($errors['url'])->withError($errors['mensaje']);
        }

        $units = UnidadMedicion::all()->toArray();
        $countries  = CodigosPaises::all()->toArray();
        $arrayActividades = $company->getActivities();
        
        if(count($arrayActividades) == 0){
            return redirect('/empresas/editar')->withError('No ha definido una actividad comercial para esta empresa');
        }
        
        return view("Invoice/create-factura",
            [
                'document_type' => $tipoDocumento, 'rate' => $this->get_rates(),
                'document_number' => $this->getDocReference($tipoDocumento),
                'document_key' => $this->getDocumentKey($tipoDocumento),
                'units' => $units, 'countries' => $countries,
                'default_currency' => $company->default_currency,
                'default_vat_code' => $company->default_vat_code
            ])
            ->with('arrayActividades', $arrayActividades);
    }
    
    /**
     * Muestra el formulario para emitir sujeto pasivos
     *
     * @return \Illuminate\Http\Response
     */
    public function emitSujetoPasivo()
    {
        $company = currentCompanyModel();
        $tipoDocumento = '08';
        
        //validates if the company has invoices left, atv, consecutivos
        /*$errors = $company->validateEmit();
        if(count($errors) > 0){
            return redirect($errors['url'])->withError($errors['mensaje']);
        }*/

        //gather info from the DBs to the view
        $units = UnidadMedicion::all()->toArray();
        $countries  = CodigosPaises::all()->toArray();
        $arrayActividades = $company->getActivities();
        
        if(count($arrayActividades) == 0){
            return redirect('/empresas/editar')->withError('No ha definido una actividad comercial para esta empresa');
        }
        
        return view("Invoice/create-fec-sujetopasivo", [
            'document_type' => $tipoDocumento, 'rate' => $this->get_rates(),
            'document_number' => $this->getDocReference($tipoDocumento),
            'document_key' => $this->getDocumentKey($tipoDocumento),
            'units' => $units, 'countries' => $countries,
            'default_currency' => $company->default_currency,
            'default_vat_code' => $company->default_vat_code
            ])
            ->with('arrayActividades', $arrayActividades);
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
        
        if(CalculatedTax::validarMes($request->generated_date)){
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
          
            return redirect('/facturas-emitidas')->withMessage('Factura registrada con éxito');
        }else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }
       
      
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
            if(CalculatedTax::validarMes($request->generated_date)){
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
                    if ($request->document_type == '04') {
                        $invoice->reference_number = $company->last_ticket_ref_number + 1;
                    }

                    $invoiceData = $invoice->setInvoiceData($request);
                    
                    $invoice->document_key = $this->getDocumentKey($request->document_type);
                    $invoice->document_number = $this->getDocReference($request->document_type);
                    $invoice->save();
                    
                    if (!empty($invoiceData)) {
                        $invoice = $apiHacienda->createInvoice($invoiceData, $tokenApi);
                    }
                    if ($request->document_type == '01') {
                        $company->last_invoice_ref_number = $invoice->reference_number;
                        $company->last_document = $invoice->document_number;

                    } elseif ($request->document_type == '08') {
                        $company->last_invoice_pur_ref_number = $invoice->reference_number;
                        $company->last_document_invoice_pur = $invoice->document_number;

                    } elseif ($request->document_type == '09') {
                        $company->last_invoice_exp_ref_number = $invoice->reference_number;
                        $company->last_document_invoice_exp = $invoice->document_number;
                    } elseif ($request->document_type == '04') {
                        $company->last_ticket_ref_number = $invoice->reference_number;
                        $company->last_document_ticket = $invoice->document_number;
                    }

                    $company->save();
                    clearInvoiceCache($invoice);
                

                    return redirect('/facturas-emitidas')->withMessage('Factura registrada con éxito');
                } else {
                    return back()->withError( 'Ha ocurrido un error al enviar factura.' );
                }
            }else{
                return back()->withError('Mes seleccionado ya fue cerrado');
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
        $countries  = CodigosPaises::all()->toArray();

        $product_categories = ProductCategory::whereNotNull('invoice_iva_code')->get();
        $codigos = CodigoIvaRepercutido::where('hidden', false)->get();
        $units = UnidadMedicion::all()->toArray();
        return view('Invoice/show', compact('invoice','units','arrayActividades','countries','product_categories','codigos') );
    }

    public function actualizar_categorias(Request $request){
        $invoice = Invoice::where('id',$request->invoice_id)->first(); 

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            Invoice::where('id',$request->invoice_id)
                ->update(['commercial_activity'=>$request->commercial_activity]);
            foreach ($request->items as $item) {
                InvoiceItem::where('id',$item['id'])
                ->update(['product_type'=>$item['category_product'],'iva_type'=>$item['tipo_iva']]);
                
            }
            return redirect('/facturas-emitidas/')->withMessage('Factura validada satisfactoriamente');
        }else{
            return back()->withError('Mes seleccionado ya fue cerrado');
        }
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

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            $units = UnidadMedicion::all()->toArray();
            $this->authorize('update', $invoice);
          
            $arrayActividades = $company->getActivities();
            $countries  = CodigosPaises::all()->toArray();

          
            //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
            if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
              return redirect('/facturas-emitidas');
            }  
        }else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }
      
        return view('Invoice/edit', compact('invoice', 'units', 'arrayActividades', 'countries') );
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
        if(CalculatedTax::validarMes($request->generated_date)){
            $this->authorize('update', $invoice);

            //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
            if( $invoice->generation_method != 'M' && $invoice->generation_method != 'XLSX' ){
              return redirect('/facturas-emitidas');
            }
          
            $invoice->setInvoiceData($request);
            
            clearInvoiceCache($invoice);
            return redirect('/facturas-emitidas')->withMessage('Factura editada con éxito');
        } else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }

    }
    
    public function export( $year, $month ) {
        return Excel::download(new InvoiceExport($year, $month), 'documentos-emitidos.xlsx');
    }
    
    public function importExcel() {
        
        request()->validate([
          'archivo' => 'required',
        ]);
      
        try {
            $collection = Excel::toCollection( new InvoiceImport(), request()->file('archivo') );
        }catch( \Exception $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }catch( \Throwable $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido.' );
        }

        $company = currentCompanyModel();
        
        if( count($collection[0]->toArray()) < 1800 ){
            ProcessInvoicesImport::dispatchNow($collection[0]->toArray(), $company);
            return redirect('/facturas-emitidas')->withMessage('Facturas importados satisfactoriamente.');
        }else{
            ProcessInvoicesImport::dispatch($collection[0]->toArray(), $company);
            return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
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
            Log::info('Anulacion de facturar -->'.$id);
            $invoice = Invoice::findOrFail($id); 

            if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
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
            }else{
                return back()->withError('Mes seleccionado ya fue cerrado');
            }

        } catch ( \Exception $e) {
            Log::error('Error al anular facturar -->'.$e);
            return redirect('/facturas-emitidas')->withErrors('Error al anular factura');
        }

    }

    private function get_rates()
    {

        $cacheKey = "usd_rate";
        $lastRateKey = "last_usd_rate";
        try {
            if ( !Cache::has($cacheKey) ) {

                $today = new Carbon();
                $client = new \GuzzleHttp\Client();
                $response = $client->get(config('etax.exchange_url'),
                    ['query' => [
                        'Indicador' => '318',
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
                $valor =  json_decode($tables[0]->NUM_VALOR);

                Cache::put($cacheKey, $valor, now()->addHours(2));
                Cache::put($lastRateKey, $valor, now()->addDays(5));
            }

            $value = Cache::get($cacheKey);
            return $value;

        } catch( \Exception $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: ');
            $value = Cache::get($lastRateKey);
            return $value;
        } catch (RequestException $e) {
            Log::error('Error al consultar tipo de cambio: Code:'.$e->getCode().' Mensaje: '.
                $e->getResponse()->getReasonPhrase());
            $value = Cache::get($lastRateKey);
            return $value;
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
                    

                    $FechaEmision = explode("T", $arr['FechaEmision']);
                    $FechaEmision = explode("-", $FechaEmision[0]);
                    $FechaEmision = $FechaEmision[2]."/".$FechaEmision[1]."/".$FechaEmision[0];
                    if(CalculatedTax::validarMes($FechaEmision)){
                        
                        //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                        try { 
                            $identificacionReceptor = array_key_exists('Receptor', $arr) ? $arr['Receptor']['Identificacion']['Numero'] : 0 ;
                        }catch(\Exception $e){ $identificacionReceptor = 0; };
                        
                        $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                        $consecutivoComprobante = $arr['NumeroConsecutivo'];
                        $identificacionEmisor = $arr['Emisor']['Identificacion']['Numero'];
                        $consecutivoComprobante = $arr['NumeroConsecutivo'];
                    
                        //Compara la cedula de Receptor con la cedula de la compañia actual. Tiene que ser igual para poder subirla
                        if( preg_replace("/[^0-9]+/", "", $company->id_number) == preg_replace("/[^0-9]+/", "", $identificacionEmisor ) ) {
                            //Registra el XML. Si todo sale bien, lo guarda en S3.
                            $invoice = Invoice::saveInvoiceXML( $arr, 'XML' );
                            if( $invoice ) {
                                Invoice::storeXML( $invoice, $file );
                            }
                        }
                    }else{
                        return redirect('/facturas-emitidas/validaciones')->withError('Mes seleccionado ya fue cerrado');
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

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
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
        }else{
            return redirect('/facturas-emitidas/validaciones')->withError('Mes seleccionado ya fue cerrado');
        }
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
            ->editColumn('subtotal', function(Invoice $invoice) {
                return $invoice->subtotal;
            })
            ->editColumn('iva_amount', function(Invoice $invoice) {
                return $invoice->iva_amount;
            })
            ->editColumn('total', function(Invoice $invoice) {
                return $invoice->total;
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
    
    public function hideInvoice ( Request $request, $id )
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        
        if ( $request->hide_from_taxes ) {
            $invoice->hide_from_taxes = true;
            $invoice->save();
            clearInvoiceCache($invoice);
            return redirect('/facturas-emitidas')->withMessage( 'La factura '. $invoice->document_number . ' se ha ocultado para cálculo de IVA.');
        }else{
            $invoice->hide_from_taxes = false;
            $invoice->save();
            clearInvoiceCache($invoice);
            return redirect('/facturas-emitidas')->withMessage( 'La factura '. $invoice->document_number . ' se ha incluido nuevamente para cálculo de IVA.');
        }
    }
    
    public function authorizeInvoice ( Request $request, $id )
    {
        $invoice = Invoice::findOrFail($id); 

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            $this->authorize('update', $invoice);
            
            if ( $request->autorizar ) {
                $invoice->is_authorized = true;
                $invoice->save();
                return redirect('/facturas-emitidas/autorizaciones')->withMessage( 'La factura '. $invoice->document_number . ' ha sido autorizada');
            }else {
                $invoice->is_authorized = false;
                $invoice->is_void = true;
                InvoiceItem::where('invoice_id', $invoice->id)->delete();
                $invoice->delete();
                return redirect('/facturas-emitidas/autorizaciones')->withMessage( 'La factura '. $invoice->document_number . ' ha sido rechazada');
            }
        }else{
            return redirect('/facturas-emitidas/autorizaciones')->withError('Mes seleccionado ya fue cerrado');
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

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            $this->authorize('update', $invoice);
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
            clearInvoiceCache($invoice);
            
            return redirect('/facturas-emitidas')->withMessage('La factura ha sido eliminada satisfactoriamente.');
        }else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }
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
        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            if( $invoice->company_id != currentCompany() ){
                return 404;
            }
            $invoice->restore();
            InvoiceItem::onlyTrashed()->where('invoice_id', $invoice->id)->restore();
            clearInvoiceCache($invoice);
            
            return redirect('/facturas-emitidas')->withMessage('La factura ha sido restaurada satisfactoriamente.');
        }else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }
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
        
        if(!$file) {
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
        if ($docType == '04') {
            $lastSale = currentCompanyModel()->last_ticket_ref_number + 1;
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
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef($ref);

        return $key;
    }
    
    public function fixImports() {
        $invoiceUtils = new InvoiceUtils();
        $invoices = Invoice::where('generation_method', 'Email')->orWhere('generation_method', 'XML')->get();
        dd($invoices);
        
        foreach($invoices as $invoice) {
            if( !$invoice->client_zip ){
                $file = $invoiceUtils->downloadXml( $invoice, $invoice->company_id );
                if($file) {
                    $xml = simplexml_load_string($file);
                    $json = json_encode( $xml ); // convert the XML string to JSON
                    $arr = json_decode( $json, TRUE );
                    $invoice = Invoice::saveInvoiceXML( $arr, $invoice->generation_method );
                }
            }
        }
        
        return true;
    }

    public function consultInvoice($id) {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);

        $invoiceUtils = new InvoiceUtils();
        $file = $invoiceUtils->downloadXml( $invoice, currentCompanyModel(), 'MH' );

        $filename = 'MH-'.$invoice->document_key . '.xml';
        if( ! $invoice->document_key ) {
            $filename = $invoice->document_number . '-' . $invoice->client_id . '.xml';
        }

        if(!$file) {
            return redirect()->back()->withError('No se encontró el XML de Mensaje Hacienda. Por favor intente reenviar consulta ha hacienda.');
        }

        $headers = [
            'Content-Type' => 'application/xml',
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename'=> $filename
        ];
        return response($file, 200, $headers);
    }

    public function queryInvoice($id) {
        try {
            $invoice = Invoice::findOrFail($id);
            $this->authorize('update', $invoice);

            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);

            if ($tokenApi !== false) {
                $company = currentCompanyModel();
                $result = $apiHacienda->queryHacienda($invoice, $tokenApi, $company);
                if ($result == false) {
                    return redirect()->back()->withErrors('El servidor de Hacienda es inaccesible en este momento, o el comprobante no ha sido recibido. Por favor intente de nuevo más tarde o contacte a soporte.');
                }
                $filename = 'MH-'.$invoice->document_key . '.xml';
                if( ! $invoice->document_key ) {
                    $filename = $invoice->document_number . '-' . $invoice->client_id . '.xml';
                }
                $headers = [
                    'Content-Type' => 'application/xml',
                    'Content-Description' => 'File Transfer',
                    'Content-Disposition' => "attachment; filename={$filename}",
                    'filename'=> $filename
                ];
                return response($result, 200, $headers);
            }

        } catch (\Exception $e) {
            Log::error("Error consultado factura -->" .$e);
            return redirect()->back()->withErrors('Error al consultar comprobante en hacienda');
        }
    }
}
