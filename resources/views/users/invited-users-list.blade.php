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
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       
                        <div class="tab-pane fade show active" id="" role="tabpanel">                            

                            <h3 class="card-title">Usuarios Invitados</h3>                            
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Invited User Type</th>
                                        <th>Invitation Type</th>
                                        <th>Status</th>
                                        <th>Sent/Accepted Date</th>                                                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($users_details))
                                    @foreach ( $users_details as $row )
                                    @php $email = isset($row['email']) ? $row['email']:$row['user']['email'];@endphp
                                    <tr>
                                        <td>{{ isset($row['team']) ? $row['team']['name']:$row['company']['name'] }}</td>
                                        <td>{{ $email }}</td>
                                        <td>{{ !empty(isEmailRegistered($email)) ? 'Registered User':'Guest User'}}</td>
                                        <td>{{ ($_GET['type'] == 'admin') ? 'Invited as admin':'Invited as read-only user'}}</td>
                                        <td>{!! isset($row['email']) ? '<label class="badge badge-warning">Pending</label>':'<label class="badge badge-success">Accepted</label>' !!}</td>
                                        <td>{{ date('jS-M-Y H:i',strtotime($row['created_at'])) }}</td>                                 
                                    </tr>
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

@endsection