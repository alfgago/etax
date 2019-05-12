<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Company;
use App\User;
use App\CalculatedTax;
use App\Variables;
use App\PlansInvitation;
use Mpociot\Teamwork\Facades\Teamwork;
use Illuminate\Http\Request;

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
                PlansInvitation::create(['plan_no' => $company->plan_no, 'company_id' => $company->id, 'user_id' => auth()->user()->id, 'is_admin' => $is_admin, 'is_read_only' => $is_readonly]);
                /* Ends here */

                return redirect()->route('User.companies')->with('success', 'Invitation has been accepted.');
            }
        }
        /* Logic Ends Here */

      return view('/Dashboard/index');

    }
    
    public function reports() {
      
      return view('/Reports/index');

    }
    
    public function reporteDashboard( Request $request ) {
      
      try {
        $ano = $request->ano ? $request->ano : 2019;
        $mes = $request->mes ? $request->mes : 1;
        
        $prorrataOperativa = $this->getProrrataOperativa($ano);
        
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
      }catch( \Exception $ex ){
          
      }
      
      return view('/Dashboard/reporte-dashboard', compact('acumulado', 'e', 'f', 'm', 'a', 'y', 'j', 'l', 'g', 's', 'c', 'n', 'd', 'dataMes', 'ano', 'nombreMes'));

    }
    
    public function reporteCuentasContables( Request $request ) {
      $time_start = $this->microtime_float();
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 0;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
      $data = CalculatedTax::calcularFacturacionPorMesAno( $mes, $ano, 0, $prorrataOperativa );
      $nombreMes = Variables::getMonthName($mes);
      
      $time_end = $this->microtime_float();
      $time = $time_end - $time_start;
      
      return view('/Reports/reporte-cuentas', compact('data', 'ano', 'nombreMes') );

    }
    
    public function reporteEjecutivo( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
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
    
    public function reporteDetalleDebitoFiscal( Request $request ) {
      
      $ano = $request->ano ? $request->ano : 2019;
      $mes = $request->mes ? $request->mes : 1;
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
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
      
      $prorrataOperativa = $this->getProrrataOperativa($ano);
      
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
    
    public function getProrrataOperativa( $ano ){
      $anoAnterior = $ano > 2018 ? $ano-1 : 2018;
      $company = currentCompanyModel();
      
      if($anoAnterior == 2018) {
        if( $company->first_prorrata_type == 1 ){
          $prorrataOperativa = $company->first_prorrata ? currentCompanyModel()->first_prorrata / 100 : 1;
        }else {
          $anterior = CalculatedTax::getProrrataPeriodoAnterior( $anoAnterior );
          $prorrataOperativa = $anterior->prorrata;
        }
      }else{
        $anterior = CalculatedTax::getProrrataPeriodoAnterior( $anoAnterior );
        $prorrataOperativa = $anterior->prorrata;
      }
      
      return $prorrataOperativa;
    }
  
    /** @author Akhil Bansal
     * This Method is used to switch companies workspaces
     * @param Request $request 
     * @return $url
     */
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
  
}
