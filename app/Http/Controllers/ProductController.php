<?php

namespace App\Http\Controllers;

use App\Company;
use App\Product;
use App\UnidadMedicion;
use Illuminate\Http\Request;

class ProductController extends Controller
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
        return view('Product/index');
    }
    
    /**
     * Returns the required ajax data.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexData() {
        $current_company = currentCompany();

        $query = Product::where('company_id', $current_company)->where('is_catalogue', 1);
        return datatables()->eloquent( $query )
            ->orderColumn('reference_number', '-reference_number $1')
            ->addColumn('actions', function($product) {
                return view('datatables.actions', [
                    'routeName' => 'productos',
                    'deleteTitle' => 'Anular producto',
                    'editTitle' => 'Editar producto',
                    'hideDelete' => true,
                    'deleteIcon' => 'fa fa-ban',
                    'id' => $product->id
                ])->render();
            }) 
            ->editColumn('unidad_medicion', function(Product $product) {
                return $product->getUnidadMedicionName();
            })
            ->editColumn('tipo_iva', function(Product $product) {
                return $product->getTipoIVAName();
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
        $units = UnidadMedicion::all()->toArray();
        return view("Product/create", ['units' => $units]);
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
          'product_category_id' => 'required',
          'default_iva_type' => 'required',
        ]);
      
        $product = new Product();
        $company = currentCompanyModel();
        $product->company_id = $company->id;
      
        $product->code = $request->code;
        $product->name = $request->name;
        $product->product_category_id = $request->product_category_id;
        $product->measure_unit = $request->measure_unit;
        $product->unit_price = $request->unit_price;
        $product->description = $request->description;
        $product->default_iva_type = $request->default_iva_type;
        $product->is_catalogue = true;
      
        $product->save();
      
        return redirect('/productos');
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
        $this->authorize('update', $product);
        
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
          'code' => 'required',
          'name' => 'required',
          'measure_unit' => 'required',
          'unit_price' => 'required',
          'product_category_id' => 'required',
          'default_iva_type' => 'required',
        ]);
      
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
      
        $product->code = $request->code;
        $product->name = $request->name;
        $product->product_category_id = $request->product_category_id;
        $product->measure_unit = $request->measure_unit;
        $product->unit_price = $request->unit_price;
        $product->description = $request->description;
        $product->default_iva_type = $request->default_iva_type;
        $product->is_catalogue = true;
      
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
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);
        $product->delete();
        
        return redirect('/productos');
    }
}
