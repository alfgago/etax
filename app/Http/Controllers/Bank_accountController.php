<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BankAccount;
use \Carbon\Carbon;

class Bank_accountController extends Controller
{
    

    public function index(){
    
        $company = currentCompanyModel()->id;
    	$cuentas = BankAccount::where('company_id',$company)->get();
    	//dd($cuentas);
    	return view('bank_account.index')->with('cuentas',$cuentas);
    }

    public function crear(){
    	return view('bank_account.formulario');
    }

    public function guardar(Request $request){
        $fecha = Carbon::now()->setTimezone('America/Costa_Rica')->format('Y-m-d h:m:s');
        $company = currentCompanyModel()->id;
        BankAccount::insert([
            ['account' => $request->account,
             'company_id' => $company,
            'bank' =>  $request->bank,
            'currency' =>  $request->currency,
            'created_at' => $fecha]
        ]);

        return redirect('/bank-account')->withMessage('Cuenta agregada exitosamente');
    }


    public function editar($id){
        $company = currentCompanyModel()->id;
        $cuenta = BankAccount::where('company_id',$company)
                ->where('id',$id)->first();
        //dd($cuenta);
        return view('bank_account.formulario_editar')->with('cuenta',$cuenta);
    }

    public function actualizar(Request $request){
        $fecha = Carbon::now()->setTimezone('America/Costa_Rica')->format('Y-m-d h:m:s');
        $company = currentCompanyModel()->id;
        BankAccount:: where('id', $request->bank_account_id)->where('company_id', $company)
            ->update(['account' => $request->account,
            'bank' =>  $request->bank,
            'currency' =>  $request->currency,
            'updated_at' => $fecha]);

        return redirect('/bank-account')->withMessage('Cuenta actualizada exitosamente');
    }
}
