<?php

namespace App\Http\Controllers;

use App\User;
use App\SubscriptionPlan;
use App\EtaxProducts;
use App\Sales;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Carbon\Carbon;

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
        
        $plans = EtaxProducts::where('isSubscription', true)->with('plan')->get();
        return view( 'Subscriptions/change-plan', compact('plans') );
        
    }
    
    public function confirmPlanChange(Request $request) {
        
        $company = currentCompanyModel();
        $user = auth()->user();
        
        $start_date = Carbon::parse( now('America/Costa_Rica') );
        $trial_end_date = $start_date->addDays(1);
        $next_payment_date = $start_date->addMonths(1);
        
        $sale = Sales::updateOrCreate (
            [ 
                'user_id' => $user->id 
            ],
            [ 
                'company_id' => $company->id,
                'status'  => 1,
                'recurrency' => $request->recurrency,
                'trial_end_date' => $trial_end_date,
                'start_date' => $start_date, 
                'next_payment_date' => $next_payment_date, 
                'etax_product_id' => $request->product_id
            ]
        );
        
        return redirect('payment/payment-checkout');
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
        
        
        return view('Subscriptions/all', [
          'users' => $users
        ]);
    }
    
    public function exportar() {
        if( auth()->user()->user_name != "alfgago" ) {
            return redirect(404);
        }
        
        
        return Excel::download(new UsersExport(), 'usuarios.xlsx');
    }
    
}
