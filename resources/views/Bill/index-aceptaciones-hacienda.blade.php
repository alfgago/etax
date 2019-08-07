@extends('layouts/app')

@section('title') 
  Aceptación de facturas ante Hacienda
@endsection

@section('breadcrumb-buttons')
    <div onclick="abrirPopup('importar-aceptacion-popup');" class="btn btn-primary hidden">Importar facturas para aceptación</div>
    <a href="/facturas-recibidas/aceptaciones-otros" class="btn btn-primary">Aceptación manual de facturas</a>
    <a href="/facturas-recibidas/autorizaciones" class="btn btn-primary">Autorizar facturas por email</a>
    <a href="/facturas-recibidas/validaciones" class="btn btn-primary">Validar facturas</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
        <div class="descripcion mb-2">
          Este proceso genera la aceptación o rechazo ante Hacienda.
        </div>
        
        @if( currentCompanyModel()->use_invoicing )
        <h2 class="mt-4 mb-4" style="color: red;">Asegúrese de tener la prorrata y proporcionalidad correctas antes de aceptar su primera factura en 4.3</h2>
        @else
        <h2 class="mt-4 mb-4" style="color: red;">
          Usted no tiene facturación con eTax habilitada, por lo que esta pantalla únicamente incluirá o no las facturas en eTax para cálculo, y <b><u>no</u></b> realizará aceptaciones con Hacienda.
        </h2>
        @endif
        <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Comprobante</th>
              <th>Emisor</th>
              <th>Total en <br>factura</th>
              <th>Total en <br>aceptación (₡)</th>
              <th>IVA <br>Total (₡)</th>
              <th>IVA <br>Acreditable (₡)</th>
              <th>IVA <br>Gasto (₡)</th>
              <th>F. Generada</th>
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

$(function() {
  $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "/api/billsAccepts",
    columns: [
      { data: 'document_number', name: 'document_number' },
      { data: 'provider', name: 'provider.id' },
      { data: 'total', name: 'total', class: "text-right" },
      { data: 'accept_total_factura', class: "text-right", name: 'accept_total_factura', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_total', class: "text-right", name: 'accept_iva_total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_acreditable', class: "text-right", name: 'accept_iva_acreditable', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'accept_iva_gasto', class: "text-right", name: 'accept_iva_gasto', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), orderable: false, searchable: false },
      { data: 'generated_date', name: 'generated_date' },
      { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ],
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
  
});

function confirmAccept( id ) {
  var formId = "#accept-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea aceptar la factura?',
    text: "Al aceptarla, se enviará el mensaje de aceptación a Hacienda con los datos ingresados.",
    type: 'success',
    customContainerClass: 'container-success',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero aceptarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
  
function confirmDecline( id ) {
  var formId = "#decline-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea rechazar la factura?',
    text: "Al rechazarla, se enviará el mensaje de rechazo a Hacienda.",
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
