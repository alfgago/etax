@extends('layouts/app')

@section('title')
    Empresas
@endsection

@section('breadcrumb-buttons')
    @if( auth()->user()->isContador() )
        @can('admin')
            <a type="submit" class="btn btn-primary {{$data['class']}}" href="{{$data['url']}}">Registrar otra empresa</a>
        @endcan
    @endif
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
                        <li>
                            <a class="nav-link" aria-selected="false" href="/elegir-plan">Cambiar plan</a>
                        </li>
                        @if( auth()->user()->isContador() )
                            <li>
                                <a class="nav-link active" aria-selected="true" href="/usuario/empresas">Empresas</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">

                            <h3 class="card-title">Contabilidades</h3>

                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Identificación</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>   
                                        <th>Acciones</th>   
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ( $teams->count() )
                                    @foreach($teams as $team)
                                    @php $company_detail = get_company_details($team->company_id);  @endphp
                                    @if($company_detail)
                                    <tr>
                                        <td>{{$company_detail->id_number}}</td>
                                        <td>{{$company_detail->name}}</td>
                                        <td>{{$company_detail->email}}</td>
                                        <td>
                                            @if( auth()->user()->isOwnerOfTeam($team) )
                                            <form id="delete-form-{{ $company_detail->id }}" class="inline-form" method="POST" action="/empresas/{{ $company_detail->id }}" >
                                              @csrf
                                              @method('delete')
                                              <a type="button" class="text-danger mr-2" title="Eliminar empresa" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete({{ $company_detail->id }});">
                                                <i class="fa fa-ban" aria-hidden="true"></i>
                                              </a>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('footer-scripts')

<script>
    
function confirmDelete( id ) {
  var formId = "#delete-form-"+id;
  Swal.fire({
    title: '¿Está seguro que desea desactivar la empresa?',
    text: "Los datos de la empresa serán guardados durante 12 meses. Si desea recuperarlos o transferirlos a otra cuentas, contacte a soporte.",
    type: 'warning',
    showCloseButton: true,
    showCancelButton: true,
    confirmButtonText: 'Sí, quiero desactivarla'
  }).then((result) => {
    if (result.value) {
      $(formId).submit();
    }
  })
  
}
    
</script>


@endsection
