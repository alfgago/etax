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
use Illuminate\Support\Facades\Cache;
use App\Exports\DeclaracionExport;

use DB;

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
    
    public function reports()
    {
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
        
        $userId = auth()->user()->id;
        Cache::forget("cache-currentcompany-$userId");
        
        $company = currentCompanyModel();
        $operativeData = $company->getOperativeData($ano);
        
        $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0 );
        $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0 );
        $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0 );
        $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0 );
        $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0 );
        $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0 );
        $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0 );
        $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0 );
        $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0 );
        $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0 );
        $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0 );
        $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0 );
        
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
        if($ano == 2019){
          $acumulado->sumAcumulados( $ano, true );
          $iva_deducible_estimado = $acumulado->iva_deducible_estimado;
          $iva_deducible_operativo = $acumulado->iva_deducible_operativo;
          $acumulado->setCalculosIVA( $operativeData->prorrata_operativa, 0 );
          $acumulado->iva_deducible_estimado = $iva_deducible_estimado;
        }

        $nombreMes = Variables::getMonthName($mes);
        $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0 );
        
        currentCompanyModel()->setFirstAvailableInvoices( $ano, $mes, $dataMes->count_invoices );
      
        if( !$request->vista || $request->vista == 'basica' ){
          return view('/Dashboard/dashboard-basico', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes', 'operativeData'));
        } else {
          return view('/Dashboard/dashboard-gerencial', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes', 'operativeData'));
        }

      }catch( \Throwable $ex ){
        Log::error( $ex->getMessage() );
        $this->forceRecalc($ano);
      }
      
    }
    
    public function reporteCuentasContables( Request $request ) {
      $time_start = $this->microtime_float();
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 0;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);

      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0 );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0 );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0 );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0 );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0 );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0 );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0 );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0 );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0 );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0 );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0 );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0 );
      
      $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0 );
      $nombreMes = Variables::getMonthName($mes);
      
      $acumulado = null;
      if($mes == 12 || $mes == 0){
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
      }
      
      return view('/Reports/reporte-cuentas', compact('data', 'ano', 'nombreMes', 'acumulado') );

    }
    
    public function reporteEjecutivo( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $operativeData = $company->getOperativeData($ano);
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0 );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0 );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0 );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0 );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0 );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0 );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0 );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0 );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0 );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0 );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0 );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0 );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
      $nombreMes = Variables::getMonthName($mes);
      $dataMes = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0 );
      
      return view('/Reports/reporte-resumen-ejecutivo', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes', 'operativeData'));

    }
    
    public function reporteBorradorIVA( Request $request ) {
        $ano = $request->ano ? $request->ano : 2019;
        $mes = $request->mes ? $request->mes : 7;
        $nombreMes = Variables::getMonthName($mes);
        $company = currentCompanyModel();
        $prorrataOperativa = $company->getProrrataOperativa($ano);

        $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0 );
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
        if($mes == 12){
          $acumulado->sumAcumulados( $ano, true );
          $iva_deducible_estimado = $acumulado->iva_deducible_estimado;
          $iva_deducible_operativo = $acumulado->iva_deducible_operativo;
          $acumulado->setCalculosIVA( $prorrataOperativa, 0 );
          $acumulado->iva_deducible_estimado = $iva_deducible_estimado;
          $acumulado->iva_deducible_operativo = $iva_deducible_operativo;
        }
        
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
    
    public function descargarDatosDeclaracion(Request $request){
      $ano = $request->ano ? $request->ano : 2019;
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $dataDeclaraciones = [];
      for($mes = 1; $mes <= 12; $mes++){
        $nombreMes = Variables::getMonthName($mes);
        $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0 );
        $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
        if($mes == 12){
          $acumulado->sumAcumulados( $ano, true );
          $iva_deducible_estimado = $acumulado->iva_deducible_estimado;
          $iva_deducible_operativo = $acumulado->iva_deducible_operativo;
          $acumulado->setCalculosIVA( $prorrataOperativa, 0 );
          $acumulado->iva_deducible_estimado = $iva_deducible_estimado;
          $acumulado->iva_deducible_operativo = $iva_deducible_operativo;
        }
        $dataDeclaracion = $data->calcularDeclaracion($acumulado);
        $dataDeclaracion['nombreMes'] = $nombreMes;
        $dataDeclaraciones[] = $dataDeclaracion;
      }

      return Excel::download(new DeclaracionExport($ano, $dataDeclaraciones), 'datos-declaracion.xlsx');
    }
    
    public function reporteDetalleDebitoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0 );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0 );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0 );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0 );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0 );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0 );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0 );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0 );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0 );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0 );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0 );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0 );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
      
      return view('/Reports/reporte-detalle-debito-fiscal', compact( 'acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'ano' ));

    }
    
    public function reporteDetalleCreditoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $company = currentCompanyModel();
      $prorrataOperativa = $company->getProrrataOperativa($ano);
      
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0 );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0 );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0 );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0 );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0 );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0 );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0 );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0 );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0 );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0 );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0 );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0 );

      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0 );
      
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
      $e = CalculatedTax::calcularFacturacionPorMesAno( 1, $ano, 0, true );
      $f = CalculatedTax::calcularFacturacionPorMesAno( 2, $ano, 0, true );
      $m = CalculatedTax::calcularFacturacionPorMesAno( 3, $ano, 0, true );
      $a = CalculatedTax::calcularFacturacionPorMesAno( 4, $ano, 0, true );
      $y = CalculatedTax::calcularFacturacionPorMesAno( 5, $ano, 0, true );
      $j = CalculatedTax::calcularFacturacionPorMesAno( 6, $ano, 0, true );
      $l = CalculatedTax::calcularFacturacionPorMesAno( 7, $ano, 0, true );
      $g = CalculatedTax::calcularFacturacionPorMesAno( 8, $ano, 0, true );
      $s = CalculatedTax::calcularFacturacionPorMesAno( 9, $ano, 0, true );
      $c = CalculatedTax::calcularFacturacionPorMesAno( 10, $ano, 0, true );
      $n = CalculatedTax::calcularFacturacionPorMesAno( 11, $ano, 0, true );
      $d = CalculatedTax::calcularFacturacionPorMesAno( 12, $ano, 0, true );
      $acumulado = CalculatedTax::calcularFacturacionPorMesAno( 0, $ano, 0, true );
    }

    public function reporteCompanies(Request $request)
    { 
      $date = Carbon::now();
      $mes3 = $date->monthName;
      $mes2 = $date->subMonth(1)->monthName;
      $mes1 = $date->subMonth(1)->monthName;

      $companies = DB::select( DB::raw("SELECT id_number,business_name, IF(commercial_activities IS NULL, 0,commercial_activities) commercial_activities,SUM( CASE WHEN (b.generated_date >= (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -3 MONTH)) AND b.generated_date < (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -2 MONTH))) THEN 1 ELSE 0 END) AS MES1, SUM( CASE WHEN (b.generated_date >= (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -2 MONTH)) AND b.generated_date < (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -1 MONTH))) THEN 1 ELSE 0 END) AS MES2, SUM( CASE WHEN (b.generated_date >= (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL -1 MONTH)) AND b.generated_date < CURRENT_TIMESTAMP()) THEN 1 ELSE 0 END) AS MES3 FROM companies c LEFT JOIN bills b ON c.id = company_id where id_number IS NOT NULL AND business_name IS NOT NULL GROUP BY id_number,business_name,commercial_activities ORDER BY `MES1` DESC, `MES2` DESC, `MES3` DESC, commercial_activities DESC"));

      return view('/Reports/reporte-companies', compact("companies","mes1","mes2","mes3"));
    }

}
