@extends('layouts/app')

@section('title') 
  Crear factura emitida
@endsection

@section('content') 
<form method="POST" action="/facturas-emitidas/validarEnvioExcel" enctype="multipart/form-data" class="toggle-xlsx">
	@csrf
	<div class="row">
		<div class="col-md-12">
		  	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
				<thead>
					<th>Identificador</th>
					<th>Cliente</th>
					<th>Identificación</th>
					<th>Correo</th>
					<th>Monto Total</th>
					<th>Autorizada</th>
					<th></th>
				</thead>
				<tbody>
					<?php $i = 0; ?>
					@foreach ( $facturas as $factura )
						
						<tr>
							<td>{{$factura->consecutivo}}</td>
							<td>{{$factura->nombreReceptor}}</td>
							<td>{{$factura->identificacionReceptor}}</td>
							<td>{{$factura->correoReceptor}}</td>
							<td>{{$factura->montoTotal}}</td>
							<td>
									<input type="text" value="{{$factura->consecutivo}}" id="consecutivo-{{$factura->consecutivo}}" name="facturas[{{$i}}][consecutivo]" class="hidden" />
								<div class="form-check">
									<input type="checkbox" @if($factura->autorizado) checked @endif  name="facturas[{{$i}}][autorizado]" id="autorizado-{{$factura->consecutivo}}" class="form-check-input" />
								</div>
								
							</td>
							<td>
								<a class="text-black" id="ver-mas-{{$factura->consecutivo}}" onclick="detalle_xls('{{$factura->consecutivo}}')" ><i class="fa fa-eye" aria-hidden="true"></i></a>
							</td>
						</tr>
						<tr  id="tr-{{$factura->consecutivo}}" class="div-oculto-detalle hidden">
							<td colspan="13">
								<div id="div-{{$factura->consecutivo}}" ></div>
							</td>
						</tr>
						<tr class="hidden"></tr>
						<?php $i++; ?>
			        @endforeach
				</tbody>
			</table>
	  	  </div>  
	</div>
  	<div class="btn-holder hidden">
       <button id="btn-submit" type="submit" class="btn btn-primary">Guardar factura</button>
    </div>

</form>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Enviar facturas</button>
@endsection 


@section('footer-scripts')
<script>
	function validarEnvio(id){
		var autorizado = $("#"+id).attr("autorizado");
		var consecutivo = $("#"+id).attr("consecutivo");
		if(autorizado == 1){
			$("#autorizado-"+consecutivo).val(0);
			$("#"+id).attr("autorizado",0);
			$("#"+id).html("No enviar");
			$("#"+id).addClass("btn-danger");
			$("#"+id).removeClass("btn-success");
		}else{
			$("#autorizado-"+consecutivo).val(1);
			$("#"+id).attr("autorizado",1);
			$("#"+id).html("Enviar");
			$("#"+id).addClass("btn-success");
			$("#"+id).removeClass("btn-danger");
		}

	}
	function detalle_xls(consecutivo){
		$(".div-mostrado-detalle").toggle();
		$(".div-mostrado-detalle").addClass("div-oculto-detalle");
		if(!$("#tr-"+consecutivo).hasClass("div-mostrado-detalle")){
			$("#tr-"+consecutivo).toggle();
			if(!$("#tr-"+consecutivo).hasClass("factura-detallada")){
				$("#tr-"+consecutivo).addClass("factura-detallada");
				$.ajax({
		           type:'GET',
		           url:'detalleXlsInvoice/'+consecutivo,
		           success:function(data){
		                $("#div-"+consecutivo).html(data);
		           }

		        });
			}
		}
		$(".div-mostrado-detalle").removeClass("div-mostrado-detalle");
		$("#tr-"+consecutivo).addClass("div-mostrado-detalle");
	}
</script>
@endsection
