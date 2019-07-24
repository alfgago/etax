<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\Provider;
use App\Exports\ProviderExport;
use App\Imports\ProviderImport;
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
        return view('Provider/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $current_company = currentCompany();

        $query = Provider::where('company_id', $current_company);
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($proveedor) {
                return view('datatables.actions', [
                    'routeName' => 'proveedores',
                    'deleteTitle' => 'Eliminar proveedor',
                    'hideDelete' => true,
                    'editTitle' => 'Editar proveedor',
                    'deleteIcon' => 'fa fa-trash-o',
                    'id' => $proveedor->id
                ])->render();
            })
            ->editColumn('es_exento', function(Provider $proveedor) {
                return $proveedor->es_exento ? 'Sí' : 'No';
            })
            ->editColumn('tipo_persona', function(Provider $proveedor) {
                return $proveedor->getTipoPersona();
            })
            ->addColumn('nombreC', function(Provider $proveedor) {
                return $proveedor->getFullName();
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
        $company = currentCompanyModel();    
        $provider->company_id = $company->id;
      
        $provider->tipo_persona = $request->tipo_persona;
        $respValidateIdNumber = validate_IdNumber($request->id_number, $request->tipo_persona);
        if($respValidateIdNumber != true){
            return redirect()->back()->withError('Verifique la cantidad de dígitos de la identificación');
        }
        $provider->id_number = preg_replace("/[^0-9]/", "", $request->id_number );
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
        $provider->address = $request->address ?? null;
        $provider->foreign_address = $request->foreign_address ?? $provider->address;
        $provider->phone = $request->phone;
        $provider->email = $request->email;
        $provider->fullname = $provider->toString();
      
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
        $respValidateIdNumber = validate_IdNumber($request->id_number, $request->tipo_persona);
        if($respValidateIdNumber != true){
            return redirect()->back()->withError('Verifique la cantidad de dígitos de la identificación');
        }
        $provider->id_number = preg_replace("/[^0-9]/", "", $request->id_number );
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
        $provider->address = $request->address ?? null;
        $provider->foreign_address = $request->foreign_address ?? $provider->address;
        $provider->phone = $request->phone;
        $provider->email = $request->email;
        $provider->fullname = $provider->toString();
      
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
        //$provider->delete();
        
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
      
        $time_start = getMicrotime();
        
        $proveedors = Excel::toCollection( new ProviderImport(), request()->file('archivo') );
        $company_id = currentCompany(); 
        foreach ($proveedors[0] as $row){
            
            $zip = 0;
            
            if( $row['canton'] ) {
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
            }
            
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
                    'country' => $row['pais'],
                    'state' => $row['provincia'],
                    'city' => $row['canton'],
                    'district' => $row['distrito'],
                    'zip' => $zip,
                    'neighborhood' => $row['barrio'],
                    'address' => $row['direccion'],
                    'phone' => $row['telefono'],
                    'es_exento' => false
                ]
            );
            
        }
        $time_end = getMicrotime();
        $time = $time_end - $time_start;
        
        return redirect('/proveedores')->withMessage('Proveedores importados exitosamente en '.$time.'s');
    }
    
    /**
     * Restore the specific item
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $rest = Provider::onlyTrashed()->where('id', $id)->first();
        if( $rest->company_id != currentCompany() ){
            return 404;
        }
        $rest->restore();
        
        return redirect('/proveedores')->withMessage('El proveedor ha sido restaurado satisfactoriamente.');
    }    
    
}
