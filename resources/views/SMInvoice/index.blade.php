@extends('layouts/app')

@section('title') 
  Facturación masiva Seguros del Magisterio
@endsection

@section('breadcrumb-buttons')
  <a class="btn btn-primary" style="color: #ffffff;" onclick="abrirPopup(&quot;importar-sm-popup&quot;);">Importar Excel SM</a>
  @include( 'SMInvoice.import' )
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      <form class="filters mb-4 pb-4" id="send-form"  method="POST" action="">
        @csrf
        <div class="div-filtro">
          <label>Archivo subido</label>
          <div class="periodo-selects">
            <select class="form-control" id="filtro-batch" name="batch" onchange="reloadDataTable();">
              <option value="0" selected>Seleccione un archivo</option>
              @foreach($batches as $batch)
                <option value="{{ $batch->batch }}" >{{ $batch->batch }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </form>
      
      <table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="1">#</th>
              <th data-priority="1">Tipo</th>
              <th data-priority="1">Consecutivo</th>
              <th data-priority="1">num_factura</th>
              <th data-priority="3">num_objeto</th>
              <th data-priority="5">fecha_emision</th>
              <th data-priority="5">fecha_pago</th>
              <th data-priority="4">condicion</th>
              <th data-priority="4">medio_pago</th>
              <th data-priority="5">moneda</th>
              <th data-priority="5">tipo_id</th>
              <th data-priority="1">doc_identificacion</th>
              <th data-priority="1">nombre_tomador</th>
              <th data-priority="5">telefono_habitacion</th>
              <th data-priority="5">telefono_celular</th>
              <th data-priority="5">correo</th>
              <th data-priority="5">provincia</th>
              <th data-priority="5">canton</th>
              <th data-priority="5">distrito</th>
              <th data-priority="5">codigo_postal</th>
              <th data-priority="5">des_direccion</th>
              <th data-priority="5">cantidad</th>
              <th data-priority="3">precio_unitario</th>
              <th data-priority="3">impuesto</th>
              <th data-priority="1">total</th>
              <th data-priority="1">descripcion</th>
              <th data-priority="5">actividad_comercial</th>
              <th data-priority="5">codigo_etax</th>
              <th data-priority="5">categoria</th>
              <th data-priority="5">refer_factura</th>
              <th data-priority="1">Mes</th>
              <th data-priority="2">Fecha subida</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
      
      
      
      <div style="text-align: center;">
        <a class="btn btn-primary" style="color: #ffffff; font-size: 1.25rem; margin-top: 1.5rem;" onclick="confirmEnviarSM();">Confirmar y enviar archivo</a>
      </div>
      <div style="text-align: center;">
        <a class="btn btn-primary" style="color: #15408E; background: none; font-size: 1.25rem; margin-top: 1.5rem;" onclick="revisarNotasCredito();">Verificar Notas</a>
      </div>
  </div>  
</div>

@endsection

@section('footer-scripts')

<script>
  
var datatable;
$(function() {
  datatable = $('#invoice-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/api/sm",
      data: function(d){
          d.batch = $( '#filtro-batch' ).val();
      },
      type: 'GET'
    },
    order: [[ 8, 'desc' ]],
    columns: [
      { data: 'linea_excel', name: 'linea_excel' },
      { data: 'document_type', name: 'document_type' },
      { data: 'clave_col', name: 'document_key' },
      { data: 'num_factura', name: 'num_factura' },
      { data: 'num_objeto', name: 'num_objeto' },
      { data: 'fecha_emision', name: 'fecha_emision' },
      { data: 'fecha_pago', name: 'fecha_pago' },
      { data: 'condicion', name: 'condicion' },
      { data: 'medio_pago', name: 'medio_pago' },
      { data: 'moneda', name: 'moneda' },
      { data: 'tipo_id', name: 'tipo_id' },
      { data: 'doc_identificacion', name: 'doc_identificacion' },
      { data: 'nombre_tomador', name: 'nombre_tomador' },
      { data: 'telefono_habitacion', name: 'telefono_habitacion' },
      { data: 'telefono_celular', name: 'telefono_celular' },
      { data: 'correo', name: 'correo' },
      { data: 'provincia', name: 'provincia' },
      { data: 'canton', name: 'canton' },
      { data: 'distrito', name: 'distrito' },
      { data: 'codigo_postal', name: 'codigo_postal' },
      { data: 'des_direccion', name: 'des_direccion' },
      { data: 'cantidad', name: 'cantidad' },
      { data: 'precio_unitario', name: 'precio_unitario', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'impuesto', name: 'impuesto', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'total', name: 'total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'descripcion', name: 'descripcion' },
      { data: 'actividad_comercial', name: 'actividad_comercial' },
      { data: 'codigo_etax', name: 'codigo_etax' },
      { data: 'categoria', name: 'categoria' },
      { data: 'refer_factura', name: 'refer_factura' },
      { data: 'mes_col', name: 'mes_col' },
      { data: 'created_at', name: 'created_at' },
      
    ],
    createdRow: function (row, data, index) {
      if(data.hide_from_taxes){
        $(row).addClass("tax-hidden");
      }
    },
    language: {
      url: "/lang/datatables-es_ES.json",
    }
  });
});

function reloadDataTable() {
  datatable.ajax.reload();
}

function confirmEnviarSM() {
  var formId = "#send-form";
  jQuery(formId).attr('action', '/sm/confirmar-envio');
  Swal.fire({
    title: '¿Está seguro que desea enviar las facturas del archivo?',
    text: "Las facturas serán enviadas a Hacienda. El proceso puede tardar unas horas en terminar dependiendo de la cantidad de facturas. Solamente se envian las que no estan duplicadas y aun no tienen clave.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero enviar todas las facturas del archivo seleccionado',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}



function revisarNotasCredito() {
  var formId = "#send-form";
  jQuery(formId).attr('action', '/sm/revisar-nc');
  Swal.fire({
    title: '¿Está seguro que desea revisar las notas?',
    text: "Este proceso va a verificar que todas las Notas de crédito estén asociadas correctamente con una factura. De no estarlo, estas no se van a enviar.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero verificar las notas',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
</script>

<style>
  .div-filtro{
    float: left;
    margin: 5px;
  }
  .filters{
    position: relative;
    margin-bottom: 3.5rem !important;
  }
</style>

@endsection
