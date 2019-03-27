<?php

namespace App\Http\Controllers;

use App\Company;
use App\Provider;
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
}
