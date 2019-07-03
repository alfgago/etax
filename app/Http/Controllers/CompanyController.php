<?php

namespace App\Http\Controllers;

use App\Actividades;
use App\AvailableInvoices;
use App\Company;
use App\EtaxProducts;
use App\Payment;
use App\PaymentMethod;
use App\Sales;
use App\SubscriptionPlan;
use App\Utils\PaymentUtils;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AtvCertificate;
use App\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUser;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use stdClass;

class CompanyController extends Controller {
    
    use SoftDeletes;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        if( ! auth()->user()->isContador() ) {
            return redirect('/')->withMessage('Usted no tiene una cuenta de contador.');
        }
        
        $available_companies_count = User::checkCountAvailableCompanies();
        
        if ($available_companies_count == 0) {
            return redirect()->route('User.companies')->withError('Su cuenta actual no permite más empresas.');
        }
        $actividades = Actividades::all()->toArray();
        return view('Company.create')->with('actividades', $actividades);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'tipo_persona' => 'required',
            'id_number' => 'required|unique:companies',
            'name' => 'required',
            'email' => 'required',
            'country' => 'required'
        ]);

        $company = new Company();
        $userId = auth()->user()->id;
        $company->user_id = $userId;
        
        $sale =  \App\Sales::where('user_id', $userId)
                ->where('is_subscription', true)
                ->where('status', 1)
                ->first();
                
        if( !isset( $sale ) ) {
            return redirect('/')->withError( 'Usted no cuenta con los permisos necesarios para crear una nueva empresa.' );
        }

        $company->type = $request->tipo_persona;
        $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
        $company->name = $request->name;
        $company->last_name = $request->last_name;
        $company->last_name2 = $request->last_name2;
        $company->country = $request->country;
        $company->state = ($request->state != '0') ? $request->state : NULL;
        $company->city = ($request->city != '0') ? $request->city : NULL;
        $company->district = ($request->district != '0') ? $request->district : NULL;
        $company->neighborhood = $request->neighborhood;
        $company->zip = $request->zip;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->invoice_email = $request->invoice_email;
        $company->email = $request->email;
        $company->default_currency = !empty($request->default_currency) ? $request->default_currency : 'CRC';
        $commercial_activities = $request->main_comercial_activity;
        if($request->second_comercial_activity != ''){
            $commercial_activities = $request->main_comercial_activity . ',' . $request->second_comercial_activity;
        }
        $company->commercial_activities = $commercial_activities;

        /* Add company to a plan */
        $company->subscription_id = getCurrentSubscription()->id; //Solo el contador deberia poder, por lo que siempre va a existir un current user subscription.
        $company->save();

        /* Add Company to Team */
        $team = Team::firstOrCreate([
                'name' => "(".$company->id.") " . $company->id_number,
                'owner_id' => $userId,
                'company_id' => $company->id
            ]
        );

        auth()->user()->attachTeam($team);

        return redirect()->route('User.companies')->withMessage('La compañía ha sido agregada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show(Company $empresa) {
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        
        $subscription = getCurrentSubscription();
        if( ! isset( $subscription ) ) {
          return redirect('/elegir-plan');
        }
        
        $company = currentCompanyModel();
        
        if (!$company) {
            return redirect('/');
        }

        $users = User::with(['roles' => function($q) {
                        $q->where('name', 'admin');
                    }])->get();
        $team = Team::where('company_id', $company->id)->first();

        $certificate = AtvCertificate::where('company_id', $company->id)->first();
        $actividades = Actividades::all()->toArray();
        return view('Company.edit', compact('company', 'users', 'certificate', 'team', 'actividades'))->withTeam($team);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editConfiguracion() {
        
        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }
        
        return view('Company.edit-advanced', compact('company'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editCertificate() {

        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }

        $certificate = AtvCertificate::where('company_id', $company->id)->first();
        
        return view('Company.edit-certificate', compact('company', 'certificate'));
    }
    
        /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function editTeam() {
        $company = currentCompanyModel();

        if (!$company) {
            return redirect('/');
        }

        $users = User::with(['roles' => function($q) { $q->where('name', 'admin'); }])->get();
        $team = Team::where('company_id', $company->id)->first();

        /* Only owner of company can edit that company */
        if ( !auth()->user()->isOwnerOfTeam($team) ) {
            abort(401);
        }
        
        return view('Company.edit-team', compact('company', 'users', 'team'))->withTeam($team);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $company = Company::find($id);
        $this->authorize('update', $company);

        if (!$company) {
            return redirect('/');
        }
        
        $request->validate([
            'id_number' => 'required',
            'tipo_persona' => 'required',
            'name' => 'required',
            'email' => 'required',
            'country' => 'required'
        ]);
        
        if( $company->id_number != $request->id_number ) {
            $request->validate([
                'id_number' => 'required|unique:companies',
            ]);
        }

        $team = Team::where('company_id', $company->id)->first();
        
        if ($request->file('input_logo')) {
            
            if ( isset( $company->logo_url ) ){
                if (Storage::exists( $company->logo_url )) {
                    Storage::delete( $company->logo_url );
                }
            }
            
            $pathLogo = Storage::putFileAs(
                "empresa-$request->id_number", $request->file('input_logo'),
                "logo.".$request->file('input_logo')->getClientOriginalExtension()
            );
            $company->logo_url = $pathLogo;
        }

        $company->type = $request->tipo_persona;
        $company->id_number = preg_replace("/[^0-9]+/", "", $request->id_number);
        $company->business_name = $request->business_name;
        $company->activities = $request->activities;
        $company->name = $request->name;
        $company->last_name = $request->last_name;
        $company->last_name2 = $request->last_name2;
        $company->email = $request->email;
        $company->invoice_email = $request->invoice_email;
        $company->country = $request->country;
        $company->state = ($request->state != '0') ? $request->state : NULL;
        $company->city = ($request->city != '0') ? $request->city : NULL;
        $company->district = ($request->district != '0') ? $request->district : NULL;
        $company->neighborhood = $request->neighborhood;
        $company->zip = $request->zip;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->atv_validation = false;
        
        $company->commercial_activities = $request->commercial_activities;
        if ( is_array($company->commercial_activities) ) {
            $company->commercial_activities = implode( ", ", $company->commercial_activities );
        }
        
        $company->save();

        //Update Team name based on company
        /*$team->name = "(".$company->id.") " . $company->id_number;
        $team->save();*/

        return redirect()->route('Company.edit')->withMessage('La información de la empresa ha sido actualizada.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(Request $request, $id) {
        $company = Company::find($id);

        if (!$company) {
            return redirect()->back()->withError('No se ha encontrado una compañía a su nombre.');
        }

        $this->authorize('update', $company);

        $team = Team::where('company_id', $company->id)->first();

        $company->default_currency = $request->default_currency;
        $company->default_invoice_notes = $request->default_invoice_notes;
        $company->default_vat_code = $request->default_vat_code;
        $company->last_document = $request->last_document;
        $company->last_invoice_ref_number = $request->last_document ? getInvoiceReference($request->last_document) : 0;
        $company->last_document_rec = $request->last_document_rec;
        $company->last_rec_ref_number = $request->last_document_rec ? getInvoiceReference($request->last_document_rec) : 0;
        $company->first_prorrata = $request->first_prorrata;
        $company->first_prorrata_type = $request->first_prorrata_type;
        $company->use_invoicing = $request->use_invoicing;
        $company->atv_validation = false;
        
        if( $company->first_prorrata_type == 1 ) {
            $company->operative_prorrata = $request->first_prorrata;
            $company->operative_ratio1 = $request->operative_ratio1;
            $company->operative_ratio2 = $request->operative_ratio2;
            $company->operative_ratio3 = $request->operative_ratio3;
            $company->operative_ratio4 = $request->operative_ratio4;
        }

        $company->save();
        
        clearLastTaxesCache( $company->id, 2018);

        return redirect()->route('Company.edit_config')->withMessage('La configuración de la empresa ha sido actualizada.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function updateCertificado(Request $request, $id) {

        $company = Company::find($id);
        $company->atv_validation = false;
        $company->save();
        $this->authorize('update', $company);

        if (!$company) {
            return redirect()->back()->withError('No se ha encontrado una compañía a su nombre.');
        }
        
        if ( !$company->use_invoicing || !$request->file('cert') ) {
            return redirect()->back()->withError('Debe subir el certificado antes de guardar el formulario.');
        }

        $team = Team::where('company_id', $company->id)->first();

        $id_number = $company->id_number;
        
        if (Storage::exists("empresa-$id_number/$id_number.p12")) {
            Storage::delete("empresa-$id_number/$id_number.p12");
        }

        $pathCert = Storage::putFileAs(
            "empresa-$id_number", $request->file('cert'), "$id_number.p12"
        );

        $cert = AtvCertificate::firstOrNew(
            [
                'company_id' => $id,
            ]
        );

        $cert->user = $request->user;
        $cert->password = $request->password;
        $cert->key_url = $pathCert;
        $cert->pin = $request->pin;
        
        $cert->save();
        
        return redirect()->route('Company.edit_cert')->withMessage('El certificado ATV ha sido actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request ) {
        if( 'ELIMINAR' != $request->confirmacion ){
            return 404;
            return redirect('/')->withError('La compañía no pudo ser eliminada, verifique el texto de confirmación');
        }
        
        $user = auth()->user();
        $currentCompany = currentCompanyModel();
        
        if( $currentCompany->user_id != $user->id ) {
            Log::warning( 'El usuario '. $user->id .' intentó eliminar la empresa #' . $currentCompany->id . ' - ' . $currentCompany->id_number );
            return redirect('/')->withError('Usted no tiene permisos para eliminar la empresa');
        }
        
        Log::warning( 'El usuario '. $user->id .' eliminó la empresa #' . $currentCompany->id . ' - ' . $currentCompany->id_number );
        $currentCompany->delete();
        return redirect('/')->withSuccess('La empresa ha sido eliminada satisfactoriamente.');
    }

    public function changeCompany(Request $request) {

        if (!$request->ajax()) {
            return redirect()->back()->withError('Ha ocurrido un error, inténtelo de nuevo.');
        }

        try {
            $companyId = $request->companyId;
            $team = Team::where( 'company_id', $companyId )->first();
	        auth()->user()->switchTeam( $team );
        } catch( UserNotInTeamException $e )
        {
        	
        }
        
    }
    
    public function setProrrata2018PorFacturas() {

        $company = currentCompanyModel();
        $this->authorize('update', $company);

        clearLastTaxesCache( $company->id, 2018);
        $company->first_prorrata_type = 3;
        $company->save();

        return redirect('/facturas-emitidas')->with('success', 'Empiece calculando su prorrata 2018 ingresando todas sus facturas de dicho periodo.');
    }

    public function comprarFacturasVista(){
        return back()->withError( 'Compra de facturas adicionales deshabilitada hasta el 1 de Julio.' );
        
        $company = currentCompany();
        $sale = Sales::where('company_id', $company)->first();
        $producto = EtaxProducts::where('id', $sale->etax_product_id)->first();
        $availableInvoices = AvailableInvoices::where('company_id', $company)->first();
        $productosEtax = EtaxProducts::where('is_subscription', 0)->where('id', '!=', 15)->get(); //El 15 es el producto de cálculos de prorrata, creado por seeders.
        $paymentmethods = PaymentMethod::where('user_id', auth()->user()->id)->get();
        $invoices = $availableInvoices->monthly_quota - $availableInvoices->current_month_sent;
        
        return view('/Company/comprarFacturasView')->with('productosEtax', $productosEtax)
                                                        ->with('availableInvoices', $availableInvoices)
                                                        ->with('invoices', $invoices)
                                                        ->with('paymentmethods', $paymentmethods);
    }

    public function seleccionarCliente(Request $request){
        $request->product = json_decode($request->product);
        $product = $request->product;
        $payment_method = $request->payment_method;
        $user = auth()->user();
        if(isset($payment_method)){
            return view('payment/clientDataSelect')->with('product', $product)
                ->with('user', $user)
                ->with('payment_method', $payment_method);
        }else{
            return redirect()->back()->withErrors('Debe seleccionar un metodo de pago');
        }
    }

    public function comprarFacturas(Request $request){
        $date = Carbon::parse(now('America/Costa_Rica'));
        $current_company = currentCompany();
        $company = get_company_details($current_company);
        $available_company_invoices = ($company->additional_invoices == null) ? $available_company_invoices = 0 : $available_company_invoices = $company->additional_invoices;
        $product_id = $request->product_id;
        switch ($product_id){
            case 9:
                $additional_invoices = $available_company_invoices + 5;
            break;
            case 10:
                $additional_invoices = $available_company_invoices + 25;
            break;
            case 11:
                $additional_invoices = $available_company_invoices + 50;
            break;
            case 12:
                $additional_invoices = $available_company_invoices + 250;
            break;
            case 13:
                $additional_invoices = $available_company_invoices + 2000;
            break;
            case 14:
                $additional_invoices = $available_company_invoices + 5000;
            break;
        }
        $paymentUtils = new PaymentUtils();
        if(isset($payment_method)){
            $pagoProducto = $paymentUtils->comprarProductos($request);
            if($pagoProducto){
                $user = auth()->user();
                $invoiceData = new stdClass();
                $invoiceData->client_code = $request->id_number;
                $invoiceData->client_id_number = $request->id_number;
                $invoiceData->client_id = $request->user_id;
                $invoiceData->tipo_persona = $request->tipo_persona;
                $invoiceData->first_name = $request->first_name;
                $invoiceData->last_name = $request->last_name;
                $invoiceData->last_name2 = $request->last_name2;
                $invoiceData->country = $request->country;
                $invoiceData->state = $request->state;
                $invoiceData->city = $request->city;
                $invoiceData->district = $request->district;
                $invoiceData->neighborhood = $request->neighborhood;
                $invoiceData->zip = $request->zip;
                $invoiceData->address = $request->address;
                $invoiceData->phone = $request->phone;
                $invoiceData->es_exento = $request->es_exento;
                $invoiceData->email = $request->email;
                $invoiceData->expiry = $date->toDateTimeString();
                $invoiceData->amount = $request->product_price;
                $invoiceData->subtotal = $request->product_price;

                $item = new stdClass();
                $item->total = $request->product_price;
                $item->code = $request->product_id;
                $item->name = $request->product_name;
                $item->descuento = 0;
                $item->cantidad = 1;

                $invoiceData->items = [$item];
                $procesoFactura = $paymentUtils->facturarProductosEtax($invoiceData);
                $company->additional_invoices = $additional_invoices;
                $company->save();
                return redirect()->back()->withMessage('¡Gracias por su confianza! El pago ha sido recibido con éxito. Recibirá su factura al correo electrónico muy pronto.');
            }else{
                return redirect()->back()->withErrors('No pudo procesarse el pago');
            }
        }else{
            return redirect()->back()->withErrors('Debe incluir un método de pago');
        }
    }

    public function confirmCompanyDeactivation($token) {

        $company = Company::where('deactivation_token', $token)->first();

        if ($company) {

            /* Check Token validity for 24 Hours */
            if (!token_expired($token)) {
                if ($company->user_id == auth()->user()->id) {
                    if (empty($company->deleted_at)) {
                        $company->deactivation_token = null;
                        $company->save();
                        $company->delete();
                        Team::where('company_id', $company->id)->delete();
                        session(['current_company' => '']);
                        return redirect()->route('User.companies')->with('success', 'Company deactivated successfully.');
                    } else {
                        return redirect()->route('User.companies')->withErrors(['error' => 'Company already deactivated.']);
                    }
                } else {
                    return redirect()->route('User.companies')->withErrors(['error' => 'You are not authorize to deactivate this company.']);
                }
            } else {
                return redirect()->route('User.companies')->withErrors(['error' => 'Link has been Expired.']);
            }
        } else {
            return redirect()->route('User.companies')->withErrors(['error' => 'Invalid Link or Company already deactivated.']);
        }
    }
}
