<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\CalculatedTax;
use App\Book;
use App\Invoice;
use App\Bill;
use App\Http\Controllers\CacheController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

/**
 * @group Controller - Libro contable
 *
 * Funciones de BookController
 */
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
    
    public function validar($cierre){
        $book = Book::join('calculated_taxes','calculated_taxes.id','books.calculated_tax_id')
            ->where('books.id',$cierre)->first();
        $invoices = Invoice::where(function ($query) use($book) {
            $query->where(['company_id'=> $book->company_id,'month'=> $book->month,'year'=> $book->year,'is_authorized' => true])
                ->where('commercial_activity', null);
        })->orWhere(function($query) use($book) {
            $query->where(['company_id'=> $book->company_id,'month'=> $book->month,'year'=> $book->year,'is_authorized' => true])
                ->whereHas('items', function ($query){
                $query->where('iva_type', null)->orwhere('product_type', null);
            });
        })->get();
        
        $bills = Bill::where(function ($query) use($book) {
            $query->where(['company_id'=> $book->company_id,'month'=> $book->month,'year'=> $book->year,'accept_status' => true])
                ->where('activity_company_verification', null);
        })->orWhere(function($query) use($book) {
            $query->where(['company_id'=> $book->company_id,'month'=> $book->month,'year'=> $book->year,'accept_status' => true])
                ->whereHas('items', function ($query){
                $query->where('iva_type', null)->orwhere('product_type', null);
            });
        })->get();

        $bloqueo = count($bills) + count($invoices);
        if($book->year == 2019){
            if($book->month < 6){
                $bloqueo = 0;
            }
        }
        if($bloqueo > 0){
            $retorno = array(
                "cierre" => $cierre,
                "bloqueo" => $bloqueo,
                "invoices" => $invoices,
                "bills" => $bills,
            ); 
            return view('Book/validar')->with('retorno',$retorno );
        }else{
            return $bloqueo;
        }
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
    

    public function retenciones_tarjeta($id){
        $company = currentCompanyModel();
        $retencion_porcentaje = $company->card_retention;
        $cierres = CalculatedTax::where('company_id', $company->id)
            ->where('id',$id)->first();
        $month = $cierres->month;
        $year = $cierres->year;
        $cerrado = $cierres->is_closed;
        $total_retenido = $cierres->retention_by_card;
        $invoices = Invoice::select('generated_date', 'document_number', 'client_first_name', 'client_last_name', 'client_last_name2','total','total as retencion')
            ->where('company_id',$company->id)
            ->whereYear('generated_date',$year)
            ->whereMonth('generated_date',$month)
            ->where('payment_type','02')
            ->get();
        $total_facturado = 0;
        $total_retencion = 0;
        foreach($invoices as $invoice){
            $total = $invoice->total;
            $invoice->retencion = $total * $retencion_porcentaje / 100;
            $total_facturado += $total;
            $total_retencion += $total * $retencion_porcentaje / 100;
        }
        $data = array(
            'cierre' => $id,
            'mes' => $month,
            'year' => $year,
            'cerrado'  => $cerrado,
            'total_facturado' => $total_facturado,
            'total_retencion' => $total_retencion,
            'total_retenido' => $total_retenido,
            'retencion_porcentaje' => $retencion_porcentaje
        );
        //dd($data);
        return view('Book.retenciones_tarjeta')->with('invoices', $invoices)->with('data', $data);
    }

    public function actualizar_retencion_tarjeta(Request $request){
        $company = currentCompanyModel();
        CalculatedTax::where('company_id', $company->id)
            ->where('id',$request->cierre)
            ->where('is_closed',0)
            ->update(['retention_by_card' => $request->total_retenido]);
        //dd($request);
        return redirect('/cierres')->withMessage('Retención por tarjeta actualizada exitosamente.');
    }

    
}
