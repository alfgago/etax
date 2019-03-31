<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\Provider;
use App\Exports\ClientExport;
use App\Imports\ClientImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ProviderController extends Controller
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
        $providers = Provider::where('company_id', $current_company)->paginate(10);
        return view('Provider/index', [
          'providers' => $providers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Provider/create");
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
      
        $provider = new Provider();
        $company = auth()->user()->companies->first();
        $provider->company_id = $company->id;
      
        $provider->tipo_persona = $request->tipo_persona;
        $provider->id_number = $request->id_number;
        $provider->code = $request->code;
        $provider->first_name = $request->first_name;
        $provider->last_name = $request->last_name;
        $provider->last_name2 = $request->last_name2;
        $provider->country = $request->country;
        $provider->state = $request->state;
        $provider->city = $request->city;
        $provider->district = $request->district;
        $provider->neighborhood = $request->neighborhood;
        $provider->zip = $request->zip;
        $provider->address = $request->address;
        $provider->phone = $request->phone;
        $provider->email = $request->email;
      
        $provider->save();
      
        return redirect('/proveedores');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $provider = Provider::findOrFail($id);
        $this->authorize('update', $provider);
        return view('Provider/edit', compact('provider') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Provider  $provider
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
      
        $provider = Provider::findOrFail($id);
        $this->authorize('update', $provider);
      
        $provider->tipo_persona = $request->tipo_persona;
        $provider->id_number = $request->id_number;
        $provider->code = $request->code;
        $provider->first_name = $request->first_name;
        $provider->last_name = $request->last_name;
        $provider->last_name2 = $request->last_name2;
        $provider->country = $request->country;
        $provider->state = $request->state;
        $provider->city = $request->city;
        $provider->district = $request->district;
        $provider->neighborhood = $request->neighborhood;
        $provider->zip = $request->zip;
        $provider->address = $request->address;
        $provider->phone = $request->phone;
        $provider->email = $request->email;
      
        $provider->save();
      
        return redirect('/proveedores');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider = Provider::findOrFail($id);
        $this->authorize('update', $provider);
        $provider->delete();
        
        return redirect('/proveedores');
    }
    
    public function export() {
        return Excel::download(new ProviderExport(), 'proveedores.xlsx');
    }
    
    public function import() {
        
        request()->validate([
          'archivo' => 'required',
          'tipo_archivo' => 'required',
        ]);
      
        $time_start = $this->microtime_float();
        
        $proveedors = Excel::toCollection( new ProviderImport(), request()->file('archivo') );
        $company_id = auth()->user()->companies->first()->id;
        foreach ($proveedors[0] as $row){
            Provider::updateOrCreate(
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
                    'es_exento' => $row['exento']
                ]
            );
            
        }
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;
        
        return redirect('/proveedores')->withMessage('Proveedores importados exitosamente en '.$time.'s');
    }
    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
    
}
