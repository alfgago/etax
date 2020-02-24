<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Company;
use App\Client;
use App\Exports\ClientExport;
use App\Imports\ClientImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

/**
 * @group Controller - Clientes
 *
 * Funciones de ClientController
 */
class ClientController extends Controller
{
  
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('CheckSubscription');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Client/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $current_company = currentCompany();

        $query = Client::where('company_id', $current_company);
        
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($client) {
                return view('Client.actions', [
                    'data' => $client
                ])->render();
            })
            ->editColumn('es_exento', function(Client $client) {
                return $client->es_exento ? 'Sí' : 'No';
            })
            ->editColumn('tipo_persona', function(Client $client) {
                return $client->getTipoPersona();
            })
            ->addColumn('nombreC', function(Client $client) {
                return $client->getFullName();
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
        return view("Client/create");
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
          'tipo_persona' => 'required',
          'id_number' => 'required',
          'code' => 'required',
          'first_name' => 'required',
          'email' => 'required',
          'country' => 'required'
        ]);
      
        $cliente = new Client();
        $company = currentCompanyModel();    
        $cliente->company_id = $company->id;
      
        $cliente->tipo_persona = $request->tipo_persona;
        if($cliente->tipo_persona == "E" || $cliente->tipo_persona == "O"){
            $identificacionCliente = $request->id_number;
        }else{
            $identificacionCliente = preg_replace("/[^0-9]/", "", $request->id_number );
        }
        $cliente->id_number = $identificacionCliente;
        
        $cliente->code = $request->code;
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
        $cliente->address = $request->address ?? null;
        $cliente->foreign_address = $request->foreign_address ?? $cliente->address;
        $cliente->phone = $request->phone;
        $cliente->es_exento = $request->es_exento;
        //$cliente->billing_emails = $request->billing_emails;
        if (is_array ($request->billing_emails)) {
            $arrayEmails = array();
            foreach($request->billing_emails as $email){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($arrayEmails, $email);
                }else{
                    return redirect()->back()->withErrors('Los correos para facturación deben ser válidos');
                }
            }
            $cliente->billing_emails = implode(", ", $arrayEmails);
        }
        
        $cliente->email = $request->email;
        $cliente->fullname = $cliente->toString();
      
        $cliente->save();
      
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $cliente,
            [
                'company_id' => $cliente->company_id,
                'id' => $cliente->id
            ],
            "Cliente creado."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/clientes')->withMessage('Cliente creado');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);
        
       
        
        return view('Client/edit', compact('client') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
          'tipo_persona' => 'required',
          'id_number' => 'required',
          'code' => 'required',
          'first_name' => 'required',
          'email' => 'required',
          'country' => 'required'
        ]);
      
        $cliente = Client::findOrFail($id);
        $this->authorize('update', $cliente);
      
        $cliente->tipo_persona = $request->tipo_persona;
        $cliente->tipo_persona = $request->tipo_persona;
        if($cliente->tipo_persona == "E" || $cliente->tipo_persona == "O"){
            $identificacionCliente = $request->id_number;
        }else{
            $identificacionCliente = preg_replace("/[^0-9]/", "", $request->id_number );
        }
        $cliente->id_number = $identificacionCliente;
        $cliente->code = $request->code;
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
        $cliente->address = $request->address ?? null;
        $cliente->foreign_address = $request->foreign_address ?? $cliente->address;
        $cliente->phone = $request->phone;
        $cliente->es_exento = $request->es_exento;
        $cliente->billing_emails = $request->billing_emails;
        if (is_array ($cliente->billing_emails)) {
            $cliente->billing_emails = implode(", ",$cliente->billing_emails);
        }
        $cliente->email = $request->email;
        $cliente->fullname = $cliente->toString();
      
        $cliente->save();
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $cliente,
            [
                'company_id' => $cliente->company_id,
                'id' => $cliente->id
            ],
            "Cliente actualizado."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/clientes')->withMessage('Cliente actualizado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Client::find($id);
        $this->authorize('update', $cliente);
        $cliente->delete();
        
      $user = auth()->user();
        Activity::dispatch(
            $user,
            $cliente,
            [
                'company_id' => $cliente->company_id,
                'id' => $cliente->id
            ],
            "Cliente eliminado."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/clientes')->withMessage('Cliente eliminado');
    }
    
    public function export() {
        return Excel::download(new ClientExport(), 'clientes.xlsx');
    }
    
    public function import() {
        
        request()->validate([
          'archivo' => 'required',
          'tipo_archivo' => 'required',
        ]);
      
        $time_start = getMicrotime();
        
        $clientes = Excel::toCollection( new ClientImport(), request()->file('archivo') );
        $company_id = currentCompany();  
        foreach ($clientes[0] as $row){
            
            $zip = $row['codigopostal'];
            $row['provincia'] = substr($zip, 0, 1);
            $row['canton'] = substr($zip, 0, 3);
            $row['distrito'] = $zip;
            
            /*if( $row['canton'] ) {
                if( strlen( (int)$row['canton'] ) <= 2 ) {
                    $row['canton'] = (int)$row['provincia'] . str_pad((int)$row['canton'], 2, '0', STR_PAD_LEFT);
                }
            }
            
            if( $row['distrito'] ) {
                if( strlen( $row['distrito'] ) > 4 ) {
                    $zip = (int)$row['distrito'];
                }else{
                    $row['distrito'] = (int)$row['canton'] . str_pad((int)$row['distrito'], 2, '0', STR_PAD_LEFT);
                    $zip = $row['distrito'];
                }
            }*/
            
            $correosCopia = $row['correoscopia'];
            $correosCopia = str_replace(";", ",", $correosCopia);
            
            
            $cliente = Client::updateOrCreate(
                [
                    'id_number' => $row['identificacion'],
                    'company_id' => $company_id,
                ],
                [
                    'code' => $row['codigo'] ? $row['codigo'] : 'No indica',
                    'company_id' => $company_id,
                    'tipo_persona' => $row['tipopersona'],
                    'id_number' => $row['identificacion'],
                    'first_name' => $row['nombre'],
                    'last_name' => $row['primerapellido'],
                    'last_name2' => $row['segundoapellido'],
                    'email' => $row['correo'],
                    'billing_emails' => $correosCopia,
                    'country' => $row['pais'],
                    'state' => $row['provincia'],
                    'city' => $row['canton'],
                    'district' => $row['distrito'],
                    'zip' => $zip,
                    'neighborhood' => $row['barrio'],
                    'address' => $row['direccion'],
                    'foreign_address' => $row['direccion'],
                    'phone_area' => $row['areatel'],
                    'phone' => $row['telefono'],
                    'es_exento' => $row['exento'][0] == 'S',
                    'emisor_receptor' => $row['emisorreceptor']
                ]
            );

          $user = auth()->user();
          Activity::dispatch(
              $user,
              $cliente,
              [
                  'company_id' => $cliente->company_id,
                  'id' => $cliente->id
              ],
              "Cliente creado por excel."
          )->onConnection(config('etax.queue_connections'))
          ->onQueue('log_queue');
              
        }
        $time_end = getMicrotime();
        $time = $time_end - $time_start;
        
        return redirect('/clientes')->withMessage('Clientes importados exitosamente en '.$time.'s');
    }
    
    /**
     * Restore the specific item
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $rest = Client::onlyTrashed()->where('id', $id)->first();
        if( $rest->company_id != currentCompany() ){
            return 404;
        }
        $rest->restore();
        $user = auth()->user();
          Activity::dispatch(
              $user,
              $rest,
              [
                  'company_id' => $rest->company_id,
                  'id' => $rest->id
              ],
              "El cliente ha sido restaurado satisfactoriamente."
          )->onConnection(config('etax.queue_connections'))
          ->onQueue('log_queue');
              
        return redirect('/clientes')->withMessage('El cliente ha sido restaurado satisfactoriamente.');
    }   
    
    
    public function select2RemoteDataSource (Request $request)
    {
        $search = $request->get('search');
        $data = Client::select(['id', 'first_name', 'last_name', 'last_name2', 'id_number'])
            ->where('id_number', 'like', '%' . $search . '%')
            ->orWhere('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhere('last_name2', 'like', '%' . $search . '%')->orderBy('first_name')->paginate(10);
        return response()->json(['items' => $data->toArray()['data'], 'pagination' => $data->nextPageUrl() ? true : false]);
    }
    
    
}
