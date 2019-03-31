<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\Client;
use App\Exports\ClientExport;
use App\Imports\ClientImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

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
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_company = auth()->user()->companies->first()->id;
        $clients = Client::where('company_id', $current_company)->sortable()->paginate(10);
        
        return view('Client/index', [
          'clients' => $clients
        ]);
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
        $company = auth()->user()->companies->first();
        $cliente->company_id = $company->id;
      
        $cliente->tipo_persona = $request->tipo_persona;
        $cliente->id_number = $request->id_number;
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
        $cliente->address = $request->address;
        $cliente->phone = $request->phone;
        $cliente->es_exento = $request->es_exento;
        $cliente->billing_emails = $request->billing_emails;
        $cliente->email = $request->email;
      
        $cliente->save();
      
        return redirect('/clientes');
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
        $cliente->id_number = $request->id_number;
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
        $cliente->address = $request->address;
        $cliente->phone = $request->phone;
        $cliente->es_exento = $request->es_exento;
        $cliente->billing_emails = $request->billing_emails;
        $cliente->email = $request->email;
      
        $cliente->save();
      
        return redirect('/clientes');
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
        
        return redirect('/clientes');
    }
    
    public function export() {
        return Excel::download(new ClientExport(), 'clientes.xlsx');
    }
    
    public function import() {
        
        request()->validate([
          'archivo' => 'required',
          'tipo_archivo' => 'required',
        ]);
      
        $time_start = $this->microtime_float();
        
        $clientes = Excel::toCollection( new ClientImport(), request()->file('archivo') );
        $company_id = auth()->user()->companies->first()->id;
        foreach ($clientes[0] as $row){
            Client::updateOrCreate(
                [
                    'id_number' => $row['identificacion'],
                    'company_id' => $company_id,
                ],
                [
                    'code' => $row['codigo'] ? $row['codigo'] : '',
                    'company_id' => $company_id,
                    'tipo_persona' => $row['tipopersona'],
                    'id_number' => $row['identificacion'],
                    'first_name' => $row['nombre'],
                    'last_name' => $row['primerapellido'],
                    'last_name2' => $row['segundoapellido'],
                    'email' => $row['correo'],
                    'billing_emails' => $row['correoscopia'],
                    'country' => $row['pais'],
                    'state' => $row['provincia'],
                    'city' => $row['canton'],
                    'district' => $row['distrito'],
                    'neighborhood' => $row['barrio'],
                    'address' => $row['direccion'],
                    'phone_area' => $row['areatel'],
                    'phone' => $row['telefono'],
                    'es_exento' => $row['exento'],
                    'emisor_receptor' => $row['emisorreceptor']
                ]
            );
            
        }
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;
        
        return redirect('/clientes')->withMessage('Clientes importados exitosamente en '.$time.'s');
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
    
}
