@extends('layouts/app')

@section('title') 
  Facturas recibidas
@endsection

@section('breadcrumb-buttons')
    <a type="submit" class="btn btn-primary" href="/facturas-recibidas/create">Ingresar factura existente</a>
    <div onclick="abrirPopup('importar-recibidas-popup');" class="btn btn-primary">Importar facturas recibidas</div>
    <a type="submit" class="btn btn-primary" href="/facturas-recibidas/aceptaciones">Aceptación de facturas</a>
    <a href="/facturas-recibidas/autorizaciones" class="btn btn-primary">Autorizar facturas por email</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
        <div class="filters mb-4 pb-4">
            <label>Filtrar documentos</label>
            <div class="periodo-selects">
              <select id="filtro-select" name="filtro" onchange="reloadDataTable();">
                  <option value="99" selected>Todos los documentos</option>
                  <option value="1">Facturas electrónicas</option>
                  <option value="3">Notas de crédito</option>
                  <option value="0">Documentos eliminados</option>
              </select>
            </div>
        </div>
          
        <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Emisor</th>
              <th>Tipo Doc.</th>
              <th data-priority="5">Moneda</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="6">F. Generada</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
  </div>  
</div>

@endsection

@section('footer-scripts')

<script>
  
var datatable;
$(function() {
  datatable = $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/api/bills",
      data: function(d){
          d.filtro = $( '#filtro-select' ).val();
      },
      type: 'GET'
    },
    order: [[ 7, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'document_number' },
      { data: 'provider', name: 'provider.fullname' },
      { data: 'document_type', name: 'document_type' },
      { data: 'currency', name: 'currency', orderable: false, searchable: false },
      { data: 'subtotal', name: 'subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'iva_amount', name: 'iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'total', name: 'total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ) },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});

function reloadDataTable() {
  datatable.ajax.reload();
}

function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la factura',
    text: "Este proceso la eliminará a nivel de cálculo en eTax, sin embargo no hace anulaciones ni revierte aceptaciones ante Hacienda. Usted podrá volver a importar la factura via XML o ingreso manual.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero eliminarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}


function condfirmEnvioAceptacion( id ) {
  var formId = "#envioaceptacion-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea enviar la factura a aceptación?',
    text: "Este proceso la marcará como no aceptada ante Hacienda. Podrá ya sea enviarla nuevamente, o aprobar manualmente si se equivocó al enviarla.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero enviarla a aceptaciones',
    customContainerClass: 'container-success',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}


function confirmAnular( id ) {
  var formId = "#anular-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea anular la factura',
    text: "Este proceso anulará la factura ante Hacienda y enviará una nueva nota de crédito al cliente.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero anularla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}

function confirmRecover( id ) {
  
  var formId = "#recover-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea restaurar la factura?',
    text: "La factura será tomada en cuenta para sus cálculos de IVA nuevamente.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero restaurarla',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}

function validarPopup(obj) {
  
    var link = $(obj).attr("link");
    var titulo = $(obj).attr("titulo");
    $("#titulo_modal_estandar").html(titulo);
    $.ajax({
       type:'GET',
       url:link,
       success:function(data){
          $("#body_modal_estandar").html(data);
       }
  
    });
  
}
  
</script>

@endsection
