@extends('layouts/app')

@section('title') 
  Aceptación manual de facturas 4.3
@endsection

@section('breadcrumb-buttons')
    <div onclick="abrirPopup('importar-aceptacion-popup');" class="btn btn-primary">Importar facturas para aceptación</div>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
        <div class="descripcion mb-4">
          Este proceso <b style="text-decoration: underline;">NO</b> genera la aceptación o rechazo ante Hacienda. Solamente valida la información que debe llevar el mensaje receptor recibido por otros proveedores.
        </div>
          
        <table id="bill-table" class="dataTable table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Emisor</th>
              <th>Comprobante</th>
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
            <tbody>
            @if ( $bills->count() )
              @foreach ( $bills as $data )
                <tr id="row-index-{{ $loop->index }}">
                  <td>{{ @$data->provider->getFullName() }}</td>
                  <td>{{ $data->document_number }}</td>
                  <td>{{ $data->currency }} {{ number_format($data->total) }}</td>
                  <td>{{ number_format($data->accept_total_factura) }}</td>
                  <td>{{ number_format($data->accept_iva_total) }}</td>
                  <td><input style="max-width:90px;" type="number" min="0" step="0.01" class="accept_iva_acreditable-linea" value="{{ number_format($data->accept_iva_acreditable) }}" onkeyup="setTo('{{ $loop->index }}', 'accept_iva_acreditable', this.value)" /></td>
                  <td><input style="max-width:90px;" type="number" min="0" step="0.01" class="accept_iva_gasto-linea" value="{{ number_format($data->accept_iva_gasto) }}" onkeyup="setTo('{{ $loop->index }}', 'accept_iva_gasto', this.value)" /></td>
                  <td>{{ $data->generatedDate()->format('d/m/Y') }}</td>
                  <td>
                    <form id="accept-form-{{ $data->id }}" class="inline-form por-etax" method="POST" action="/facturas-recibidas/confirmar-aceptacion-otros/{{ $data->id }}" >
                      @csrf
                      @method('patch')
                      <input type="hidden" name="respuesta" value="1">
                      <input type="hidden" required name="accept_iva_acreditable" class="accept_iva_acreditable" value="{{ $data->accept_iva_acreditable }}">
                      <input type="hidden" required name="accept_iva_gasto" class="accept_iva_gasto" value="{{ $data->accept_iva_gasto }}">
                      <a href="#" title="Confirmar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAccept({{ $data->id }});">
                        Confirmar
                      </a>
                    </form>
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
          </tbody>
        </table>
        
        {{ $bills->links() }}
  </div>  
</div>


@endsection

@section('footer-scripts')

<script>
  
  function confirmAccept( id ) {
    
    var formId = "#accept-form-"+id;
    Swal.fire({
      title: '¿Está seguro que desea aceptar la factura?',
      text: "Al aceptarla, la factura será tomada en cuenta para el cálculo en eTax.",
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
  
  function setTo( rowIndex, field, value) {
    $('#row-index-'+rowIndex+' .'+field).val( value );
  }
  
</script>

@endsection
