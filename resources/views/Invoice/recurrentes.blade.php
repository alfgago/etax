@extends('layouts/app')

@section('title') 
  	Facturas recurrentes
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Cliente</th>
              <th>Recurrencia</th>
              <th>Día / Frecuencia</th>
              <th data-priority="3">Total</th>
              <th data-priority="4">Próxima fecha</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @if ( $recurrentes->count() )
              @foreach ( $recurrentes as $data )
                @if ( isset($data->invoice) )
                  <tr>
                    <td>{{ $data->invoice->clientName() }}</td>
                    <td>{{ $data->tipo() }}</td>
                    <td>{{ $data->opciones() }}</td>
                    <td>{{ number_format( $data->invoice->total, 2 ) }}</td>
                    <td>{{date('d/m/Y', strtotime($data->next_send))}}</td>
                    <td>
                      <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Acciones
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">      
                          <a href="/facturas-emitidas/ver-recurrente/{{ $data->id }}" alt="Ver histórico de recurrencia" class="dropdown-item" >
                            <i class="fa fa-clock-o" aria-hidden="true"></i> Ver histórico
                          </a>
                          <a href="/facturas-emitidas/{{ $data->invoice->id }}" alt="Ver factura" class="text-info dropdown-item" >
                            <i class="fa fa-eye" aria-hidden="true"></i> Ver detalle
                          </a>
                          <a href="/facturas-emitidas/editar-factura-recurrente/{{ $data->invoice->id }}" alt="Cambiar factura" class="text-success dropdown-item" >
                            <i class="fa fa-file" aria-hidden="true"></i> Editar factura
                          </a>
                          <form id="delete-form-{{ $data->id }}" class="inline-form" method="POST" action="/facturas-emitidas/eliminar-recurrente/{{ $data->id }}" >
                            @csrf
                            @method('delete')
                            <a type="button" class="text-danger dropdown-item" title="Eliminar recurrente" onclick="confirmDelete({{ $data->id }});">
                              <i class="fa fa-trash" aria-hidden="true"></i> Eliminar recurrente
                            </a>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endif
              @endforeach
            @endif

          </tbody>
        </table>
        
  </div>  
</div>

@endsection

@section('footer-scripts')
<script>
function confirmDelete( id ) {
    var formId = "#delete-form-"+id;
    Swal.fire({
        title: '¿Está seguro que desea eliminar la recurrencia?',
        text: "",
        type: 'warning',
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: 'Sí, deseo eliminarlo'
    }).then((result) => {
        if (result.value) {
            $(formId).submit();
        }
    })

} 
</script>


@endsection