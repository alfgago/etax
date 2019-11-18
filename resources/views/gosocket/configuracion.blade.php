@extends('layouts/wizard-layout')

@section('title') 
  Configuración inicial
@endsection

@section('slug', 'wizard')

@section('header-scripts')

@endsection

@section('content') 
<div class="wizard-container">
  <div class="wizard-popup" id="wizard-popup">
  	
   	<div class="titulo-bienvenida">
    	<h2>Configuración inicial de empresa</h2>
    	<p>Para iniciar, complete sus datos a continuación.</p>
    </div>
    	
    
    <div class="form-container">
    
	    <form method="POST" action="update-wizard" class="wizard-form" enctype="multipart/form-data">

	        @csrf
				
			<div class="step-section step1 is-active">

		      	<div class="form-group col-md-12">
				  <label for="commercial_activities">Actividad comercial principal *</label>
				  <select class="form-control checkEmpty select-search-wizard" name="commercial_activities" id="commercial_activities" required>
				      <option value='' selected>-- No seleccionado --</option>
				      @foreach ( $actividades as $actividad )
				          <option value="{{ $actividad['codigo'] }}" >{{ $actividad['codigo'] }} - {{ $actividad['actividad'] }}</option>
				      @endforeach
				  </select>
				</div>
		      	<div class="form-group col-md-12">
				  <h3>
				  Prorrata inicial
				  </h3>
				</div>

				<div class="form-group col-md-6">
				  <label for="first_prorrata_type">Método de cálculo de prorrata operativa inicial</label>
				  <select class="form-control" name="first_prorrata_type" id="first_prorrata_type" onchange="toggleTipoProrrata();" required>
				    <option value="1" selected>Registro manual</option>
				    <option value="2" >Ingreso de totales por código</option>
				    <option value="3" >Ingreso de facturas del 2018</option>
				  </select>
				</div>

				<div class="form-group col-md-6 hidden toggle-types type-1" >
				  <label for="first_prorrata">Digite su prorrata inicial</label>
				  <input type="number" class="form-control" name="first_prorrata" id="first_prorrata" step="0.01" min="1" max="100" value="{{ @$company->first_prorrata ? $company->first_prorrata : 100 }}" required>
				</div>

				<div class="form-group col-md-6 hidden toggle-types type-1">
				  <label for="operative_ratio1">Digite su proporción de ventas al 1%</label>
				  <input type="number" class="form-control" name="operative_ratio1" id="operative_ratio1" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio1 ? $company->operative_ratio1 : 0 }}" required>
				</div>

				<div class="form-group col-md-6 hidden toggle-types type-1">
				  <label for="operative_ratio2">Digite su proporción de ventas al 2%</label>
				  <input type="number" class="form-control" name="operative_ratio2" id="operative_ratio2" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio2 ? $company->operative_ratio2 : 0 }}" required>
				</div>

				<div class="form-group col-md-6 hidden toggle-types type-1">
				  <label for="operative_ratio3">Digite su proporción de ventas al 13%</label>
				  <input type="number" class="form-control" name="operative_ratio3" id="operative_ratio3" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio3 ? $company->operative_ratio3 : 0 }}" required>
				</div>

				<div class="form-group col-md-6 hidden toggle-types type-1">
				  <label for="operative_ratio4">Digite su proporción de ventas al 4%</label>
				  <input type="number" class="form-control" name="operative_ratio4" id="operative_ratio4" step="0.01" min="0" max="100" value="{{ @$company->operative_ratio4 ? $company->operative_ratio4 : 0 }}" required>
				</div>

				<div class="form-group col-md-6">
				  <label for="saldo_favor_2018">Ingrese su saldo a favor acumulado de periodos anteriores</label>
				  <input type="number" class="form-control" name="saldo_favor_2018" id="saldo_favor_2018" step="0.01" value="0" required>
				</div>

				<div class="btn-holder">
				  <button type="submit" id="btn-submit" class="btn btn-primary btn-next" onclick="trackClickEvent( 'TerminarConfigInicial' );">Terminar configuración inicial</button>
				</div>

			</div>
				
						
	
	    </form>
	  </div>  
	  
  </div>

		<a style=""
	  class="btn btn-cerrarsesion" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
        Cerrar sesión
    </a>
    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    
</div>  

@endsection



@section('footer-scripts')

<script>
	
	

    
    function toggleTipoProrrata() {
		var metodo = $("#first_prorrata_type").val();
		$( ".toggle-types" ).hide();
		$( ".type-"+metodo ).show();
	}
    
    $( document ).ready(function() {  
        toggleTipoProrrata();
    });

	
</script>

@endsection
