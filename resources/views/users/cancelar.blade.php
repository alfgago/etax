@extends('layouts/app')

@section('title')
    Cancelar subscripción
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
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/perfil">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/cambiar-plan">Cambiar plan</a>
                        </li>
                        @if( auth()->user()->isContador() )
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">
                            <form method="POST" action="/usuario/update-cancelar">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="form-group col-md-12">
                                      <label for="motivo">Motivo de la cancelación del plan</label>
                                      <textarea class="form-control" name="motivo" id="motivo" ></textarea>
                                    </div>
                                    
                                    <button id="btn-submit" type="submit" class="btn btn-danger">Cancelar</button>
                                    
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

