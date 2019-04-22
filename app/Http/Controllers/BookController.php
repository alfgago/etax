<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\CalculatedTax;
use App\Book;
use App\Http\Controllers\CacheController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class BookController extends Controller
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
        $books = CalculatedTax::where([ 
            ['company_id', $current_company],
            ['month', '!=', 0],
            ['month', '!=', -1]
        ])->orderBy('month', 'DESC')->orderBy('year', 'DESC')->orderBy('created_at', 'DESC')->paginate(10);
        
        return view('Book/index', [
          'books' => $books
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function close(Request $request, $id)
    {      
        $current_company = auth()->user()->companies->first()->id;  
        $calc = CalculatedTax::findOrFail($id);
        $this->authorize('update', $calc);
      
        $calc->is_closed = true;
      
        $calc->save();
        
        $mes = $calc->month;
        $ano = $calc->year;
        CacheController::clearCierreCache($current_company, $mes, $ano);
        $cacheKey = "cache-estadoCierre-$current_company-$mes-$ano";
        Cache::forever( $cacheKey, true );
      
        return redirect('/cierres')->withMessage('Cierres de mes satisfactorio');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function openForRectification(Request $request, $id)
    {      
        $current_company = auth()->user()->companies->first()->id;  
        $calc = CalculatedTax::findOrFail( $id );
        $this->authorize('update', $calc);
          
        if( $calc->is_closed && $calc->is_final ){
            if( !$calc->is_rectification ){
                //Crea un clon para la rectificacion
                $calcClone = $calc->replicate();
                $calcClone->is_rectification = true;
                $calcClone->is_closed = false;
                $calcClone->save();
                
                $calc->is_final = false;
                $calc->save();
            }else{
                $calc->is_closed = false;
                $calc->save();
            }
            
            $mes = $calc->month;
            $ano = $calc->year;
            
            CacheController::clearCierreCache($current_company, $mes, $ano);
            CacheController::clearTaxesCache($current_company, $mes, $ano);
            $cacheKey = "cache-estadoCierre-$current_company-$mes-$ano";
            Cache::forever( $cacheKey, false );
        }
      
        return redirect('/cierres')->withMessage('Rectificación abierta');
    }

    
    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
    

    
}
