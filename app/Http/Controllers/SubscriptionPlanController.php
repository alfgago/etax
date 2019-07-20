<?php

namespace App\Http\Controllers;

use App\User;
use App\SubscriptionPlan;
use App\EtaxProducts;
use App\Sales;
use App\Coupon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionPlanController extends Controller
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
        
    public function changePlan() {
        $plans = EtaxProducts::where('is_subscription', true)->with('plan')->get();
        return view( 'subscriptions/change-plan', compact('plans') );
        
    }
    
    public function selectPlan() {
        
        $plans = EtaxProducts::where('is_subscription', true)->with('plan')->get();
        return view( 'subscriptions/subscription-wizard', compact('plans') );
        
    }
    
    public function startTrial() {
        
        $plans = EtaxProducts::where('is_subscription', true)->with('plan')->get();
        return view( 'subscriptions/subscription-wizard-trial', compact('plans') );
        
    }
    
    public function confirmStartTrial(Request $request) {
        $user = auth()->user();
        $sale = Sales::startTrial( $request->product_id, $request->recurrency );
        Log::info('Nuevo suscriptor ha iniciado periodo de pruebas: ' . $user->email);
        return redirect('/wizard')->withMessage('¡Felicidades! Ha iniciado su prueba en eTax.');
        
    }
    
    public function confirmPlanChange(Request $request) {
        
        $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );
        return redirect('/');
        
    }

    public function confirmCode(Request $request){


        $code = Coupon::where('code', $request->codigo)->first();
        $retorno = array(
            "precio" => $request->precio,
            "nota" => ''
        );
        if( $request->codigo == $code->code ){
            $descuento = ($request->precio * $code->discount_percentage);
            $precio_final = $request->precio - $descuento;
            $nota = $code->promotion_name;
            if( $request->banco == 1 ) {
                $descuento = ($precio_final * 0.1);
                $precio_final = $precio_final - $descuento;
                $nota = $code->promotion_name .' + 10% BN Nacional ';
            }
            if($precio_final < 0){
                $precio_final = 0;
            }
            $nota = '( DESCUENTO: '.$code->discount_percentage .'% '. $nota .')';
            $retorno = array(
                "precio" => $precio_final,
                "nota" => $nota
            );
        }
            
        return $retorno;


    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        //
    }
    
    public function all()
    {
        if( auth()->user()->user_name != "alfgago" ) {
            return redirect(404);
        }
        
        $users = User::paginate(10);
        
        
        return view('subscriptions/all', [
          'users' => $users
        ]);
    }
    
    public function exportar() {
        if( auth()->user()->user_name != "alfgago" ) {
            return redirect(404);
        }
        return Excel::download(new UsersExport(), 'usuarios.xlsx');
    }
    
    private function getDocReference($docType) {
        $lastSale = currentCompanyModel()->last_invoice_ref_number + 1;
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    private function getDocumentKey($docType) {
        $company = currentCompanyModel();
        $invoice = new Invoice();
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef(currentCompanyModel()->last_invoice_ref_number + 1);


        return $key;
    }
    
}
