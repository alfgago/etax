<?php

namespace App\Http\Controllers;

use App\Company;
use App\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $providers = Provider:all();
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
        $empresa = Company::first();
        $provider->empresa_id = $empresa->id;
      
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
        Provider::find($id)->delete();
        return redirect('/providers');
    }
}
