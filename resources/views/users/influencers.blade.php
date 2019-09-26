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
                         <?php 
                        $menu = new App\Menu;
                        $items = $menu->menu('menu_perfil');
                        foreach ($items as $item) { ?>
                            <li>
                                <a class="nav-link @if($item->link == '/usuario/empresas') active @endif" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
                            </li>
                        <?php } ?>
                        @if( auth()->user()->isContador() )
                            <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                            </li>
                        @endif
                        @if( auth()->user()->isInfluencers())
                         <li>
                                <a class="nav-link active" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                        @endif
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
                            			@foreach( $lista_ingresos as $row )
											<tr>
	                            				<td>{{@$row["codigo"]}}</td>
	                            				<td>{{@$row["cantidad"]}}</td>
	                            				<td>{{@$row["total"]}}</td>
	                            				<td>{{@$row["porcentaje"]}}</td>
	                            				<td>{{@$row["ganancia"]}}</td>
	                            			</tr>
                            			@endforeach
											<tr>
	                            				<th></th>
	                            				<th>{{@$saldos["ingresos"]}}</th>
	                            				<th></th>
	                            				<th></th>
	                            				<th>{{@$saldos["monto_ingresos"]}}</th>
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
                            			@foreach( $lista_retiros as $row )
											<tr>
	                            				<td>{{@$row["payment_date"]}}</td>
	                            				<td>{{@$row["amount"]}}</td>
	                            				<td>{{@$row["account"]}}</td>
	                            				<td>{{@$row["proof"]}}</td>
	                            				<td>{{@$row["payment_status"]}}</td>
	                            			</tr>
                            			@endforeach
											<tr>
	                            				<th></th>
	                            				<th>{{@$saldos["monto_retiros"]}}</th>
	                            				<th></th>
	                            				<th></th>
	                            				<th></th>
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
                            				<td>{{@$saldos["monto_retiros"]}}</td>
                            			</tr>
                            			<tr>
                            				<th>Total</th>
                            				<th></th>
                            				<th>{{@$saldos["saldo"]}}</th>
                            			</tr>
                            		</table>
                            		<br>
                            		<h3 class="card-title">Solicite retiro</h3>
									{!! Form::open(['route' => 'Influencers.retiro' ,'class' => 'form']) !!}

									<div class="form-group">
									    {!! Form::label('monto', 'Monto') !!}
									    {!! Form::number('monto', $saldos["saldo"],['min'=>0,'max'=>$saldos["saldo"], 'class' => 'form-control', 'required'=>'required'] ) !!}
									</div>

									<div class="form-group">
									    {!! Form::label('cuenta', 'Cuenta') !!}
									    {!! Form::text('cuenta', null, ['class' => 'form-control', 'required'=>'required']) !!}
									</div>
									<div class="form-group">
									    {!! Form::label('cedula', 'Cedula') !!}
									    {!! Form::text('cedula', null, ['class' => 'form-control', 'required'=>'required']) !!}
									</div>


									@if ($saldos["saldo"] >= 1)
									    {!! Form::submit('Solicitar', ['class' => 'btn btn-info']) !!}
									@else
									    {!! Form::submit('Solicitar', ['class' => 'btn btn-info', 'disabled'=> 'disabled']) !!}
									@endif

									

									{!! Form::close() !!}

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
