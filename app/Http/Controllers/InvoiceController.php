<?php

namespace App\Http\Controllers;
use App\Jobs\LogActivityHandler as Activity;
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
use App\Bill;
use App\BillItem;
use App\Exports\InvoiceExport;
use App\Exports\LibroVentasExport;
use App\Exports\LibroVentasExportSM;
use App\Imports\InvoiceImport;
use App\Imports\InvoiceImportSM;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use GuzzleHttp\Message\ResponseInterface;
use PDF;
use App\Jobs\ProcessInvoice;
use App\Jobs\ProcessInvoicesImport;
use App\Jobs\ProcessSendExcelInvoices;
use Illuminate\Support\Facades\Input;

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
                if ($invoice->hacienda_status == '03' || $invoice->hacienda_status == '30') {
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
            
            
            try{
                if ($request->document_type == '08' ) {
                    $this->storeBillFEC($request);
                    if( $request->tipo_compra == 'local' ){
                        $invoice->is_void = true;
                        $invoice->save();
                    }
                }
            }catch(\Throwable $e){}
                    
            
            $company->save();
            
            clearInvoiceCache($invoice);
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "Factura registrada con éxito."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
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
                    $request->document_key = $invoice->document_key;
                    $request->document_number = $invoice->document_number;
                    
                    if ($request->document_type == '08' ) {
                        $this->storeBillFEC($request);
                        if( $request->tipo_compra == 'local' ){
                            $invoice->is_void = true;
                        }
                    }
                    
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
                    $user = auth()->user();
                    Activity::dispatch(
                        $user,
                        $invoice,
                        [
                            'company_id' => $invoice->company_id,
                            'id' => $invoice->id,
                            'document_key' => $invoice->document_key
                        ],
                        "Factura registrada con éxito."
                    )->onConnection(config('etax.queue_connections'))
                    ->onQueue('log_queue');
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
    
    private function storeBillFEC($request) {
        $company = currentCompanyModel();

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

        $bill->setBillData($request);
        
        $bill->is_code_validated = 1;
        $bill->accept_status = 1;
        $bill->accept_iva_condition = '01';
        $bill->accept_iva_acreditable = $bill->iva_amount;
        $bill->accept_iva_gasto = 0;
        $bill->description = "FEC" . ($request->description ?? '');
        $bill->save();
        $company->last_bill_ref_number = $bill->reference_number;
        $company->save();

        $user = auth()->user();
        Activity::dispatch(
            $user,
            $invoice,
            [
                'company_id' => $invoice->company_id,
                'id' => $invoice->id,
                'document_key' => $invoice->document_key
            ],
            "Factura registrada con éxito FEC."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        clearBillCache($bill);

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
        $totalIvaDevuelto = 0;
        if ($invoice->total_iva_devuelto == 0) {
            foreach ($invoice->items as $item) {
                if ($invoice->payment_type == '02' && $item->product_type == 12) {
                    $totalIvaDevuelto += $item->iva_amount;
                }
            }
            $invoice->total_iva_devuelto = $totalIvaDevuelto;
        }
        $invoice->save();

        $product_categories = ProductCategory::whereNotNull('invoice_iva_code')->get();
        $codigos = CodigoIvaRepercutido::where('hidden', false)->get();
        $units = UnidadMedicion::all()->toArray();
        return view('Invoice/show', compact('invoice','units','arrayActividades','countries','product_categories','codigos', 'company') );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function notaDebito($id)
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
        if($company->last_debit_note_ref_number === null) {
            return redirect('/empresas/configuracion')->withErrors('No ha ingresado ultimo consecutivo de nota de debito');
        }

        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        $company = currentCompanyModel();
        $arrayActividades = $company->getActivities();
        $countries  = CodigosPaises::all()->toArray();

        $product_categories = ProductCategory::whereNotNull('invoice_iva_code')->get();
        $codigos = CodigoIvaRepercutido::where('hidden', false)->get();
        $units = UnidadMedicion::all()->toArray();
        return view('Invoice/nota-debito', compact('invoice','units','arrayActividades','countries','product_categories','codigos', 'company') );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function notaCredito($id)
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
        if($company->last_debit_note_ref_number === null) {
            return redirect('/empresas/configuracion')->withErrors('No ha ingresado ultimo consecutivo de nota de debito');
        }

        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        $company = currentCompanyModel();
        $arrayActividades = $company->getActivities();
        $countries  = CodigosPaises::all()->toArray();

        $product_categories = ProductCategory::whereNotNull('invoice_iva_code')->get();
        $codigos = CodigoIvaRepercutido::where('hidden', false)->get();
        $units = UnidadMedicion::all()->toArray();

        return view('Invoice/nota-credito', compact('invoice','units','arrayActividades','countries','product_categories','codigos', 'company') );
    }

    public function sendNotaDebito($id, Request $request)
    {
        try {
            Log::info('Enviando nota de debito de facturar -->'.$id);
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
                    $note->document_type = "02";
                    $note->hacienda_status = '01';
                    $note->payment_status = "01";
                    $note->payment_receipt = "";
                    $note->generation_method = "etax";
                    $note->reference_number = $company->last_debit_note_ref_number + 1;
                    $note->save();
                    $noteData = $note->setNoteData($invoice, $request->items, $note->document_type, $request);
                    if (!empty($noteData)) {
                        $apiHacienda->createCreditNote($noteData, $tokenApi);
                    }
                    $company->last_debit_note_ref_number = $noteData->reference_number;
                    $company->last_document_debit_note = $noteData->document_number;
                    $company->save();

                    clearInvoiceCache($invoice);
                    $user = auth()->user();
                    Activity::dispatch(
                        $user,
                        $invoice,
                        [
                            'company_id' => $invoice->company_id,
                            'id' => $invoice->id,
                            'document_key' => $invoice->document_key
                        ],
                        "Nota de debito creada."
                    )->onConnection(config('etax.queue_connections'))
                    ->onQueue('log_queue');
                    return redirect('/facturas-emitidas')->withMessage('Nota de debito creada.');

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

    public function actualizar_categorias(Request $request){
        $invoice = Invoice::where('id',$request->invoice_id)->first(); 

        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            try{
                Invoice::where('id',$request->invoice_id)
                    ->update(['commercial_activity'=>$request->commercial_activity]);
                foreach ($request->items as $item) {
                    InvoiceItem::where('id',$item['id'])
                    ->update(['product_type'=>$item['category_product'],'iva_type'=>$item['tipo_iva']]);
                }
            }catch(\Throwable $e){}
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "Factura validada satisfactoriamente."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');

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
      
        return view('Invoice/edit', compact('invoice', 'units', 'arrayActividades', 'countries', 'company') );
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
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "Factura editada con éxito."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
            return redirect('/facturas-emitidas')->withMessage('Factura editada con éxito');
        } else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }

    }
  
    public function validar($id){
        $company = currentCompanyModel();
        $invoice = Invoice::find($id);
            $companyActivities = explode(", ", $company->commercial_activities);
            $commercialActivities = Actividades::whereIn('codigo', $companyActivities)->get();
            $codigosEtax = CodigoIvaRepercutido::where('hidden', false)->get();
            $categoriaProductos = ProductCategory::whereNotNull('invoice_iva_code')->get();
            return view('Invoice/validar', compact('invoice', 'commercialActivities', 'codigosEtax', 'categoriaProductos', 'company'));
        
    }


    public function guardarValidar(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice);
        if(CalculatedTax::validarMes( $invoice->generatedDate()->format('d/m/y') )){ 
            $invoice->commercial_activity = $request->actividad_comercial;
            $invoice->is_code_validated = true;
            foreach( $request->items as $item ) {
                InvoiceItem::where('id', $item['id'])
                ->update([
                  'iva_type' =>  $item['iva_type'],
                  'product_type' =>  $item['product_type']
                ]);
            }
            
            $invoice->save();
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ". $invoice->document_number . " ha sido validada."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
            
            clearInvoiceCache($invoice);

            return back()->withMessage( 'La factura '. $invoice->document_number . ' ha sido validada');
        }else{
            return back()->withError('Mes seleccionado ya fue cerrado');
        }

    }



    public function export( $year, $month ) {
        return Excel::download(new InvoiceExport($year, $month), 'documentos-emitidos.xlsx');
    }
    
    public function exportLibroVentas( $year, $month ) {
        $company = currentCompanyModel();
        if( $company->id == 1110 ){
            return Excel::download(new LibroVentasExportSM($year, $month), 'libro-ventas.xlsx');
        }
        return Excel::download(new LibroVentasExport($year, $month), 'libro-ventas.xlsx');
    }
    
    public function importExcel() {
        
        request()->validate([
          'archivo' => 'required',
        ]);
      
        try {
            $collection = Excel::toCollection( new InvoiceImport(), request()->file('archivo') );
        }catch( \Throwable $ex ){
            return back()->withError( 'Se ha detectado un error en el tipo de archivo subido. '.$ex->getMessage() );
        }

        $company = currentCompanyModel();

        try {
            $collection = $collection->toArray()[0];
            Log::info($company->id_number . " importanto Excel ventas con ".count($collection)." lineas");
            $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
            $i = 0;
            $invoiceList = array();
            
            if(count($collection) < 7500){
                foreach ($collection as $row){
                    $metodoGeneracion = "XLSX";
                  
                    if( isset($row['consecutivocomprobante']) ){
                        $i++;
                      
                        $cedulaEmpresa = isset($row['cedulaempresa']) ? $row['cedulaempresa'] : null;
                        if( $company->id_number != $cedulaEmpresa ){ 
                          return back()->withError( "Error en validación: Asegúrese de agregar la columna CedulaEmpresa a su archivo de excel, con la cédula de su empresa en cada línea. La línea $i le pertenece a la empresa actual. ($company->id_number)" );
                        }
                        //Datos de proveedor
                        $nombreCliente = $row['nombrecliente'];
                        $codigoCliente = isset($row['codigocliente']) ? $row['codigocliente'] : '';
                        $tipoPersona = (int)$row['tipoidentificacion'];
                        $identificacionCliente = $row['identificacionreceptor'] ?? null;
                        $correoCliente = $row['correoreceptor'] ?? null;
                        $telefonoCliente = null;
    
                        //Datos de factura
                        $consecutivoComprobante = $row['consecutivocomprobante'];
                        $claveFactura = isset($row['clavefactura']) ? $row['clavefactura'] : $consecutivoComprobante;
                        $condicionVenta = str_pad((int)$row['condicionventa'], 2, '0', STR_PAD_LEFT);
                        $metodoPago = str_pad((int)$row['metodopago'], 2, '0', STR_PAD_LEFT);
                        $numeroLinea = isset($row['numerolinea']) ? $row['numerolinea'] : 1;
                        $fechaEmision = $row['fechaemision'];
    
                        $fechaVencimiento = isset($row['fechavencimiento']) ? $row['fechavencimiento'] : $fechaEmision;
                        $idMoneda = $row['moneda'];
                        $tipoCambio = $row['tipocambio'];
                        $totalDocumento = $row['totaldocumento'];
                        $tipoDocumento = str_pad((int)$row['tipodocumento'], 2, '0', STR_PAD_LEFT);
                        $descripcion = isset($row['descripcion'])  ? $row['descripcion'] : '';
    
                        //Datos de linea
                        $codigoProducto = $row['codigoproducto'];
                        $detalleProducto = $row['detalleproducto'];
                        $unidadMedicion = $row['unidadmedicion'];
                        $cantidad = isset($row['cantidad']) ? $row['cantidad'] : 1;
                        $precioUnitario = $row['preciounitario'];
                        $subtotalLinea = (float)$row['subtotallinea'];
                        $totalLinea = $row['totallinea'];
                        $montoDescuento = isset($row['montodescuento']) ? $row['montodescuento'] : 0;
                        $codigoEtax = $row['codigoivaetax'];
                        $categoriaHacienda = isset($row['categoriahacienda']) ? $row['categoriahacienda'] : (isset($row['categoriadeclaracion']) ? $row['categoriadeclaracion'] : null);
                        $montoIva = (float)$row['montoiva'];
                        $acceptStatus = isset($row['aceptada']) ? $row['aceptada'] : 1;
                        
                        $codigoActividad = $row['actividadcomercial'] ?? $mainAct;
                        $xmlSchema = $row['xmlschema'] ?? 43;
                        
                        //Exoneraciones
                        $totalNeto = 0;
                        $tipoDocumentoExoneracion = $row['tipodocumentoexoneracion'] ?? null;
                        $documentoExoneracion = $row['documentoexoneracion'] ?? null;
                        $companiaExoneracion = $row['companiaexoneracion'] ?? null;
                        $porcentajeExoneracion = $row['porcentajeexoneracion'] ?? 0;
                        $montoExoneracion = $row['montoexoneracion'] ?? 0;
                        $impuestoNeto = $row['impuestoneto'] ?? 0;
                        $totalMontoLinea = $row['totalmontolinea'] ?? 0;
                        
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
                            'moneda' => $idMoneda,
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
                            'categoriaHacienda' => $categoriaHacienda,
                            'acceptStatus' => $acceptStatus,
                            'isAuthorized' => true,
                            'codeValidated' => true
                        );
                        
                        $invoiceList = Invoice::importInvoiceRow($arrayInsert, $invoiceList, $company);
                    }
                }
            
                Log::info("$i procesadas...");
                foreach (array_chunk ( $invoiceList, 100 ) as $facturas) {
                    Log::info("Mandando 100 a queue...");
                    ProcessInvoicesImport::dispatch($facturas);
                }
                Log::info("Envios a queue finalizados $company->id_number");
            }else{
                return redirect('/facturas-emitidas')->withError('Error importando. El archivo tiene más de 2500 lineas.');
            }
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo:" . $ex);
            return redirect('/facturas-emitidas')->withError('Error importando. Archivo excede el tamaño mínimo.');
        }

        $company->save();
        
        return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
        
        
    }
    
    

    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
 
    public function anularInvoice($id, Request $request)
    {
        try {
            Log::info('Enviando nota de credito de facturar -->'.$id);
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
                    $note->reason = $request->reason ?? "Anular Factura";
                    $note->code_note = $request->code_note ?? "01";
                    $note->reference_number = $company->last_note_ref_number + 1;
                    $note->save();
                    $noteData = $note->setNoteData($invoice, $request->items, $note->document_type, $request);
                    if (!empty($noteData)) {
                        $apiHacienda->createCreditNote($noteData, $tokenApi);
                    }
                    $company->last_note_ref_number = $noteData->reference_number;
                    $company->last_document_note = $noteData->document_number;
                    $company->save();

                    clearInvoiceCache($invoice);
                    $user = auth()->user();
                    Activity::dispatch(
                        $user,
                        $invoice,
                        [
                            'company_id' => $invoice->company_id,
                            'id' => $invoice->id,
                            'document_key' => $invoice->document_key
                        ],
                        "Nota de credito creada."
                    )->onConnection(config('etax.queue_connections'))
                    ->onQueue('log_queue');
                    return redirect('/facturas-emitidas')->withMessage('Nota de credito creada.');

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




    public function importXML(Request $request) {
        try {
            $time_start = getMicrotime();
            $company = currentCompanyModel();
            $file = Input::file('file');

            $xml = simplexml_load_string( file_get_contents($file) );
            $json = json_encode( $xml ); // convert the XML string to json  
            $arr = json_decode( $json, TRUE );

                if(substr($arr['NumeroConsecutivo'],8,2) != "04"){
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
                                $user = auth()->user();
                                Activity::dispatch(
                                    $user,
                                    $invoice,
                                    [
                                        'company_id' => $invoice->company_id,
                                        'id' => $invoice->id,
                                        'document_key' => $invoice->document_key
                                    ],
                                    "Factura de compra importada por xml."
                                )->onConnection(config('etax.queue_connections'))
                                ->onQueue('log_queue');
                                Invoice::storeXML( $invoice, $file );
                            }
                        }else{
                            return Response()->json("El documento $consecutivoComprobante no le pertenece a su empresa actual", 400);
                        }
                    }else{
                        return Response()->json('Error: El mes de la factura ya fue cerrado', 400);
                        //return redirect('/facturas-emitidas/validaciones')->withError('Mes seleccionado ya fue cerrado');
                    } 
                }else{
                    return Response()->json('Error: No se puede subir tiquetes electrónicos.', 400);
                        //return redirect('/facturas-emitidas/validaciones')->withError('Mes seleccionado ya fue cerrado');
                } 
            $company->save();
            $time_end = getMicrotime();
            $time = $time_end - $time_start;
                        

        }catch( \Exception $ex ){
            Log::error('Error importando con archivo inválido' . $ex->getMessage());
            return Response()->json('Error importando con archivo inválido', 400);
        }catch( \Throwable $ex ){
            Log::error('Error importando con archivo inválido' . $ex->getMessage());
            return Response()->json('Error importando con archivo inválido', 400);
        }
        return Response()->json('success', 200);
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
                    ->orderBy('generated_date', 'DESC')->paginate(10);
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
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ". $invoice->document_number . " ha sido validada."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
            
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
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ". $invoice->document_number . " se ha ocultado para cálculo de IVA."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
            return redirect('/facturas-emitidas')->withMessage( 'La factura '. $invoice->document_number . ' se ha ocultado para cálculo de IVA.');
        }else{
            $invoice->hide_from_taxes = false;
            $invoice->save();
            clearInvoiceCache($invoice);
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ". $invoice->document_number . " se ha incluido nuevamente para cálculo de IVA."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
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
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $invoice,
                    [
                        'company_id' => $invoice->company_id,
                        'id' => $invoice->id,
                        'document_key' => $invoice->document_key
                    ],
                    "La factura ". $invoice->document_number . " ha sido autorizada."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
                return redirect('/facturas-emitidas/autorizaciones')->withMessage( 'La factura '. $invoice->document_number . ' ha sido autorizada');
            }else {
                $invoice->is_authorized = false;
                $invoice->is_void = true;
                InvoiceItem::where('invoice_id', $invoice->id)->delete();
                $user = auth()->user();
                Activity::dispatch(
                    $user,
                    $invoice,
                    [
                        'company_id' => $invoice->company_id,
                        'id' => $invoice->id,
                        'document_key' => $invoice->document_key
                    ],
                    "La factura ". $invoice->document_number . " ha sido rechazada."
                )->onConnection(config('etax.queue_connections'))
                ->onQueue('log_queue');
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
            
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ha sido eliminada satisfactoriamente."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
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
            
            
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $invoice,
                [
                    'company_id' => $invoice->company_id,
                    'id' => $invoice->id,
                    'document_key' => $invoice->document_key
                ],
                "La factura ha sido restaurada satisfactoriamente."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');
            return redirect('/facturas-emitidas')->withMessage('La factura ha sido restaurada satisfactoriamente.');
        }else{
            return redirect('/facturas-emitidas')->withError('Mes seleccionado ya fue cerrado');
        }
    }  
    
    public function downloadPdf($id) {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);
        $totalIvaDevuelto = 0;

        if ($invoice->total_iva_devuelto == 0) {
            foreach ($invoice->items as $item) {
                if ($invoice->payment_type == '02' && $item->product_type == 12) {
                    $totalIvaDevuelto += $item->iva_amount;
                }
            }
            $invoice->total_iva_devuelto = $totalIvaDevuelto;
        }
        $invoice->save();
        
        $invoiceUtils = new InvoiceUtils();
        $file = $invoiceUtils->downloadPdf( $invoice, currentCompanyModel() );
        $filename = $invoice->document_key . '.pdf';
        if( ! $invoice->document_key ) {
            $filename = $invoice->document_number . '-' . $invoice->client_id . '.pdf';
        }
        
        /*$headers = [
            'Content-Type' => 'application/pdf', 
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename'=> $filename
        ];*/
        return $file;
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
        
        try{
            $invoiceUtils = new InvoiceUtils();
            $path = $invoiceUtils->getXmlPath( $invoice, $company );
            $invoiceUtils->sendInvoiceEmail( $invoice, $company, $path );
        }catch( \Exception $e ){
            return back()->withError( 'El correo electrónico no pudo ser reenviado.');
        }
        
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $invoice,
            [
                'company_id' => $invoice->company_id,
                'id' => $invoice->id,
                'document_key' => $invoice->document_key
            ],
            "Se han reenviado los correos exitosamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return back()->withMessage( 'Se han reenviado los correos exitosamente.');
    }
    
    private function getDocReference($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
        if ($docType == '01') {
            $lastSale = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '04') {
            $lastSale = $company->last_ticket_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType, $company = false) {
        if(!$company){
            $company = currentCompanyModel();
        }
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
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType, $company).
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
    
    
    public function importExcelSM() {
        request()->validate([
          'archivo' => 'required',
        ]);
      

        $collection = Excel::toCollection( new InvoiceImportSM(), request()->file('archivo') );
        $companyId = currentCompany();
        $invoiceList = $collection->toArray()[0];
        
        try {
            Log::debug('Creando job de registro de facturas.');
            foreach (array_chunk ( $invoiceList, 100 ) as $facturas) {
                ProcessSendExcelInvoices::dispatch($facturas, $companyId)->onQueue('bulk');
            }
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo:" . $ex);
        }

        
        return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
        
        
    }

    public function envioMasivoExcel(){
        request()->validate([
          'archivo' => 'required',
        ]);
        $collection = Excel::toCollection( new InvoiceImport(), request()->file('archivo') );
        $companyId = currentCompany();
        $invoiceList = $collection->toArray()[0];
        try {
            Log::debug('Creando job de registro de facturas.');
            foreach (array_chunk ( $invoiceList, 200 ) as $facturas) {

                ProcessSendExcelInvoices::dispatch($facturas, $companyId)->onQueue('bulk');
            }
        }catch( \Throwable $ex ){
            Log::error("Error importando excel archivo:" . $ex);
        }
        return redirect('/facturas-emitidas')->withMessage('Facturas importados exitosamente, puede tardar unos minutos en ver los resultados reflejados. De lo contrario, contacte a soporte.');
    }

    public function guardarMasivoExcel($facturas, $companyId){
        $company = Company::find($companyId);
        $excelCollection = $facturas;
        
        Log::notice("$company->id_number importando ".count($excelCollection)." lineas... Last Invoice: $company->last_invoice_ref_number");
        $mainAct = $company->getActivities() ? $company->getActivities()[0]->code : 0;
        $i = 0;
        $invoiceList = array();
        foreach ($excelCollection as $row){
            try{

                $metodoGeneracion = "etax-excel";
                if($row['CedulaEmpresa'] == $company->id_number){

                    if( isset($row['Identificacion']) ){
                        $xls_invoice = XlsInvoice::updateOr
                            'id_number' => $row['consecutivo'],
                            'company_id' => $company->id,
                        ]
                    );
                        $xls_invoice->codigoActividad = $row['codigoActividad'];
                        $xls_invoice->nombreReceptor = $row['nombreReceptor'];
                        $xls_invoice->tipoIdentificacion = $row['tipoIdentificacion'];
                        $xls_invoice->Identificacion = $row['Identificacion'];
                        $xls_invoice->provincia = $row['provincia'];
                        $xls_invoice->canton = $row['canton'];
                        $xls_invoice->distrito = $row['distrito'];
                        $xls_invoice->direccion = $row['direccion'];
                        $xls_invoice->correo = $row['correo'];
                        $xls_invoice->condicionVenta = $row['condicionVenta'];
                        $xls_invoice->plazoCredito = $row['plazoCredito'];
                        $xls_invoice->medioPago = $row['medioPago'];
                        $xls_invoice->numeroLinea = $row['numeroLinea'];
                        $xls_invoice->cantidad = $row['cantidad'];
                        $xls_invoice->unidadMedida = $row['unidadMedida'];
                        $xls_invoice->detalle = $row['detalle'];
                        $xls_invoice->precioUnitario = $row['precioUnitario'];
                        $xls_invoice->montoTotal = $row['montoTotal'];
                        $xls_invoice->montoDescuento = $row['montoDescuento'];
                        $xls_invoice->naturalezaDescuento = $row['naturalezaDescuento'];
                        $xls_invoice->subTotal = $row['subTotal'];
                        $xls_invoice->codigoImpuesto = $row['codigoImpuesto'];
                        $xls_invoice->codigoTarifa = $row['codigoTarifa'];
                        $xls_invoice->tarifaImpuesto = $row['tarifaImpuesto'];
                        $xls_invoice->montoImpuesto = $row['montoImpuesto'];
                        $xls_invoice->tipoDocumentoExoneracion = $row['tipoDocumentoExoneracion'];
                        $xls_invoice->numeroDocumentoExoneracion = $row['numeroDocumentoExoneracion'];
                        $xls_invoice->nombreInstitucionExoneracion = $row['nombreInstitucionExoneracion'];
                        $xls_invoice->fechaEmisionExoneracion = $row['fechaEmisionExoneracion'];
                        $xls_invoice->porcentajeExoneracionExoneracion = $row['porcentajeExoneracionExoneracion'];
                        $xls_invoice->montoExoneracionExoneracion = $row['montoExoneracionExoneracion'];
                        $xls_invoice->montoTotalLinea = $row['montoTotalLinea'];
                        $xls_invoice->codigoMoneda = $row['codigoMoneda'];
                        $xls_invoice->tipoCambio = $row['tipoCambio'];
                        $xls_invoice->totalServGravados = $row['totalServGravados'];
                        $xls_invoice->totalServExentos = $row['totalServExentos'];
                        $xls_invoice->totalMercanciasGravadas = $row['totalMercanciasGravadas'];
                        $xls_invoice->totalMercanciasExentas = $row['totalMercanciasExentas'];
                        $xls_invoice->totalGravado = $row['totalGravado'];
                        $xls_invoice->totalExento = $row['totalExento'];
                        $xls_invoice->totalVenta = $row['totalVenta'];
                        $xls_invoice->totalDescuentos = $row['totalDescuentos'];
                        $xls_invoice->totalVentaNeta = $row['totalVentaNeta'];
                        $xls_invoice->totalImpuesto = $row['totalImpuesto'];
                        $xls_invoice->totalOtrosCargos = $row['totalOtrosCargos'];
                        $xls_invoice->totalComprobante = $row['totalComprobante'];
                    }else {
                        //Log::warning('Factura repetida en envio masivo '.$identificacionCliente);
                    }
                }else {
                    //Log::warning('Factura repetida en envio masivo '.$identificacionCliente);
                }
                   
            }catch( \Throwable $ex ){
                Log::error("Error en factura SM:" . $ex);
            }
        }
        $company->save();
        $userId = $company->user_id;
        Cache::forget("cache-currentcompany-$userId");
                
        Log::debug("Agregando facturas a queue");
        foreach($invoiceList as $fac){
            ProcessSendExcelSingleInvoice::dispatch($fac)->onQueue('imports');
        }
        Log::debug($i." facturas importadas por excel");
    }
        
}
