@extends('layouts/app')

@section('title')
Billetera
@endsection

@section('breadcrumb-buttons')
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        
        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/perfil">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li class="active">
                            <a class="nav-link" aria-selected="false" href="/cambiar-plan">Cambiar plan</a>
                        </li>
                        @if( auth()->user()->isContador() )
                            <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                            </li>
                        @endif
                         <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       
                        <div class="tab-pane fade show active" id="" role="tabpanel">
                            <div class="row">
                            	<div class="col-8 col-xs-12">
                            		<h3 class="card-title">Ingresos</h3> 
                            		<table class="table table-striped">
                            			<tr>
                            				<th>Codigo</th>
                            				<th>Cantidad</th>
                            				<th>Total</th>
                            				<th>Porcentaje</th>
                            				<th>Ganancia</th>
                            			</tr>
                            			@foreach( $lista_ingresos as $row ):
											<tr>
	                            				<td>{{@$row["codigo"]}}</td>
	                            				<td>{{@$row["cantidad"]}}</td>
	                            				<td>{{@$row["total"]}}</td>
	                            				<td>{{@$row["porcentaje"]}}</td>
	                            				<td>{{@$row["ganancia"]}}</td>
	                            			</tr>
                            			@endforeach
											<tr>
	                            				<td></td>
	                            				<td>{{@$saldos["ingresos"]}}</td>
	                            				<td></td>
	                            				<td></td>
	                            				<td>{{@$saldos["monto_ingresos"]}}</td>
	                            			</tr>
                            		</table>
                            		<br>
                            		<h3 class="card-title">Retiros</h3> 
                            		<table class="table table-striped">
                            			<tr>
                            				<th>Fecha</th>
                            				<th>Monto</th>
                            				<th>Cuenta</th>
                            				<th>Transferencia</th>
                            				<th>Estado</th>
                            			</tr>
                            			@foreach( $lista_retiros as $row ):
											<tr>
	                            				<td>{{@$row["payment_status"]}}</td>
	                            				<td>{{@$row["amount"]}}</td>
	                            				<td>{{@$row["account"]}}</td>
	                            				<td>{{@$row["proof"]}}</td>
	                            				<td>{{@$row["payment_status"]}}</td>
	                            			</tr>
                            			@endforeach
											<tr>
	                            				<td></td>
	                            				<td>{{@$saldos["total_retiros"]}}</td>
	                            				<td></td>
	                            				<td></td>
	                            				<td></td>
	                            			</tr>
                            		</table>
                            	</div>
                            	<div class="col-4 col-xs-12">
                            		<h3 class="card-title">Saldos</h3> 
                            		<table class="table">
                            			<tr>
                            				<td>Ingresos</td>
                            				<td>{{@$saldos["ingresos"]}}</td>
                            				<td>{{@$saldos["monto_ingresos"]}}</td>
                            			</tr>
                            			<tr>
                            				<td>Retiros</td>
                            				<td>{{@$saldos["retiros"]}}</td>
                            				<td>{{@$saldos["total_retiros"]}}</td>
                            			</tr>
                            			<tr>
                            				<th>Total</th>
                            				<th></th>
                            				<th>{{@$saldos["saldo"]}}</th>
                            			</tr>
                            		</table>
                            		<br>
                            		<h3 class="card-title">Solicite retiro</h3>
                            		<form id="retiro_dinero" action="{{ url('usuario/add-retiro') }}" method="post">
                            			<div class="form-group">
                            				<label>Monto</label>
                            				<input type="number" id="monto_retiro" class="form-control monto_retiro">
                            			</div>
                            			<div class="form-group">
                            				<label>Cuenta</label>
                            				<input type="text" id="cuenta_retiro" class="form-control cuenta_retiro">
                            			</div>
                            			<div class="form-group">
                            				<label>Cedula</label>
                            				<input type="text" id="cedula_retiro" class="form-control cedula_retiro">
                            			</div>
  										<button type="submit" class="btn btn-primary">Solicitar</button>
                            		</form> 
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('footer-scripts')

@endsection
