@extends('layouts/app')

@section('title') 
  Facturas emitidas
@endsection

@section('breadcrumb-buttons')
    <a class="btn btn-primary" href="/facturas-emitidas/emitir-factura/01">Emitir factura nueva</a>
    <a class="btn btn-primary" href="/facturas-emitidas/create">Ingresar factura existente</a>
    <div onclick="abrirPopup('importar-emitidas-popup');" class="btn btn-primary">Importar facturas emitidas</div>
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
                <option value="4">Tiquete electrónico</option>
                <option value="3">Notas de crédito</option>
                <option value="2">Notas de debito</option>
                <option value="8">Factura de compra</option>
                <option value="9">Factura de exportación</option>
                <option value="0">Documentos eliminados</option>
            </select>
          </div>
      </div>

      <table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th data-priority="3">Actividad</th>
              <th data-priority="4">Tipo Doc.</th>
              <th data-priority="5">Moneda</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="4">F. Generada</th>
              <th data-priority="1">Estado</th>
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
  datatable = $('#invoice-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/api/invoices",
      data: function(d){
          d.filtro = $( '#filtro-select' ).val();
      },
      type: 'GET'
    },
    order: [[ 8, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'document_number' },
      { data: 'client', name: 'client.fullname' },
      { data: 'commercial_activity', name: 'commercial_activity' },
      { data: 'document_type', name: 'document_type' },
      { data: 'moneda', name: 'currency', orderable: false, searchable: false },
      { data: 'subtotal', name: 'subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'iva_amount', name: 'iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'total', name: 'total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'hacienda_status', name: 'hacienda_status' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
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

function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea eliminar la factura' ,
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

function confirmAnular( id ) {  
  var formId = "#anular-form-"+id;

  @if(currentCompanyModel(false)->atv_validation)
    var title = '¿Está seguro que desea anular la factura';
    var texto = "Este proceso anulará la factura ante Hacienda y enviará una nueva nota de crédito al cliente.";
    Swal.fire({
      title: title,
      text: texto,
      type: 'warning',
      showCloseButton: true,
      showCancelButton: true,
      confirmButtonText: 'Sí, quiero anularla'
    }).then((result) => {
      if (result.value) {
        $(formId).submit();
      }
    })
  @else
    var title = 'No puede anular factura';
    var texto = "Debe tener un certificado ATV válido para poder hacer anulaciones y generar nota de crédito.";
    Swal.fire({
      title: title,
      text: texto,
      type: 'warning',
      showCloseButton: true,
      confirmButtonText: 'Ok'
    });
  @endif
  
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

function confirmHideFromTaxes( id ) {
  
  var formId = "#hidefromtaxes-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea ocultar la factura de los cálculos de IVA?',
    text: "La factura no será tomada en cuenta para sus cálculos de IVA.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero ocultarla del cálculo',
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
  
</script>

<style>
  tr.tax-hidden td {
    text-decoration: line-through !important;
  }
</style>

@endsection
