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

/**
 * @group Controller - Planes de suscripción
 *
 * Funciones de SubscriptionPlanController.
 */
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
        $plans = SubscriptionPlan::get();
        //$plans = EtaxProducts::where('is_subscription', true)->with('plan')->get();
        return view( 'subscriptions/change-plan', compact('plans') );
        
    }
    
    public function selectPlan() {
        
        $plans = SubscriptionPlan::get();
        return view( 'subscriptions/subscription-wizard', compact('plans') );
        
    }
    
    public function startTrial() {
        
        $plans = SubscriptionPlan::get();
        return view( 'subscriptions/subscription-wizard-trial', compact('plans') );
        
    }
    
    public function confirmStartTrial(Request $request) {
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $user = auth()->user();
        if($request->plan_sel == "c"){
            $plan_tier = "Pro ($user->id)";
            $valido = 0;
            $precio_25 = 8;
            $precio_10 = 10;
            $precio_mes = 14.999;
            $precio_seis = 13.740;
            $precio_anual = 12.491;
            $total = 0;
            $total_extras = 0;
            $precio = 0;
            $coupons = Coupon::where('code',$request->codigo_contador)->where('type',1)->count();
            if($coupons != 0){
                $coupon = Coupon::where('code',$request->codigo_contador)->where('type',1)->first();
                $retorno = $coupon->amount;
                $precio_25 = $retorno;
                $precio_10 = $retorno;
                $precio_mes = $retorno;
                $precio_seis = $retorno;
                $precio_anual = $retorno;
                $precio = $retorno;
            }
            $planes = SubscriptionPlan::where('plan_tier',$plan_tier)->count();
            if($planes != 0){
                $plan = SubscriptionPlan::where('plan_tier',$plan_tier)->first();
                $retorno = $plan->price;
                $precio_25 = $retorno;
                $precio_10 = $retorno;
                $precio_mes = $retorno;
                $precio_seis = $retorno;
                $precio_anual = $retorno;
                $precio = $retorno;
            }
            $cantidad = $request->num_companies;
            $total_extras = 0;
            if($cantidad > 25){
               $total_extras = ($cantidad - 25) * $precio_25;
               $cantidad = 25;
            }
            if($cantidad > 10){
               $total_extras += ($cantidad - 10) * $precio_10;
               $cantidad = 10;
            }
            $monthly_price = $cantidad * $precio_mes;
            $six_price = $cantidad * $precio_seis;
            $annual_price = $cantidad * $precio_anual;
            $monthly_price += $total_extras;
            $six_price += $total_extras;
            $annual_price += $total_extras;
            $six_price = $six_price * 6;
            $annual_price = $annual_price * 12;
            $plan = SubscriptionPlan::updateOrCreate(
                ['plan_tier' => $plan_tier],
                ['plan_type' => 'Contador',
                'num_companies' => $request->num_companies,
                'num_users' => 10,
                'num_invoices' => 10000,
                'ticket_sla' => 1,
                'call_center_vip' => 1,
                'setup_help' => 1,
                'multicurrency' => 1,
                'e_invoicing' => 1,
                'pre_invoicing' => 1,
                'vat_declaration' => 1,
                'basic_reports' => 1,
                'intermediate_reports' => 1,
                'advanced_reports' => 1,
                'monthly_price' => round($monthly_price,2),
                'six_price' => round($six_price,2),
                'annual_price' => round($annual_price,2),
                'created_at' => $start_date,
                'price'=>$precio]
            );
            $request->product_id = $plan->id;
        }
        //dd($request->product_id);
        $sale = Sales::startTrial( $request->product_id, $request->recurrency );
        Log::info('Nuevo suscriptor ha iniciado periodo de pruebas: ' . $user->email);
        return redirect('/wizard')->withMessage('¡Felicidades! Ha iniciado su prueba en eTax.');
    }
    
    public function confirmPlanChange(Request $request) {
        $start_date = Carbon::parse(now('America/Costa_Rica'));
        $user = auth()->user();
        if($request->plan_sel == "c"){
            $plan_tier = "Pro ($user->id)";

            $valido = 0;
            $precio_25 = 8;
            $precio_10 = 10;
            $precio_mes = 14.999;
            $precio_seis = 13.740;
            $precio_anual = 12.491;
            $total = 0;
            $total_extras = 0;
            $precio = 0;
            $coupons = Coupon::where('code',$request->codigo_contador)->where('type',1)->count();
            if($coupons != 0){
                $coupon = Coupon::where('code',$request->codigo_contador)->where('type',1)->first();
                $retorno = $coupon->amount;
                $precio_25 = $retorno;
                $precio_10 = $retorno;
                $precio_mes = $retorno;
                $precio_seis = $retorno;
                $precio_anual = $retorno;
                $precio = $retorno;
            }
            $planes = SubscriptionPlan::where('plan_tier',$plan_tier)->count();
            if($planes != 0){
                $plan = SubscriptionPlan::where('plan_tier',$plan_tier)->first();
                $retorno = $plan->price;
                $precio_25 = $retorno;
                $precio_10 = $retorno;
                $precio_mes = $retorno;
                $precio_seis = $retorno;
                $precio_anual = $retorno;
                $precio = $retorno;
            }
            $cantidad = $request->num_companies;
            $total_extras = 0;
            if($cantidad > 25){
               $total_extras = ($cantidad - 25) * $precio_25;
               $cantidad = 25;
            }
            if($cantidad > 10){
               $total_extras += ($cantidad - 10) * $precio_10;
               $cantidad = 10;
            }
            $monthly_price = $cantidad * $precio_mes;
            $six_price = $cantidad * $precio_seis;
            $annual_price = $cantidad * $precio_anual;
            $monthly_price += $total_extras;
            $six_price += $total_extras;
            $annual_price += $total_extras;
            $six_price = $six_price * 6;
            $annual_price = $annual_price * 12;
            $plan = SubscriptionPlan::updateOrCreate(
                ['plan_tier' => $plan_tier],
                ['plan_type' => 'Contador',
                'num_companies' => $request->num_companies,
                'num_users' => 10,
                'num_invoices' => 10000,
                'ticket_sla' => 1,
                'call_center_vip' => 1,
                'setup_help' => 1,
                'multicurrency' => 1,
                'e_invoicing' => 1,
                'pre_invoicing' => 1,
                'vat_declaration' => 1,
                'basic_reports' => 1,
                'intermediate_reports' => 1,
                'advanced_reports' => 1,
                'monthly_price' => round($monthly_price,2),
                'six_price' => round($six_price,2),
                'annual_price' => round($annual_price,2),
                'created_at' => $start_date,
                'price'=>$precio]
            );
            $request->product_id = $plan->id;
        }
        $sale = Sales::createUpdateSubscriptionSale( $request->product_id, $request->recurrency );
        return redirect('/');
        
    }

    public function confirmCode(Request $request){
        $code = Coupon::where('code', $request->codigo)->where('type', 0)->first();
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

    public function confirmCodeAccount($codigo){
        $coupons = Coupon::where('code',$codigo)->where('type',1)->count();
        if($coupons != 0){
            $coupon = Coupon::where('code',$codigo)->where('type',1)->first();
            $retorno = $coupon->amount;
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
