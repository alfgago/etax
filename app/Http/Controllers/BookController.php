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
        $current_company = currentCompany();
        $books = CalculatedTax::where([ 
            ['company_id', $current_company],
            ['month', '!=', 0],
            ['month', '!=', -1],
            ['year', '!=', 2018]
        ])
        ->orderBy('year', 'DESC')->orderBy('month', 'DESC')->orderBy('created_at', 'DESC')->get();
        
        foreach ( $books as $book ) {
            if( ! $book->is_closed ) {
                $book = CalculatedTax::calcularFacturacionPorMesAno( $book->month, $book->year, 0, $book->saldo_favor_anterior );
            }
        }
        
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
        $current_company = currentCompany();  
        $calc = CalculatedTax::findOrFail($id);
        $this->authorize('update', $calc);
        
        $mes = $calc->month;
        $ano = $calc->year;
        
        $prevOpenBooks = CalculatedTax::where([ 
            ['company_id', $current_company],
            ['is_closed', false],
            ['year', $ano],
            ['month', '<', $mes],
            ['month', '!=', 0],
            ['month', '!=', -1]
        ])->count();
        
        if( $prevOpenBooks ) {
          return redirect()->back()->with('error', "Debe cerrar los asientos anteriores antes de cerrar el $mes/$ano." );
        }
      
        $calc->is_closed = true;
        $calc->save();
        clearCierreCache($current_company, $mes, $ano);
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
        $current_company = currentCompany();
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
            
            clearCierreCache($current_company, $mes, $ano);
            clearTaxesCache($current_company, $mes, $ano);
            clearTaxesCache($current_company, $mes+1, $ano);
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
