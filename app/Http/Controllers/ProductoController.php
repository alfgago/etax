<?php

namespace App\Http\Controllers;

use App\Company;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where('is_catalogue', 1)->get();
        return view('Product/index', [
          'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Product/create");
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
          'product_type' => 'required',
          'iva_type' => 'required',
        ]);
      
        $product = new Product();
        $empresa = Company::first();
        $product->empresa_id = $empresa->id;
      
        $product->codigo = $request->codigo;
        $product->nombre = $request->nombre;
        $product->unidad_medicion = $request->unidad_medicion;
        $product->precio_unitario = $request->precio_unitario;
        $product->descripcion = $request->descripcion;
        $product->tipo_product = $request->tipo_product;
        $product->tipo_iva_defecto = $request->tipo_iva_defecto;
        $product->es_catalogo = true;
      
        $product->save();
      
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('Product/edit', compact('product') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
          'codigo' => 'required',
          'nombre' => 'required',
          'unidad_medicion' => 'required',
          'precio_unitario' => 'required',
          'tipo_product' => 'required',
          'tipo_iva_defecto' => 'required',
        ]);
      
        $product = Product::findOrFail($id);
      
        $product->codigo = $request->codigo;
        $product->nombre = $request->nombre;
        $product->unidad_medicion = $request->unidad_medicion;
        $product->precio_unitario = $request->precio_unitario;
        $product->descripcion = $request->descripcion;
        $product->tipo_product = $request->tipo_product;
        $product->tipo_iva_defecto = $request->tipo_iva_defecto;
        $product->es_catalogo = true;
      
        $product->save();
      
        return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::find($id)->delete();
        return redirect('/products');
    }
}
