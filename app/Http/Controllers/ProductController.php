<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use App\Company;
use App\Imports\ProductImport;
use App\Product;
use App\UnidadMedicion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Controller - Productos
 *
 * Funciones de ProductController
 */
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
        //$this->middleware('CheckSubscription');
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
    public function indexData( Request $request ) {
        $current_company = currentCompany();
        
        $query = Product::where('company_id', $current_company)->where('is_catalogue', 1);
        
        if( $request->get('filtro') == 0 ) {
            
            $query = $query->onlyTrashed();
        }
        
        
        return datatables()->eloquent( $query )
            ->addColumn('actions', function($product) {
                return view('Product.actions', [
                    'data' => $product
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
      
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $product,
            [
                'company_id' => $product->company_id,
                'id' => $product->id
            ],
            "El producto ha sido agregado satisfactoriamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/productos')->withMessage('El producto ha sido agregado satisfactoriamente.');
    }

    public function import() {

        request()->validate([
            'archivo' => 'required',
            'tipo_archivo' => 'required',
        ]);
        $productos = Excel::toCollection( new ProductImport(), request()->file('archivo') );
        $company_id = currentCompany();
        foreach ($productos[0] as $row){
            $product = Product::updateOrCreate(
                [
                    'code' => $row['codigo'] ? $row['codigo'] : '',
                    'company_id' => $company_id,
                ],

                [
                    'name' => $row['nombre'],
                    'measure_unit' => $row['unidadmedida'],
                    'unit_price' => $row['precio'],
                    'description' => $row['descripcion'],
                    'is_catalogue' => true,
                    'default_iva_type' => $row['codigoetax']
                ]
            );
            $user = auth()->user();
            Activity::dispatch(
                $user,
                $product,
                [
                    'company_id' => $product->company_id,
                    'id' => $product->id
                ],
                "El producto ha sido importado satisfactoriamente desde excel."
            )->onConnection(config('etax.queue_connections'))
            ->onQueue('log_queue');

        }
        $time_start = getMicrotime();


        $time_end = getMicrotime();
        $time = $time_end - $time_start;

        return redirect('/productos')->withMessage('Productos importados exitosamente en '.$time.'s');
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
        
        $units = UnidadMedicion::all()->toArray();
        return view('Product/edit', compact('product', 'units') );
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
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $product,
            [
                'company_id' => $product->company_id,
                'id' => $product->id
            ],
            "El producto ha sido editado satisfactoriamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/products')->withMessage('El producto ha sido editado satisfactoriamente.');
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
        
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $product,
            [
                'company_id' => $product->company_id,
                'id' => $product->id
            ],
            "El producto ha sido editado satisfactoriamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/productos')->withMessage('El producto ha sido eliminado satisfactoriamente.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()->where('id', $id)->first();
        if( $product->company_id != currentCompany() ){
            return 404;
        }
        $product->restore();
        
        $user = auth()->user();
        Activity::dispatch(
            $user,
            $product,
            [
                'company_id' => $product->company_id,
                'id' => $product->id
            ],
            "El producto ha sido recuperado satisfactoriamente."
        )->onConnection(config('etax.queue_connections'))
        ->onQueue('log_queue');
        return redirect('/productos')->withMessage('El producto ha sido recuperado satisfactoriamente.');
    }    

    public function consultarProductos(Request $request){
        if (!$request->ajax()) {
            return redirect()->back()->withError('Ha ocurrido un error, inténtelo de nuevo.');
        }
        try {
            $code = $request->id;
            $product = Product::where( 'code', $code )
                    ->where( 'company_id', currentCompany() )
                    ->first();
        } catch( UserNotInTeamException $e ){}
        return $product;
    }
}
