@extends('layouts/app')

@section('title')
Mis Planes Suscritos
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
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/perfil">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li class="active">
                            <a class="nav-link" aria-selected="false" href="/usuario/cambiar-plan">Cambiar plan</a>
                        </li>
                        @if( auth()->user()->isContador() )
                            <li>
                                <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content">       
                        <div class="tab-pane fade show active" id="" role="tabpanel">                            

                            <h3 class="card-title">Mis Planes Suscritos</h3>                     
                            
                            <a class="btn btn-warning pull-right" href="{{route('plans.show-data')}}"> Comprar otro plan </a>
                            
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Plan No.</th>
                                        <th>Nombre</th>
                                        <th>Purchased On</th>                    
                                        <th>Expiry Date</th>
                                        <th>Companies Registered</th>
                                        <th>Invited Admins</th>
                                        <th>Invited read-only users</th>
                                        <th>Status</th>
                                        <th>Action</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    
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

@endsection
