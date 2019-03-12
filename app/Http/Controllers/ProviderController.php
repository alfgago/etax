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
        $providers = Provider::where('company_id', $current_company)->get();
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
          'code' => 'required',
          'name' => 'required',
          'measure_unit' => 'required',
          'unit_price' => 'required',
          'provider_type' => 'required',
          'default_iva_type' => 'required',
        ]);
      
        $provider = new Provider();
        $company = auth()->user()->companies->first();
        $provider->company_id = $company->id;
      
        $provider->code = $request->code;
        $provider->name = $request->name;
        $provider->measure_unit = $request->measure_unit;
        $provider->unit_price = $request->unit_price;
        $provider->description = $request->description;
        $provider->default_iva_type = $request->default_iva_type;
        $provider->is_catalogue = true;
      
        $provider->save();
      
        return redirect('/providers');
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
          'code' => 'required',
          'name' => 'required',
          'measure_unit' => 'required',
          'unit_price' => 'required',
          'provider_type' => 'required',
          'default_iva_type' => 'required',
        ]);
      
        $provider = Provider::findOrFail($id);
        $this->authorize('update', $provider);
      
        $provider->code = $request->code;
        $provider->name = $request->name;
        $provider->measure_unit = $request->measure_unit;
        $provider->unit_price = $request->unit_price;
        $provider->description = $request->description;
        $provider->default_iva_type = $request->default_iva_type;
        $provider->is_catalogue = true;
      
        $provider->save();
      
        return redirect('/providers');
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
        
        return redirect('/providers');
    }
}
