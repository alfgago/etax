<?php

namespace App\Http\Controllers;

use App\Jobs\LogActivityHandler as Activity;
use \Carbon\Carbon;
use App\Company;
use App\BillItem;
use App\InvoiceItem;
use App\User;
use App\CalculatedTax;
use App\Variables;
use App\PlansInvitation;
use Mpociot\Teamwork\Facades\Teamwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

/**
 * @group Controller - Reportes
 *
 * Funciones de ReportsController
 */
class ReportsController extends Controller
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
  
    public function dashboard() {
      $user = auth()->user();
      $mostrar_dashboard = 0;
      if(in_array(1, $user->permisos()) || in_array(6, $user->permisos()) || in_array(8, $user->permisos())){
        $mostrar_dashboard = 1;
      }  
      if( !$user->has_klap_user ) {
          //$user->createKlapUser();
      }
      
      /* Logic for New User Invite */
      $token = session('invite_token');
      if ($token) {
          $invite = Teamwork::getInviteFromAcceptToken($token);
          if ($invite) {
              Teamwork::acceptInvite($invite);
              /* Add entry in plan invitations table */                
              $team = \App\Team::findOrFail($invite->team_id);
              $company = Company::find($team->company_id);

              $is_admin = ($invite->role == 'admin') ? '1' : '0';
              $is_readonly = ($invite->role == 'readonly') ? '1' : '0';
              PlansInvitation::create(['subscription_id' => $company->subscription_id, 'company_id' => $company->id, 'user_id' => auth()->user()->id]);
              
              auth()->user()->switchTeam( $team );
              
              return redirect()->route('User.companies')->withMessage('La invitación ha sido aceptada.');
          }
      }
      
      $subscription = getCurrentSubscription();
      if( ! isset( $subscription ) ) {
          return redirect('/periodo-pruebas');
      }
      
      if( $subscription->status != 1 && $subscription->status != 4 ){
        return redirect('/elegir-plan');
      }
      
      if( !currentCompanyModel()->wizard_finished ) {
        if(in_array(8, $user->permisos()) ){
          return redirect('/gosocket/configuracion');
        }
        return redirect('/wizard');
      }
      return view('/Dashboard/index', compact( 'subscription','mostrar_dashboard' ) );
      
    }
    
    public function reports() {
      
      return view('/Reports/index');

    }
    
    public function reporteLibroVentas( Request $request ) {
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $companyID = currentCompany();
      
      $data = InvoiceItem::where('month', $mes)
                      ->where('year', $ano)
                      ->where('company_id', $companyID)
                      ->with('invoice','ivaType', 'productCategory', 'invoice.client')
                        ->orderBy('created_at', 'ASC')
                        ->orderBy('item_number', 'ASC')
                        ->get();
      
      $nombreMes = Variables::getMonthName($mes);
      
      return view('/Reports/reporte-libro-ventas', compact('data', 'ano', 'nombreMes') );

    }
    
    public function reporteLibroCompras( Request $request ) {
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $companyID = currentCompany();
      
      $data = BillItem::where('month', $mes)
                      ->where('year', $ano)
                      ->where('company_id', $companyID)
                      ->with('bill', 'bill.provider','ivaType', 'productCategory')
                        ->orderBy('created_at', 'ASC')
                        ->orderBy('item_number', 'ASC')
                        ->get();
      
      $nombreMes = Variables::getMonthName($mes);
      
      return view('/Reports/reporte-libro-compras', compact('data', 'ano', 'nombreMes') );

    }
    
    public function reporteDashboard( Request $request ) {
      try {
        $ano = $request->ano ? $request->ano : 2019;
        $mes = $request->mes ? $request->mes : 1;
        
        $company = currentCompanyModel();
        $prorrataOperativa = $company->getProrrataOperativa($ano);
        
        $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa );
        $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa );
        $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa );
        $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa );
        $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa );
        $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa );
        $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa );
        $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa );
        $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa );
        $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa );
        $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa );
        $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa );
        
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );

        $nombreMes = Variables::getMonthName($mes);
        $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
        
        currentCompanyModel()->setFirstAvailableInvoices( $ano, $mes, $dataMes->count_invoices );

      }catch( \Throwable $ex ){
        $this->forceRecalc($ano);
        Log::error('Error al cargar dashboard' . $ex->getMessage());
      }
      
      if( !$request->vista || $request->vista == 'basica' ){
        
        return view('/Dashboard/dashboard-basico', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));
      } else {
        return view('/Dashboard/dashboard-gerencial', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));
      }
      
    }
    
    public function reporteCuentasContables( Request $request ) {
      $time_start = $this->microtime_float();
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 0;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);

      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa );
      
      $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      
      return view('/Reports/reporte-cuentas', compact('data', 'ano', 'nombreMes') );

    }
    
    public function reporteEjecutivo( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-resumen-ejecutivo', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));

    }
    
    public function reporteBorradorIVA( Request $request ) {
        $ano = $request->ano ? $request->ano : 2019;
        $mes = $request->mes ? $request->mes : 7;
        $nombreMes = Variables::getMonthName($mes);
        $company = currentCompanyModel();
        $prorrataOperativa = $company->getProrrataOperativa($ano);

        $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
        
        if( !$data->book ) {
          return view('/Reports/no-data', compact('nombreMes') );
        }
        
        $dataDeclaracion = $data->calcularDeclaracion($acumulado);
        
        if(!$dataDeclaracion){
          $this->forceRecalc($ano);
          return view('/Reports/no-data', compact('nombreMes') );
        }
        
        return view('/Reports/reporte-borrador-iva', compact('dataDeclaracion') );
    }
    
    public function reporteDetalleDebitoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-detalle-debito-fiscal', compact( 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'ano' ));

    }
    
    public function reporteDetalleCreditoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa );
      
      return view('/Reports/reporte-detalle-credito-fiscal', compact( 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'ano' ));

    }
  
    public function changeCompany(Request $request) {

        if (!$request->ajax()) {
            abort(403);
        }

        $company_id_no = $request->id;
        $company = Company::where('id_number', trim($company_id_no))->first();

        $url = '/';

        if (!empty($company)) {
            session(['current_company' => $company->id]);

            $team = \App\Team::where('company_id', $company->id)->first();

            $url = ($request->is_edit == 1) ? '/empresas/' . $company->id . '/edit' : (($request->is_edit == 3) ? '/companies/permissions/' . $team->id : '/empresas/company-profile/' . $company->id);
        }

        return $url;
    }

    private function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float)$sec);
    }
    
    
    public function forceRecalc($ano){
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, $prorrataOperativa, true );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, $prorrataOperativa, true );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, $prorrataOperativa, true );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, $prorrataOperativa, true );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, $prorrataOperativa, true );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, $prorrataOperativa, true );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, $prorrataOperativa, true );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, $prorrataOperativa, true );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, $prorrataOperativa, true );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, $prorrataOperativa, true );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, $prorrataOperativa, true );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, $prorrataOperativa, true );
      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, $prorrataOperativa, true );
    }

}
